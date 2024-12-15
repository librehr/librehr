<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AbsenceStatusEnum: string implements HasLabel, HasColor
{
    case Pending = 'pending';
    case Allowed = 'allowed';
    case Denied = 'denied';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Allowed => 'Allowed',
            self::Denied => 'Denied',
            self::Cancelled => 'Cancelled'
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::Pending => Color::Yellow,
            self::Allowed => Color::Green,
            self::Denied => Color::Red,
            self::Cancelled => Color::Orange
        };
    }
}
