<?php

namespace App\Services\Notifications\Resources;

use App\Enums\TaskPriorityEnum;
use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Services\Notifications\NotificationsResources;

class TaskPriorityChanged extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'Priority has changed by '. data_get($this->data, 'priority_changed_by.name') . ' in ' .
            data_get($this->data, 'name') . '.';
    }

    public function getDescription(): string
    {
        return data_get($this->data, 'priority_changed_by.name') . ' has changed the priority to ' .
            data_get($this->data, 'priority')->getLabel();
    }

    public function getUrl(): string
    {
        return ViewTask::getNavigationUrl([data_get($this->data, 'id')]);
    }
}
