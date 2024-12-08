<?php

namespace App\Filament\App\Pages\HumanResources;

use App\Filament\Admin\Resources\UserResource;
use App\Models\AbsenceType;
use App\Models\Contract;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class TimeOffControl extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';

    protected static string $view = 'filament.pages.time-off-control';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Human Resources';

    protected static ?string $navigationLabel = 'Time-Off Control';
    protected static ?string $title = 'Time-Off Control';

    public static function canAccess(): bool
    {
        return in_array(\Auth::user()->role->name, ['admin', 'manager']);
    }

    public $date;
    public $calendar;
    public $daysInMonth;

    public function mount()
    {
        $this->date = now();
        $this->calendar = \App\Models\Calendar::query()->whereYear('date', $this->date)->get();
        $this->daysInMonth = range(1, $this->date->daysInMonth);
    }

    public function change($position)
    {
        if ($position == 'next') {
            $this->date = Carbon::parse($this->date)->addMonth();
        } else {
            $this->date = Carbon::parse($this->date)->subMonth();
        }
        $this->daysInMonth = range(1, $this->date->daysInMonth);
        $this->calendar = \App\Models\Calendar::query()->whereYear('date', $this->date)->get();
        $this->resetTable();
    }

    public function getHeaderActions(): array
    {
        return [
        ];
    }

    public function table(Table $table): Table
    {
        $days = [];
        foreach ($this->daysInMonth as $day) {
            $days[] = TextColumn::make('' . $day)
                ->default(function ($record) use ($day) {
                    $type = null;
                    $absences = data_get($record, 'absences');
                    foreach ($absences as $absence) {
                        $start = Carbon::parse(data_get($absence, 'start'));
                        $end = Carbon::parse(data_get($absence, 'end'))->addDay();
                        if (Carbon::createFromDate(
                            $this->date->format('Y'),
                            $this->date->format('m'),
                            $day
                        )->between($start, $end)) {
                            $type = data_get($absence, 'absenceType');
                        }
                    }

                    if ($type) {
                        return $type;
                    }

                    return null;
                })
                ->formatStateUsing(function ($state) {
                    return new HtmlString('<div class="rounded py-1 px-2 text-xs" style="color: '.data_get($state, 'attributes.color.text').'; background-color: '.data_get($state, 'attributes.color.background').'">'.str(data_get($state, 'name'))->split(1)->first().'</div>');
                })
                ->color(function () {
                    return Color::Blue;
                })
                ->alignCenter(true);
        }

        return $table
            ->description(
                function () {
                    $absenceTypes = AbsenceType::query()->get()->pluck('name')->toArray();
                    return new HtmlString("<b>Types:</b> " . implode(', ', $absenceTypes));
                }
            )
            ->query(
                Contract::query()
                    ->where('business_id', \Auth::user()->getActiveBusinessId())
            )
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                ->url(fn ($record) => route(UserResource::getRouteBaseName('app') . '.absences', ['tenant' => Filament::getTenant(),$record->id])),
                ...$days,
            ])
            ->filters([
                // ...
            ])
            ->modifyQueryUsing(fn ($query) =>
                $query->with([
                    'user:name,id',
                    'team:name,id',
                    'absences' => function ($query) {
                        $query->where('status', 'allowed')
                            ->where(function ($query) {
                                $query->whereYear('start', $this->date)
                                    ->orWhereYear('end', $this->date);
                            });

                    },
                ]))
            ->bulkActions([
                // ...
            ])
            ->groups([
                'team.name',
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
