<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterSave(): void
    {
        // images の内容は Filament のコンポーネントにより異なる（文字列/配列の可能性がある）
        $images = $this->data['images'] ?? [];

        // 既存の画像をどう扱うかの方針次第ですが、
        // ここでは追加分だけを新規登録する想定です。必要なら既存削除ロジックを追加してください。
        $sortOrder = 1;
        foreach ($images as $path) {
            if (is_array($path) && isset($path['path'])) {
                $url = $path['path'];
            } elseif (is_string($path)) {
                $url = $path;
            } else {
                // 想定外の形式はスキップ
                continue;
            }

            ProductImage::create([
                'product_id' => $this->record->id,
                'url'        => $url,
                'sort_order' => $sortOrder,
            ]);

            $sortOrder++;
        }
    }
}
