<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'text-bg-secondary',
            self::Processing => 'text-bg-primary',
            self::Shipped => 'text-bg-info',
            self::Completed => 'text-bg-success',
            self::Cancelled => 'text-bg-danger',
        };
    }
}
