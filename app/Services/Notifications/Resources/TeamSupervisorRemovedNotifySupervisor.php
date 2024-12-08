<?php

namespace App\Services\Notifications\Resources;

use App\Services\Notifications\NotificationsResources;

class TeamSupervisorRemovedNotifySupervisor extends NotificationsResources
{
    public function getTitle(): string
    {
        return data_get($this->data, 'user.name') . ' has removed you as Team Supervisor of  ' . data_get($this->data, 'contratable.name') . '.';
    }
    public function getDescription(): string
    {
            return  'You are no more supervising "' . data_get($this->data, 'contratable.name') .'" Team, thanks for your help!.';
    }

    public function getUrl(): string
    {
        return \App\Filament\App\Resources\TeamResource\Pages\ViewTeam::getNavigationUrl([data_get($this->data, 'contratable.id')]);
    }
}
