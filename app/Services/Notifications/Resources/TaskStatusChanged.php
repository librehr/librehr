<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Resources\TaskResource\Pages\ViewTask;
use App\Services\Notifications\NotificationsResources;

class TaskStatusChanged extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'Status has changed by '. data_get($this->data, 'status_changed_by.name') . ' in ' .
            data_get($this->data, 'name') . '.';
    }

    public function getDescription(): string
    {
        return data_get($this->data, 'status_changed_by.name') . ' has changed the status to ' .
            data_get($this->data, 'status')->getLabel();
    }

    public function getUrl(): string
    {
        return ViewTask::getNavigationUrl([data_get($this->data, 'id')]);
    }
}
