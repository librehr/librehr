<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Services\Notifications\NotificationsResources;

class TaskUnassigned extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'Task unassigned by '. data_get($this->data, 'user.name') . ': ' . data_get($this->data, 'contratable.name') . '.';
    }

    public function getDescription(): string
    {
        return  data_get($this->data, 'user.name') . ' has unassigned this task to you.';
    }

    public function getUrl(): string
    {
        return ViewTask::getNavigationUrl([data_get($this->data, 'contratable.id')]);
    }
}
