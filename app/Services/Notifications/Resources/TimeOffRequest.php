<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Pages\Requests;
use App\Filament\Pages\TimeOff;
use App\Services\Notifications\NotificationsResources;

class TimeOffRequest extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'A new time-off request is pending your validation';
    }

    public function getDescription(): string
    {
        return 'Please go to your inbox to validate/deny the request by ' .
            data_get($this->data, 'contract.user.name');
    }

    public function getUrl(): string
    {
        return Requests::getNavigationUrl();
    }
}
