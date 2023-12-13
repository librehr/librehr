<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Signings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.signings';

    protected static ?int $navigationSort = 2;
}
