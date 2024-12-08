<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Pages\MyProfile\ProfileTools;
use App\Services\Notifications\NotificationsResources;

class ContractToolDelivered extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New contract tool added: '. data_get($this->data, 'tool.name');
    }

    public function getDescription(): string
    {
        return 'Check out the details on your Profile Tools".';
    }

    public function getUrl(): string
    {
        return ProfileTools::getNavigationUrl();
    }
}
