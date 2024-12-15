<?php

namespace App\Livewire;

use App\Forms\Components\DownloadFile;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\MaxWidth;
use Livewire\Component;

class UserDownloadComponent extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public array $documents = [];
    public function mount($documents)
    {
        $this->documents = $documents;
    }

    public array $data = [];
    public function categoryAction(): Action
    {
        return Action::make('category')
            ->label('Files')
            ->slideOver()
            ->modalFooterActions(fn () => [])
            ->modalWidth(MaxWidth::Large)
            ->modalAutofocus(false)
            ->form(function ($data, $arguments) {
                $arguments = collect($arguments)->map(function ($file) {
                    $file['size'] = \Number::fileSize($file['size']);
                    $file['uploaded_at'] = Carbon::parse($file['uploaded_at'])->format('M, d Y');

                    return $file;
                });

                return [
                    Repeater::make('data')
                        ->label('')
                        ->simple(DownloadFile::make('size'))
                        ->default($arguments)
                        ->collapsed(true)
                        ->reorderable(false)
                        ->extraItemActions([
                            \Filament\Forms\Components\Actions\Action::make('download')
                                ->color('primary')
                                ->icon('lucide-download')
                        ])
                        ->itemLabel(fn ($state) => $state['name'])
                        ->deletable(false)
                        ->addable(false)
                        ->live()
                        ->schema([
                            DownloadFile::make('user.name')
                                ->label('Uploaded By'),
                            DownloadFile::make('size'),
                            DownloadFile::make('uploaded_at'),
                        ])
                ];
            });
    }

    /**
     * @return array
     */
    public function getCachedActions(): array
    {
        return [
            Action::make('category')
                ->slideOver()
                ->form([
                    TextInput::make('hola')
                ])
        ];
    }

    public function render()
    {
        return view('livewire.user-download-component');
    }
}
