<?php

namespace App\Filament\App\Resources\AbsenceResource\Pages;

use App\Filament\App\Resources\AbsenceResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAbsence extends CreateRecord
{
    protected static string $resource = AbsenceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //dd($data);
        $date = str(data_get($data, 'date'))->explode(' - ');
        $data['start'] = Carbon::createFromFormat('d/m/Y', $date[0]);
        $data['end'] =  Carbon::createFromFormat('d/m/Y', $date[1]);
        $data['contract_id'] = Auth::user()->getActiveContractId();
        $data['status'] = 'pending';
        unset($data['date']);
        return parent::mutateFormDataBeforeCreate($data); // TODO: Change the autogenerated stub
    }
}