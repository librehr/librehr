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
                'relatedType',
            ])
            ->get()
            ->mapToGroups(function ($record) {
                $modelName = $record->relatedType->documentable_type;
                $modelName = class_basename($modelName);
                $modelName = app('App\Filament\Resources\\'.$modelName . 'Resource')->getNavigationLabel() ?? $modelName;
                return [$modelName => $record];
            });
    }
}
