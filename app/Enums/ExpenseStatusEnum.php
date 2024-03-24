<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExpenseStatusEnum: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Cancelled = 'cancelled';
    case Denied = 'denied';
    case Paid = 'paid';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Denied => 'Denied',
            self::Paid => 'Paid',
            self::Cancelled => 'Cancelled',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::Pending => Color::Gray,
            self::Accepted => Color::Blue,
            self::Denied => Color::Red,
            self::Paid => Color::Green,
            self::Cancelled => Color::Orange,
        };
    }
}
