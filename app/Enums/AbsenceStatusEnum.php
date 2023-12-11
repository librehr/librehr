<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AbsenceStatusEnum: string implements HasLabel
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Denied = 'denied';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Denied => 'Denied'
        };
    }
}
