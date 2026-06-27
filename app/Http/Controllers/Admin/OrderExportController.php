<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

class OrderExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $fileName = 'orders_' . now()->format('Ymd_His') . '.csv';

        $startDate  = $request->query('start_date');
        $endDate    = $request->query('end_date');
        $productName = $request->query('product_name');

        $query = Order::with(['items.product'])->latest();

        // ✅ 日付フィルター
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // ✅ 商品名フィルター
        if ($productName) {
            $query->whereHas('items.product', function ($q) use ($productName) {
                $q->where('name', 'like', "%{$productName}%");
            });
        }

        $orders = $query->get();

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // ✅ 文字化け防止
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '商品名',
                '個数',
                '購入日時',
                '氏名',
                '住所',
                '備考欄',
                '配送方法',
            ]);

            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    fputcsv($handle, [
                        $item->product->name ?? '',
                        $item->quantity,
                        $order->created_at->format('Y-m-d H:i'),
                        $order->customer_name,
                        $order->shipping_address,
                        $order->note_to_talent ?? '',
                        $order->delivery_method,
                    ]);
                }
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
