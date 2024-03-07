<?php

namespace App\Services\Notifications\Resources;

use App\Filament\Pages\Dashboard;
use App\Services\Notifications\NotificationsResources;

class Post extends NotificationsResources
{
    public function getTitle(): string
    {
        return 'New post! '. data_get($this->data, 'title');
    }

    public function getDescription(): string
    {
        return 'Check out the new post now.';
    }

    public function getUrl(): string
    {
        return Dashboard::getNavigationUrl();
    }
}
