<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductImage;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        // Filament のアップロードコンポーネントが返す images の形状に依存するため、
        // 安全に扱う：null 対策と数値インデックスを自前で付ける
        $images = $this->data['images'] ?? [];

        $sortOrder = 1;
        foreach ($images as $path) {
            // $path が配列（['path' => '…']）の場合にも対応する柔軟な処理
            if (is_array($path) && isset($path['path'])) {
                $url = $path['path'];
            } elseif (is_string($path)) {
                $url = $path;
            } else {
                // 不正な要素は飛ばす
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
