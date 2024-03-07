<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Pages\TimeOff;
use App\Services\Notifications\NotificationsResources;

class TimeOffDenied extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'Your time-off request for days has been denied.';
    }

    public function getDescription(): string
    {
        return 'Check out the details on your time-off section.';
    }

    public function getUrl(): string
    {
        return TimeOff::getNavigationUrl();
    }
}
