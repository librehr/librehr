<?php

namespace App\Services\Notifications\Resources;

use App\Services\Notifications\NotificationsResources;

class TeamSupervisorAdded extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New supervisor added to your team ' . data_get($this->data, 'contratable.name') . '.';
    }

    public function getDescription(): string
    {
        return  data_get($this->data, 'supervisor.name') . ' has been added to your team.';
    }

    public function getUrl(): string
    {
        return \App\Filament\App\Resources\TeamResource\Pages\ViewTeam::getNavigationUrl([data_get($this->data, 'contratable.id')]);
    }
}
