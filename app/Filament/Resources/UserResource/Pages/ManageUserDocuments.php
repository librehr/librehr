<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Document;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use function Symfony\Component\String\s;

class ManageUserDocuments extends Page
{
    use InteractsWithRecord;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.manage-user-documents';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public $documents = null;

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();

        $this->documents = Document::query()->get();
    }
}
