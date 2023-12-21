<?php

namespace App\Filament\Pages\MyProfile;

use Filament\Pages\Page;

class Documents extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.documents';
    protected static ?int $navigationSort = 3;



    protected static ?string $navigationParentItem = 'Profile';

}
