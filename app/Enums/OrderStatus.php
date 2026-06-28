<?php

namespace App\Enums;

enum OrderStatus: string
{
    /**
     * 注文受付
     */
    case Pending = 'pending';

    /**
     * 発送準備中
     */
    case Preparing = 'preparing';

    /**
     * 発送済み
     */
    case Shipped = 'shipped';

    /**
     * 配送完了
     */
    case Completed = 'completed';

    /**
     * キャンセル
     */
    case Cancelled = 'cancelled';

    /**
     * 日本語表示
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending   => '受付',
            self::Preparing => '発送準備中',
            self::Shipped   => '発送済み',
            self::Completed => '配送完了',
            self::Cancelled => 'キャンセル',
        };
    }

    /**
     * Filament バッジ色
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending   => 'warning',
            self::Preparing => 'primary',
            self::Shipped   => 'info',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Select 用
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }
}
