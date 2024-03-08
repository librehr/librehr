<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaskPriorityEnum: string implements HasLabel, HasColor
{
    case High = '1';
    case Medium = '2';
    case Low = '3';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::High => Color::Red,
            self::Medium => Color::Yellow,
            self::Low => Color::Gray,
        };
    }
}
