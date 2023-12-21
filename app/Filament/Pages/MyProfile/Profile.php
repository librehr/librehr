<?php

namespace App\Filament\Pages\MyProfile;

use Filament\Pages\Page;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.pages.profile';

    protected static ?int $navigationSort = 3;


}
