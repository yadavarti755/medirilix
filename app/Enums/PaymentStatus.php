<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case INITIATED = 'INITIATED';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'PENDING',
            self::INITIATED => 'INITIATED',
            self::COMPLETED => 'COMPLETED',
            self::FAILED => 'FAILED',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-default',
            self::INITIATED => 'bg-primary',
            self::COMPLETED => 'bg-success',
            self::FAILED => 'bg-danger',
        };
    }
}
