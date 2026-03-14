<?php

namespace App\Enums;

enum StockAvailability: int
{
    case OUT_OF_STOCK = 0;
    case AVAILABLE = 1;

    public function label(): string
    {
        return match ($this) {
            self::OUT_OF_STOCK => 'Out of stock',
            self::AVAILABLE => 'Available',
        };
    }
}
