<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Resources\AbsenceResource;
use App\Filament\App\Traits\DocumentUploadTrait;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAbsence extends EditRecord
{
    use DocumentUploadTrait;
    protected static string $resource = AbsenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            $this->uploadDocumentAction(
                Auth::id()
            )
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        //dd($data);
        $date = str(data_get($data, 'date'))->explode(' - ');
        $data['start'] = Carbon::createFromFormat('d/m/Y', $date[0]);
        $data['end'] =  Carbon::createFromFormat('d/m/Y', $date[1]);
        unset($data['date']);
        return parent::mutateFormDataBeforeSave($data); // TODO: Change the autogenerated stub
    }
}
