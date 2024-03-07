<?php

namespace App\Filament\Traits;

use App\Models\Document;
use App\Models\DocumentsType;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait DocumentUploadTrait
{
    public function uploadDocumentAction(int $userId): Action
    {
        return Action::make('Attach Documents')
            ->form([
                Select::make('type')
                    ->required()
                    ->options(DocumentsType::query()
                        ->pluck('name', 'id')),
                Toggle::make('attributes.signature')
                    ->label('Require signature?')
                    ->inline(false),
                FileUpload::make('file')
                    ->disk('local')
                    ->multiple()
                    ->directory('documents')
                    ->downloadable()
                    ->storeFileNamesIn('attachment_file_names')
                    ->columnSpanFull(),
            ])->action(function ($data, $record) use ($userId) {
                $files = data_get($data, 'attachment_file_names');
                $attributes = data_get($data, 'attributes');
                $documents = [];
                foreach ($files as $file => $name) {
                    $documents[] = Document::query()->create([
                        'user_id' => $userId,
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now(),
                        'name' => $name,
                        'path' => $file,
                        'uuid' => Str::uuid(),
                        'size' => Storage::disk('local')->size($file),
                        'type' => Storage::disk('local')->mimeType($file),
                        'attributes' => $attributes
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
