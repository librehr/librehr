<?php

namespace App\Filament\App\Pages\HumanResources;

use App\Models\AttendanceValidation;
use App\Models\Contract;
use App\Models\Request;
use App\Models\Team;
use App\Services\Notifications;
use Carbon\Carbon;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AttendancesControl extends Page implements HasForms, HasTable
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
        $myId = \Auth::id();
        $teamsId = Team::query()
            ->with([
                'supervisors'
            ])
            ->whereRelation('supervisors', 'user_id', $myId)
            ->get()
            ->pluck('id');

        if ($teamsId->count() > 0) {
            return true;
        }


        return in_array(\Auth::user()->role->name, ['admin', 'manager']);
    }

    public $date;
    public $calendar;

    public function mount()
    {
        $this->date = now();
        $this->calendar = \App\Models\Calendar::query()->whereYear('date', $this->date)->get();
    }

    public function change($position)
    {
        if ($position == 'next' && now()->subMonth() > $this->date) {
            $this->date = Carbon::parse($this->date)->addMonth();
        } else {
            $this->date = Carbon::parse($this->date)->subMonth();
        }

        $this->calendar = \App\Models\Calendar::query()->whereYear('date', $this->date)->get();

    }

    public function getHeaderActions(): array
    {
        return [
        ];
    }

    public function table(Table $table): Table
    {
        $myId = \Auth::id();
        $teamsId = Team::query()
            ->with([
                'supervisors'
            ])
            ->whereRelation('supervisors', 'user_id', $myId)
            ->get()
            ->pluck('id')
            ->toArray();

        return $table
            ->query(
                Contract::query()
                    ->where('business_id', \Auth::user()->getActiveBusinessId())
            )
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('team.name')
                    ->searchable(),
                TextColumn::make('attendances')
                    ->label('Time Worked')
                    ->formatStateUsing(function ($record) {
                        return app(\App\Services\Attendances::class)
                        ->secondsToHm(
                            data_get($record, 'attendances')?->sum('seconds')
                        );
                    }),
                TextColumn::make('planning')
                    ->label('Estimated')
                    ->formatStateUsing(function ($state) {
                        $planningWorkDays = app(\App\Services\Attendances::class)->getPeriods(
                            data_get($state, 'attributes.periods'),
                            $this->date
                        );

                        $estimatedByDay = app(\App\Services\Attendances::class)
                            ->getEstimatedWorkTime($planningWorkDays);

                        $seconds = [];
                        $days = range(1, $this->date->daysInMonth);
                        foreach ($days as $day) {
                            $dateAttendance = Carbon::createFromDate($this->date->format('Y-m-') . $day);
                            $estimated = data_get($estimatedByDay, $dateAttendance->format('N'));
                            $workable = true;
                            $calendarDay = $this->calendar->where('date', $dateAttendance);
                            if ($calendarDay->where('workable', false)->count() > 0) {
                                $workable = false;
                            }
                            $seconds[] = $workable ? data_get($estimated, 'seconds') : 0;
                        }

                        return app(\App\Services\Attendances::class)->secondsToHm(array_sum($seconds));
                    }),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('revision')
                    ->label('Request Revision')
                    ->button()
                    ->color('gray')
                    ->outlined()
                    ->action(function (Contract $record) {
                        $validation = AttendanceValidation::query()->create([
                            'business_id' => data_get($record, 'business_id'),
                            'contract_id' =>  data_get($record, 'id'),
                            'date' => $this->date
                        ]);

                        $request = Request::query()->firstOrCreate(['name' => 'attendance-validations']);

                        $validation->requests()->attach($validation->id, [
                            'request_id' => data_get($request, 'id'),
                            'user_id' => data_get($record, 'user_id'),
                            'contract_id' => data_get($record, 'id'),
                            'created_at' => now(),
                        ]);

                        Notifications::notify(
                            Notifications\Resources\AttendanceRevision::class,
                            $validation,
                            data_get($record, 'user_id')
                        );

                        Notification::make()
                            ->title('Request sended successfully.')
                            ->success()
                            ->send();
                    })->requiresConfirmation()
                    ->hidden(function ($record) {
                        $validations = data_get($record, 'attendancesValidations')
                            ?->where('yearMonth', $this->date->format('Y-m'))
                            ->where('validated', true)
                            ->count();
                        if ($validations > 0) {
                            return true;
                        }

                        return false;
                    }),
                Action::make('validate')
                    ->label(
                        function ($record) {
                            $validations = data_get($record, 'attendancesValidations')
                                ?->where('yearMonth', $this->date->format('Y-m'))
                                ->where('validated', true)
                                ->count();
                            if ($validations > 0) {
                                return 'Validated';
                            }

                            return 'Mark as validated';
                        }
                    )->button()
                    ->color(function ($record) {
                        $validations = data_get($record, 'attendancesValidations')
                            ?->where('yearMonth', $this->date->format('Y-m'))
                            ->where('validated', true)
                            ->count();
                        if ($validations > 0) {
                            return Color::Green;
                        }

                        return Color::Yellow;
                    })
                    ->requiresConfirmation()
                    ->action(function (Contract $record) {
                        $validations = AttendanceValidation::query()
                            ->whereYear('date', Carbon::parse($this->date))
                            ->whereMonth('date', Carbon::parse($this->date))
                            ->where('contract_id', data_get($record, 'id'))
                            ->where('business_id', data_get($record, 'business_id'))
                            ->first();

                        if (!$validations) {
                            $attendance = AttendanceValidation::query()->create([
                                'business_id' => data_get($record, 'business_id'),
                                'contract_id' =>  data_get($record, 'id'),
                                'date' => $this->date,
                                'validated_by' => \Auth::id(),
                                'validated_at' => now(),
                                'validated' => true,
                            ]);

                            $attendance->load(['validatedBy', 'contract']);

                            Notifications::notify(
                                Notifications\Resources\AttendanceValidation::class,
                                $attendance,
                                data_get($attendance, 'contract.user_id')
                            );

                            Notification::make()
                                ->title('Validated successfully.')
                                ->success()
                                ->send();

                            return;
                        }

                        if (data_get($validations, 'validated', false) === false) {
                            $validations->validated = true;
                            $validations->validated_by = true;
                            $validations->validated_at = now();
                            $validations->save();

                            Notification::make()
                                ->title('Validated successfully.')
                                ->warning()
                                ->send();

                            return;
                        }

                        Notification::make()
                                ->title('This attendace has been validated previously.')
                                ->warning()
                                ->send();
                    })
                    ->disabled(function ($record) {
                        $validations = data_get($record, 'attendancesValidations')
                            ?->where('yearMonth', $this->date->format('Y-m'))
                            ->where('validated', true)
                            ->count();
                        if ($validations > 0) {
                            return true;
                        }

                        return false;
                    }),
            ])
            ->groups([
                'team.name',
            ])
            ->modifyQueryUsing(
                fn ($query) =>
                $query->with([
                    'user:name,id',
                    'attendancesValidations',
                    'team:name,id',
                    'planning' => function ($query) {
                    },
                    'attendances' => function ($query) {
                        $query->whereMonth('start', $this->date)
                            ->whereYear('start', $this->date)
                            ->select([
                                'id',
                                'contract_id',
                                'date',
                                'start',
                                'end'
                            ]);
                    }
                ])
                ->whereRelation('team', 'id', $teamsId)
            )
            ->emptyStateHeading('You don\'t have any team assigned.')
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
