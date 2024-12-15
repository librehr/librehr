<?php

namespace App\Filament\App\Pages\MyProfile;

use Filament\Pages\Page;

class Documents extends Page
{
    protected static ?string $navigationIcon = null;

    protected static string $view = 'filament.pages.my-profile.profile-documents';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationParentItem = 'Profile';

    public $documents;
    public function mount()
    {
        $this->documents = app(\App\Services\Documents::class)
            ->getDocuments(\Auth::id());
    }
}
