<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;          // orders: status/payment_method/shipping_method/total/subtotal/shipping_fee/note/address_json...
use App\Models\Shipment;       // shipments: order_id/carrier/tracking_number/shipped_at
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// ※ メール用 Mailable（用意してください）
use App\Mail\ShipmentMail;     // 引数: ($order, ['carrier'=>..,'tracking_number'=>..])
use App\Mail\RefundedMail;     // 引数: ($order)

class OrderController extends Controller
{
    /**
     * 注文一覧（フィルタ：月/ステータス/決済/配送、Excel出力リンク付き）
     */
    public function index(Request $request)
    {
        $q = Order::query()->with(['items.product', 'customer'])->latest();

        // 月フィルタ（YYYY-MM）
        if ($month = $request->string('month')->toString()) {
            [$y, $m] = explode('-', $month . '-');
            $start = Carbon::createSafe((int)$y, (int)$m, 1)->startOfMonth();
            $end   = (clone $start)->endOfMonth();
            $q->whereBetween('created_at', [$start, $end]);
        }

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }
        if ($pm = $request->string('payment_method')->toString()) {
            $q->where('payment_method', $pm);
        }
        if ($sm = $request->string('shipping_method')->toString()) {
            $q->where('shipping_method', $sm);
        }

        $orders = $q->paginate(30)->withQueryString();

        // 今月サマリ（売上/入金予定）
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();
        $monthly = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('SUM(total_amount) as total, SUM(subtotal) as subtotal, SUM(shipping_fee) as shipping')
            ->first();

        $payoutExpected = $this->calcPayoutExpected(); // カード未振込 + 振込待ち等の概算

        return view('admin.orders.index', compact('orders', 'monthly', 'payoutExpected'));
    }

    /**
     * Excel出力（要: maatwebsite/excel）
     * ルート: admin.orders.export.excel
     */
    public function exportExcel(Request $request)
    {
        // 直書きエクスポート（簡易版）：必要なら専用Exportクラスに置換
        $q = Order::with(['items', 'customer'])->latest();

        if ($month = $request->string('month')->toString()) {
            [$y, $m] = explode('-', $month . '-');
            $start = Carbon::createSafe((int)$y, (int)$m, 1)->startOfMonth();
            $end   = (clone $start)->endOfMonth();
            $q->whereBetween('created_at', [$start, $end]);
        }

        $rows = $q->get()->map(function ($o) {
            return [
                '商品名'   => $o->items->pluck('name')->join(', '),
                '購入日時' => $o->created_at->format('Y/m/d H:i'),
                '名前'     => $o->customer->name ?? $o->customer_name ?? '',
                '住所'     => data_get($o->address_json, 'full', ''),
                '備考欄'   => (string) $o->note,
                '個数'     => (int) $o->items->sum('quantity'),
                '発送種別' => $o->shipping_method,
            ];
        });

        // CSVで返す（Excelで開ける）
        $filename = 'orders_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=Shift_JIS',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $content = $this->toCsv($rows, encoding: 'SJIS-win');
        return response($content, 200, $headers);
    }

    /**
     * 注文詳細
     */
    public function show(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'customer', 'shipment']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * 入金確認 → shipping_pending へ
     * 口座振込の入金確認、現場払いの事後確定などで使用
     */
    public function confirmPayment(Order $order)
    {
        $this->authorizeUpdate($order);

        $order->update([
            'status' => 'shipping_pending',
            'paid_at' => now(),
        ]);

        return back()->with('success', '入金確認しました（発送待ちに変更）');
    }

    /**
     * 発送メール送信 + ステータス shipped
     * Request: tracking_number, carrier('sagawa'等)
     */
    public function ship(Request $request, Order $order)
    {
        $this->authorizeUpdate($order);

        $data = $request->validate([
            'carrier'          => ['required', 'string', Rule::in(['sagawa'])],
            'tracking_number'  => ['required', 'string', 'max:100'],
        ]);

        DB::transaction(function () use ($order, $data) {
            // 出荷レコード
            $shipment = Shipment::create([
                'order_id'        => $order->id,
                'carrier'         => $data['carrier'],
                'tracking_number' => $data['tracking_number'],
                'shipped_at'      => now(),
            ]);

            // ステータス
            $order->update(['status' => 'shipped']);

            // メール送信（テンプレ変数）
            try {
                Mail::to($order->customer_email)->queue(new ShipmentMail($order, $data));
            } catch (\Throwable $e) {
                // ログのみ（メール失敗しても出荷処理は継続）
                report($e);
            }
        });

        return back()->with('success', '発送メールを送信し、ステータスを発送済みにしました');
    }

    /**
     * 返金 → refunded + 返金メール送信
     * （オンライン返金は外部API処理をここで呼び出す）
     */
    public function refund(Order $order)
    {
        $this->authorizeUpdate($order);

        DB::transaction(function () use ($order) {
            // TODO: 必要なら Stripe Refund API 等
            $order->update([
                'status'     => 'refunded',
                'refunded_at'=> now(),
            ]);

            try {
                Mail::to($order->customer_email)->queue(new RefundedMail($order));
            } catch (\Throwable $e) {
                report($e);
            }
        });

        return back()->with('success', '返金処理を完了し、返金メールを送信しました');
    }

    /* ---------------------------- Helpers ---------------------------- */

    private function authorizeUpdate(Order $order): void
    {
        // 必要なら Policy で権限管理。今はダミー
        // $this->authorize('update', $order);
    }

    private function calcPayoutExpected(): int
    {
        // ざっくり例：
        // - カード決済のうち入金前 (status in ['paid','shipping_pending','shipped'])
        // - 銀行振込で入金未確認 (status='pending')
        $cardNotPaidOut = Order::where('payment_method', 'card')
            ->whereIn('status', ['paid', 'shipping_pending', 'shipped'])
            ->sum('total_amount');

        $bankWaiting = Order::where('payment_method', 'bank_transfer')
            ->where('status', 'pending')
            ->sum('total_amount');

        return (int) ($cardNotPaidOut + $bankWaiting);
    }

    /**
     * 配列をCSV文字列に（SJIS対応）
     */
    private function toCsv($rows, string $encoding = 'SJIS-win'): string
    {
        $out = fopen('php://temp', 'r+');
        if ($rows->isNotEmpty()) {
            // ヘッダ
            fputcsv($out, array_keys($rows->first()));
            foreach ($rows as $row) {
                fputcsv($out, array_values($row));
            }
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return mb_convert_encoding($csv, $encoding, 'UTF-8');
    }
}
