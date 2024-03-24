<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Attendances;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\MyProfile\Documents;
use App\Filament\Pages\MyProfile\ProfileTools;
use App\Filament\Resources\ExpenseResource;
use App\Filament\Resources\TaskResource\Pages\ViewTask;
use App\Filament\Resources\TeamResource;
use App\Services\Notifications\NotificationsResources;

class ExpenseAction extends NotificationsResources
{
    public function getTitle(): string
    {
        return data_get($this->data, 'user.name') . ' has mark as "' .  data_get($this->data, 'status')->getLabel() . '" your expense request for day  "' . data_get($this->data, 'date')->format('F d, Y') . '".';
    }
    public function getDescription(): string
    {
            return  'Go to your expenses to get more information.';
    }

    public function getUrl(): string
    {
        return ExpenseResource::getNavigationUrl();
    }
}
