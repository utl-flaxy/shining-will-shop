<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SalesPage extends Page
{
    // ✅ 管理パネル指定
    protected static ?string $panel = 'admin_shining';

    // ✅ ナビゲーション設定
    protected static ?string $navigationIcon = 'heroicon-o-currency-yen';
    protected static ?string $navigationLabel = '売上一覧';
    protected static ?string $title = '売上一覧';

    // ✅ 表示ビュー
    protected static string $view = 'filament.pages.sales-page';

    // ✅ 売上データ（今は空配列）
    public array $sales = [];

    public function mount(): void
    {
        /**
         * ✅ 今は Square 未連携なので空データ
         * ✅ 後から Square API に差し替えるだけでOKな構成
         */
        $this->sales = [];
    }
}
