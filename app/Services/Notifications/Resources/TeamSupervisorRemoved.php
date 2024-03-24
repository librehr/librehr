<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Filament\Resources\TeamResource;
use App\Services\Notifications\NotificationsResources;

class TeamSupervisorRemoved extends NotificationsResources
{
    public function getTitle(): string
    {
        return data_get($this->data, 'supervisor.name') . ' has been removed as Team Supervisor of  ' . data_get($this->data, 'contratable.name') . '.';
    }

    public function getDescription(): string
    {
            return  data_get($this->data, 'supervisor.name') . ' has been removed from supervising your team.';
    }

    public function getUrl(): string
    {
        return TeamResource\Pages\ViewTeam::getNavigationUrl([data_get($this->data, 'contratable.id')]);
    }
}
