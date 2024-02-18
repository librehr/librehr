<?php

namespace App\Filament\Traits;

use App\Models\Document;
use App\Models\DocumentsType;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait DocumentUploadTrait
{
    public function uploadDocumentAction(): Action
    {
        return Action::make('Attach Documents')
            ->form([
                Select::make('type')
                    ->required()
                    ->options(DocumentsType::query()
                        ->pluck('name', 'id')),
                FileUpload::make('file')
                    ->disk('local')
                    ->multiple()
                    ->directory('documents')
                    ->downloadable()
                    ->storeFileNamesIn('attachment_file_names')
                    ->columnSpanFull()
            ])->action(function ($data, $record) {
                $files = data_get($data, 'attachment_file_names');
                $documents = [];
                foreach ($files as $file => $name) {
                    $documents[] = Document::query()->create([
                        'user_id' => Auth::id(),
                        'name' => $name,
                        'path' => $file,
                        'uuid' => Str::uuid(),
                        'size' => Storage::disk('local')->size($file),
                        'type' => Storage::disk('local')->mimeType($file),
                    ])->id;
                }

                if (!empty($documents)) {
                    $record->documents()->attach($documents,
                    [
                        'documents_type_id' => data_get($data, 'type')
                    ]);

                    Notification::make()
                        ->title('Upload successfully')
                        ->success()
                        ->send();
                }

                return $this->redirect(route($this->getRouteName(), $record->id));
            })
            ->color(Color::Slate);
    }
}
