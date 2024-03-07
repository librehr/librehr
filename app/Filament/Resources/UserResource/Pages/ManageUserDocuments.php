<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Filament\Resources\UserResource;
use App\Filament\Traits\DocumentUploadTrait;
use App\Models\Document;
use App\Models\User;
use App\Services\Documents;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use function Symfony\Component\String\s;

class ManageUserDocuments extends Page
{
    use InteractsWithRecord, DocumentUploadTrait;

    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.manage-user-documents';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public $documents = null;

    public static function getNavigationLabel(): string
    {
        return 'Documents';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->uploadDocumentAction(
                data_get($this->getRecord(), 'id')
            )
        ];
    }

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;


    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();

        $this->documents = app(Documents::class)->getDocuments($this->record->id);
    }

    public function showDocument($id)
    {
        $this->dispatch('open-modal', id: $id);
    }
}
