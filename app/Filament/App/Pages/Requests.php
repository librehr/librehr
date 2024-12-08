<?php

namespace App\Filament\App\Pages;

use App\Models\Absence;
use App\Models\AttendanceValidation;
use App\Models\Document;
use App\Models\Requestable;
use App\Services\Notifications;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Auth;

class Requests extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'filament.pages.requests';

    protected static ?int $navigationSort = 0;
    protected static ?string $navigationLabel = 'Inbox';

    public $requests = [];

    public $user;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->getActiveBusinessId() && $user->getActiveContractId();
    }

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.header.requests');
    }

    protected function getActions(): array
    {
        return [
            Action::make('time-off-action')
                ->icon('heroicon-m-arrow-right')
                ->iconPosition(IconPosition::After)
                ->iconButton()
                ->label('Manage Time Off')
                ->color('primary')
                ->slideOver()
                ->requiresConfirmation()
                ->form([
                    Hidden::make('user')->default(data_get( $this->user, 'id')),
                    ToggleButtons::make('validated')
                        ->helperText('Once you have approved the request, you will not be able to change it again.')
                        ->label('Do you want to approve the absence requested by the employee?')
                        ->inline()
                        ->required()
                        ->boolean()
                ])
                ->action(function (array $arguments, $data) {
                    $record = Absence::query()->find(data_get($arguments, '0.requestable_id'));
                    $record->load(['contract']);
                    $record->status_by = data_get($data, 'user');
                    $record->status_at = now();

                    $message = 'Declined';
                    $record->status = 'denied';
                    if (data_get($data, 'validated', 0) == 1) {
                        $record->status = 'allowed';
                        $message = 'Approved';
                    }
                    $record->save();

                    $record->requests()->detach();

                    if (data_get($data, 'validated', 0) == 1) {
                        Notifications::notify(
                            Notifications\Resources\TimeOffValidated::class,
                            $record,
                            data_get($record, 'contract.user_id')
                        );
                    } else {
                        Notifications::notify(
                            Notifications\Resources\TimeOffDenied::class,
                            $record,
                            data_get($record, 'contract.user_id')
                        );
                    }

                    Notification::make('ok')
                        ->title($message . ' successfully.')
                        ->success()
                        ->send();
                })->after(function () {
                    $this->reloadRequests();
                }),
            Action::make('validate-attendances')
                ->icon('heroicon-m-arrow-right')
                ->iconPosition(IconPosition::After)
                ->iconButton()
                ->label('Got to Attendances')
                ->color('primary')
                ->slideOver()
                ->requiresConfirmation()
                ->form([
                    Hidden::make('user')->default(data_get( $this->user, 'id')),
                    ToggleButtons::make('validated')
                        ->label('Do you want to approve the validation requested?')
                        ->inline()
                        ->required()
                        ->boolean()
                ])
                ->action(function (array $arguments, $data) {
                    $record = AttendanceValidation::query()->find(data_get($arguments, '0.requestable_id'));
                    if (data_get($data, 'validated', 0) == 1) {
                        $record->requests()->detach();

                        Notification::make('ok')
                            ->title( 'Approved successfully.')
                            ->success()
                            ->send();
                    }
                })->after(function () {
                    $this->reloadRequests();
                }),
            Action::make('signs')
                ->icon('heroicon-m-arrow-right')
                ->iconPosition(IconPosition::After)
                ->iconButton()
                ->label('Sign files')
                ->color('primary')
                ->slideOver()
                ->requiresConfirmation()
                ->form([
                    Hidden::make('user')->default(data_get( $this->user, 'id')),
                ])
                ->action(function (array $arguments, $data) {
                    $record = Document::query()->find(data_get($arguments, '0.requestable_id'));
                    $record->requests()->detach();

                    Notification::make('ok')
                        ->title( 'Signed successfully.')
                        ->success()
                        ->send();
                })->after(function () {
                    $this->reloadRequests();
                }),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Requestable::query()->where('user_id', Auth::id())->count();
        return ($count > 0 ? $count : null);
    }


    public function mount()
    {
        $this->user = Auth::user();
        $this->reloadRequests();
    }

    protected function reloadRequests()
    {
        $this->requests = Requestable::query()
            ->where('user_id', Auth::id())
            ->with([
                'userTo',
                'contract:id,user_id,team_id',
                'contract.user:id,name,email',
                'contract.team:id,name',
                'request',
                'requestable'
            ])
            ->orderByDesc('created_at')
            ->get();
    }
}
