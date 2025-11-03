<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportSalesController extends Controller
{
    public function export(Request $request, StripeService $stripe)
    {
        $start = $request->input('start_date');
        $end = $request->input('end_date');
        $product = $request->input('product_name');
        $buyer = $request->input('buyer_name');

        // Stripeから支払い履歴取得
        $sales = $stripe->getRecentSales(100);

        // 条件フィルタ
        $filtered = collect($sales->data)->filter(function ($sale) use ($start, $end, $product, $buyer) {
            $match = true;

            if ($start && $sale->created < strtotime($start)) $match = false;
            if ($end && $sale->created > strtotime($end . ' 23:59:59')) $match = false;
            if ($product && stripos($sale->metadata->product_name ?? '', $product) === false) $match = false;
            if ($buyer && stripos($sale->metadata->user_name ?? '', $buyer) === false) $match = false;

            return $match;
        });

        // Excel作成
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['商品名', '個数', '購入者名', '購入日', '金額（円）', 'ステータス'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($filtered as $sale) {
            $sheet->setCellValue('A'.$row, $sale->metadata->product_name ?? '不明');
            $sheet->setCellValue('B'.$row, $sale->metadata->quantity ?? 1);
            $sheet->setCellValue('C'.$row, $sale->metadata->user_name ?? '未登録');
            $sheet->setCellValue('D'.$row, date('Y-m-d H:i', $sale->created));
            $sheet->setCellValue('E'.$row, $sale->amount / 100);
            $sheet->setCellValue('F'.$row, $sale->status);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'sales_filtered_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
