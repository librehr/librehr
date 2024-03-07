<?php

namespace App\Services;


use App\Models\Absence;
use App\Models\Document;
use Illuminate\Support\Carbon;

class Documents extends BaseService
{
    public function getDocuments($userId)
    {
        return Document::query()
            ->where('user_id', $userId)
            ->with([
                'documentable.type',
                'user:id,name'
            ])
            ->get()
            ->mapToGroups(function ($record) {
                $modelName = data_get($record, 'documentable.type.name', 'No classificated');
                return [$modelName => $record];
            });
    }
}
