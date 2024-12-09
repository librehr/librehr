<?php

namespace App\Filament\App\Resources\ContractResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Filament\App\Resources\ContractResource;
use App\Filament\App\Traits\DocumentUploadTrait;
use App\Services\Documents as DocumentService;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class Documents extends Page
{
    use InteractsWithRecord;
    use DocumentUploadTrait;

    protected static string $resource = ContractResource::class;

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

        $this->documents = app(DocumentService::class)->getDocuments($this->record->id);
    }

    public function showDocument($id)
    {
        $this->dispatch('open-modal', id: $id);
    }
}
