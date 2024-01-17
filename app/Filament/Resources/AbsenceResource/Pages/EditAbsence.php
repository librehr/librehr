<?php

namespace App\Filament\Resources\AbsenceResource\Pages;

use App\Filament\Resources\AbsenceResource;
use App\Filament\Traits\DocumentUploadTrait;
use App\Models\Document;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Storage;


class EditAbsence extends EditRecord
{
    use DocumentUploadTrait;
    protected static string $resource = AbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            $this->uploadDocumentAction()
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        //dd($data);
        $date = str(data_get($data, 'date'))->explode(' - ');
        $data['start'] = Carbon::createFromFormat('d/m/Y', $date[0]);
        $data['end'] =  Carbon::createFromFormat('d/m/Y', $date[1]);
        $data['status'] = 'pending';
        unset($data['date']);
        return parent::mutateFormDataBeforeSave($data); // TODO: Change the autogenerated stub
    }
}