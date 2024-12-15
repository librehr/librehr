<?php

namespace App\Services\Notifications\Resources;

use App\Filament\App\Pages\MyProfile\Documents;
use App\Services\Notifications\NotificationsResources;

class Document extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New document uploaded: '. data_get($this->data, 'name');
    }

    public function getDescription(): string
    {
        return 'Check out the new document under "My Documents > Recent".';
    }

    public function getUrl(): string
    {
        return Documents::getNavigationUrl();
    }
}
