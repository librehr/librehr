<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Resources\TaskResource\Pages\ViewTask;
use App\Services\Notifications\NotificationsResources;

class ReactionAdded extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New reaction from '. data_get($this->data, 'user.name') . ' in: ' . data_get($this->data, 'task.name') . '.';
    }

    public function getDescription(): string
    {
        return  data_get($this->data, 'user.name') . ' react with "' . data_get($this->data, 'reaction_type') . '"';
    }

    public function getUrl(): string
    {
        return ViewTask::getNavigationUrl([data_get($this->data, 'task_id')]);
    }
}
