<?php

namespace App\Services\Notifications\Resources;

use App\Services\Notifications\NotificationsResources;

class TeamSupervisorAddedNotifySupervisor extends NotificationsResources
{
    public function getTitle(): string
    {
        return data_get($this->data, 'user.name') . ' has added you as Team Supervisor of  ' . data_get($this->data, 'contratable.name') . '.';
    }
    public function getDescription(): string
    {
        return  'Welcome to the team "' . data_get($this->data, 'contratable.name') .'", you are the new supervisor.';
    }

    public function getUrl(): string
    {
        return \App\Filament\App\Resources\TeamResource\Pages\ViewTeam::getNavigationUrl([data_get($this->data, 'contratable.id')]);
    }
}
