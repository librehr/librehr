<?php

namespace App\Filament\Pages;

use App\Models\Absence;
use App\Models\Request;
use App\Models\Requestable;
use App\Services\Calendar;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use function PHPUnit\Framework\isJson;

class Requests extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static string $view = 'filament.pages.requests';

    public $requests = [];

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
                    Placeholder::make('')->content(function () {
                        $arguments = $this->mountedActionsArguments[0][0] ?? [];
                        $overlaps = app(Calendar::class)
                            ->getOverlaps(
                                data_get($arguments, 'contract_id'),
                                Carbon::parse(data_get($arguments, 'requestable.start')),
                                Carbon::parse(data_get($arguments, 'requestable.end')),
                            );

                        return json_encode($overlaps);
                    }),
                    ToggleButtons::make('validated')
                        ->helperText('Once you have approved the request, you will not be able to change it again.')
                        ->label('Do you want to approve the vacation requested by the employee?')
                        ->inline()
                        ->required()
                        ->boolean()
                ])
                ->action(function (array $arguments, $data) {
                    $record = Absence::query()->find(data_get($arguments, 'id'));
                    $record->status_by = \Auth::id();
                    $record->status_at = now();

                    $message = 'Declined';
                    $record->status = 'denied';
                    if (data_get($data, 'validated', 0) == 1) {
                        $record->status = 'allowed';
                        $message = 'Approved';
                    }
                    $record->save();

                    $record->requests()->detach();

                    Notification::make('ok')
                        ->title($message . ' successfully.')
                        ->success()
                        ->send();
                })->after(function () {
                    $this->reloadRequests();
                }),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Requestable::query()->where('user_id', Auth::id())->count();
    }


    public function mount()
    {
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
