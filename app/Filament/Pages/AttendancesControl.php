<?php

namespace App\Filament\Pages;

use App\Models\AttendanceValidation;
use App\Models\Contract;
use App\Models\ContractTool;
use App\Models\Sushi\AttendanceControl;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;


class AttendancesControl extends Page  implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.attendances-control';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Human Resources';

    protected static ?string $navigationLabel = 'Attendances Control';
    protected static ?string $title = 'Attendances Control';

    public static function canAccess(): bool
    {
        return in_array(\Auth::user()->role->name, ['admin', 'manager']);
    }

    public $date;
    public function mount()
    {
        $this->date = request()->get('date');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AttendanceControl::setDateBusiness(Carbon::parse( $this->date), 1)
            )
            ->columns([
                TextColumn::make('status')
                    ->color(fn ($record) => $this->statusInfo(data_get($record, 'summary.status'), true))
                    ->badge()
                    ->default(function ($record) {
                        return $this->statusInfo(data_get($record, 'summary.status'), false);
                    }),
                TextColumn::make('summary.user.team')->label('Team')->searchable(),
                TextColumn::make('summary.user.name')->label('Employee')->searchable(),
                TextColumn::make('summary.total_time')->label('Total Time'),
                TextColumn::make('summary.total_time_estimated')->label('Total Time Estimated'),
                TextColumn::make('summary.total_time_extra')->label('Total Time Extra')->searchable(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('request revision')
                    ->button()
                    ->color('gray')
                    ->outlined()
                    ->action(function (AttendanceControl $record) {
                    })->requiresConfirmation()
                    ->hidden(fn ($record) => data_get($record, 'summary.user.validations') !== null),
                Action::make('validate')
                    ->label(fn ($record) => data_get($record, 'summary.user.validations') !== null ? 'Validated': 'Mark as validated')
                    ->button()
                    ->color(fn ($record) => data_get($record, 'summary.user.validations') !== null ? Color::Green : Color::Yellow)
                    ->action(function (AttendanceControl $record) {
                        $validations = AttendanceValidation::query()
                            ->whereYear('date', Carbon::parse( $this->date))
                            ->whereMonth('date', Carbon::parse( $this->date))
                            ->where('contract_id', data_get($record, 'summary.user.contract_id'))
                            ->where('business_id', data_get($record, 'summary.user.business_id'))
                            ->first();

                        if (!$validations) {
                            AttendanceValidation::query()->create([
                                'business_id' => data_get($record, 'summary.user.business_id'),
                                'contract_id' =>  data_get($record, 'summary.user.contract_id'),
                                'date' => Carbon::parse( $this->date),
                                'validated_by' => \Auth::id(),
                                'validated_at' => now()
                            ]);

                            Notification::make()
                                ->title('Validated successfully')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('This attendace has been validated previously.')
                                ->warning()
                                ->send();
                        }


                    })->requiresConfirmation()
                ->disabled(fn ($record) => data_get($record, 'summary.user.validations') !== null),
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function statusInfo(int $status, $color = true)
    {
        if ($status === 0) {
            return $color ? Color::Green : 'Perfect';
        } elseif ($status === 1) {
            return $color ? Color::Yellow : 'Warning';
        }

        return $color ? Color::Red : 'Alert';
    }
}
