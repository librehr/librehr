<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Pages\Attendances;
use App\Services\Notifications\NotificationsResources;

class AttendanceRevision extends NotificationsResources
{
    //TODO: fix date to show only month, fix link to go the selected month / year
    public function getTitle(): string
    {
        return 'Revision requested on '. data_get($this->data, 'date') . ' attendances.';
    }

    public function getDescription(): string
    {
        return 'Check out the details on your personal attendances.';
    }

    public function getUrl(): string
    {
        return Attendances::getNavigationUrl();
    }
}
