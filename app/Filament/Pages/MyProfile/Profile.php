<?php

namespace App\Filament\Pages\MyProfile;

use Filament\Pages\Page;

class Profile extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.profile';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'My Profile';
}
