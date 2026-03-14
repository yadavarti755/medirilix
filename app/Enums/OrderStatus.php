<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case PLACED = 'PLACED';
    case PROCESSING = 'PROCESSING';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELLED';
    case FAILED = 'FAILED';
    case RETURN_REQUESTED = 'RETURN_REQUESTED';
    case RETURN_APPROVED = 'RETURN_APPROVED';
    case RETURN_REJECTED = 'RETURN_REJECTED';
    case RETURN_CANCELLED = 'RETURN_CANCELLED';
    case REFUND_INITIATED = 'REFUND_INITIATED';
    case REFUND_COMPLETED = 'REFUND_COMPLETED';
    case REFUND_FAILED = 'REFUND_FAILED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'PENDING',
            self::PLACED => 'PLACED',
            self::PROCESSING => 'PROCESSING',
            self::SHIPPED => 'SHIPPED',
            self::DELIVERED => 'DELIVERED',
            self::CANCELLED => 'CANCELLED',
            self::FAILED => 'FAILED',
            self::RETURN_REQUESTED => 'RETURN REQUESTED',
            self::RETURN_APPROVED => 'RETURN APPROVED',
            self::RETURN_REJECTED => 'RETURN REJECTED',
            self::RETURN_CANCELLED => 'RETURN CANCELLED',
            self::REFUND_INITIATED => 'REFUND INITIATED',
            self::REFUND_COMPLETED => 'REFUND COMPLETED',
            self::REFUND_FAILED => 'REFUND FAILED',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-primary',
            self::PLACED => 'bg-success',
            self::PROCESSING => 'bg-warning',
            self::SHIPPED => 'bg-primary', // Was generic in constants, assuming primary or info
            self::DELIVERED => 'bg-success', // check this
            self::CANCELLED => 'bg-danger',
            self::FAILED => 'bg-danger',
            self::RETURN_REQUESTED => 'bg-primary',
            self::RETURN_APPROVED => 'bg-success',
            self::RETURN_REJECTED => 'bg-danger',
            self::RETURN_CANCELLED => 'bg-danger',
            self::REFUND_INITIATED => 'bg-primary',
            self::REFUND_COMPLETED => 'bg-success',
            self::REFUND_FAILED => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
