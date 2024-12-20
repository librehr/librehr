<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Resources\TaskResource\Pages\ViewTask;
use App\Services\Notifications\NotificationsResources;

class TaskActivityAdded extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New comment by '. data_get($this->data, 'user.name') . ' in ' . data_get($this->data, 'task.name') . '.';
    }

    public function getDescription(): string
    {
        return  data_get($this->data, 'user.name') . ' has added new comment to your task';
    }

    public function getUrl(): string
    {
        return ViewTask::getNavigationUrl([data_get($this->data, 'task_id')]);
    }
}
