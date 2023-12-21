<?php

namespace App\Filament\Pages\MyProfile;

use Filament\Pages\Page;

class ProfileContracts extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.my-profile.profile-contracts';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationParentItem = 'Profile';

    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $title = 'Contracts';
}
