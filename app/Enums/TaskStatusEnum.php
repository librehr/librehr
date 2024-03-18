<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TaskStatusEnum: string implements HasLabel, HasColor
{
    case Open = 'open';
    case Pending = 'pending';
    case InReview = 'in review';
    case InProgress = 'in progress';
    case Blocked = 'blocked';
    case BlockedInternal = 'blocked internal';
    case Completed = 'completed';
    case Closed = 'closed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Pending => 'Pending',
            self::InReview => 'In Review',
            self::InProgress => 'In Progress',
            self::Blocked => 'Blocked',
            self::BlockedInternal => 'Blocked Internal',
            self::Completed => 'Completed',
            self::Closed => 'Closed',
        };
    }

    public function getColor(): array
    {
        return match ($this) {
            self::Open => Color::Gray,
            self::Pending => Color::Yellow,
            self::InReview => Color::Orange,
            self::InProgress => Color::Cyan,
            self::Blocked => Color::Red,
            self::BlockedInternal => Color::Purple,
            self::Completed => Color::Green,
            self::Closed => Color::Blue,
        };
    }
}
