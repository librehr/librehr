<?php

namespace App\Filament\Pages;

use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\Document;
use App\Models\DocumentsType;
use App\Models\Request;
use App\Services\Calendar;
use App\Services\Notifications;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Storage;
use function PHPUnit\Framework\isJson;

class TimeOff extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.time-off';

    public $contractId;
    public $year = null;
    public $calendar = [];
    public $summary = [];
    public $from;
    public $type;
    public $files  = [];

    public $absenceTypeId;
    public $absenceType;
    public $startDate = 0;
    public $endDate;
    public $days = 0;
    public $endDateMin;
    public $comments;

    public $contractAbsences;
    public $allowToRequest = true;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->getActiveBusinessId() && $user->getActiveContractId();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('request-absence')
                ->icon('heroicon-o-plus')
                ->slideOver()
                ->form([
                    Select::make('absenceType')
                        ->live()
                        ->options(function () {
                            return AbsenceType::query()->get()->pluck('name', 'id')->toArray();
                        })
                        ->required(),
                    DateRangePicker::make('date')
                        ->required(),
                    Textarea::make('comments'),
                    Placeholder::make('data')
                        ->label('')
                        ->content(function (Get $get) {
                            if ($get('date') !== null) {
                                [$from, $to] = explode(' - ', $get('date'));
                                $from = Carbon::createFromFormat('d/m/Y', $from);
                                $to = Carbon::createFromFormat('d/m/Y', $to);
                                $days = $from->diffInDays($to) + 1;
                                $daysAvailable = data_get($this->summary, 'total_days_pending');

                                $absenceType = AbsenceType::query()->find($get('absenceType'));

                                if ($days > $daysAvailable && data_get($absenceType, 'attributes.is_holidays', false) === true) {
                                    $this->allowToRequest = false;
                                    $message = "<div class='font-semibold text-red-600'>Not enought days available.</div>";
                                } else {
                                    $message = "<div>You have choosed " . $days . " days" . "</div>";
                                }

                                $overlaps = app(Calendar::class)
                                    ->getOverlaps($this->contractId, $from, $to);

                                $team = array_keys(data_get($overlaps, 'team', []));
                                $business = array_keys(data_get($overlaps, 'business', []));

                                if (count($team) > 0) {
                                    $message .= "<div>Team overlaps: ". implode(',', $team) . "</div>";
                                }

                                if (count($business) > 0) {
                                    $message .= "<div>Business overlaps: ". implode(',', $business) . "</div>";
                                }

                                return new HtmlString($message);
                            }
                            return "";
                        }),
                    FileUpload::make('file')
                        ->disk('local')
                        ->storeFileNamesIn('files')
                        ->directory('timeoff')
                        ->multiple()
                        ->hidden(function (Get $get) {
                            $absence = AbsenceType::query()->find($get('absenceType'));
                            if (data_get($absence, 'attributes.attachments', false) == true) {
                                return false;
                            }

                            return true;
                        }),
                ])
                ->action(function ($data) {
                    if ($this->allowToRequest === false) {
                        Notification::make()
                            ->title('Errors, request not processed.')
                            ->danger()
                            ->send();

                        return;
                    }
                    $user = Auth::user();
                    [$from, $to] = explode(' - ', data_get($data, 'date'));
                    $from = Carbon::createFromFormat('d/m/Y', $from);
                    $to = Carbon::createFromFormat('d/m/Y', $to);

                    $this->validate([
                        'files.*' => 'sometimes|max:4024', // Adjust max size as needed
                    ]);

                    $docs = [];
                    foreach (data_get($data, 'files', []) as $file => $name) {

                        $filePath = Storage::disk('local')->path($file);
                        $mimeType = filetype($filePath);
                        $size = filesize($filePath);

                        $docs[] = Document::query()->create([
                            'name' => $name,
                            'size' => $size,
                            'path' => $filePath,
                            'type' => $mimeType,
                            'user_id' => $user->id,
                            'uuid' => Str::uuid()
                        ]);
                    }


                    $contract = $user->getActiveContract()->load('team.supervisors');

                    $absence = Absence::query()->create([
                        'absence_type_id' => data_get($data, 'absenceType'),
                        'contract_id' => $user->getActiveContractId(),
                        'start' => $from,
                        'year' => $from->format('Y'),
                        'end' => $to,
                        'status' => 'pending',
                        'comments' => data_get($data, 'comments'),
                    ]);

                    if (!empty($docs)) {
                        $documentType = DocumentsType::query()->firstOrCreate([
                            'name' => 'Absences'
                        ]);

                        $absence->documents()->attach(collect($docs)->pluck('id')->toArray(), [
                            'documents_type_id' => data_get($documentType, 'id')
                        ]);
                    }


                    $request = Request::query()->firstWhere('name', 'absences');
                    $supervisors = data_get($contract, 'team.supervisors');

                    foreach ($supervisors as $supervisor) {
                        $absence->requests()->attach($absence->id, [
                            'request_id' => data_get($request, 'id'),
                            'user_id' => data_get($supervisor, 'id'),
                            'contract_id' => $user->getActiveContractId(),
                            'created_at' => now(),
                        ]);

                        Notifications::notify(
                            Notifications\Resources\TimeOffRequest::class,
                            $absence->load('contract.user'),
                            data_get($supervisor, 'id')
                        );
                    }

                    Notification::make()
                        ->title('Requested successfully')
                        ->success()
                        ->send();

                    $this->reloadAbsences();
                })
                ->modalWidth('sm')
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Time Off (' . $this->year . ')'; // TODO: Change the autogenerated stub
    }

    public function absenceType()
    {
    }

    public function updatedAbsenceTypeId()
    {
        $this->absenceType = AbsenceType::query()->find($this->absenceTypeId);
    }

    public function mount()
    {
        $this->contractId = Auth::user()->getActiveContractId();
        $this->year = date('Y');
        $this->loadCalendar($this->year);

        $this->type = AbsenceType::query()
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $this->reloadAbsences();
    }

    public function updateYear($type)
    {
        $this->year = $type === 'next' ? $this->year + 1 : $this->year - 1;
        $this->loadCalendar($this->year);
        $this->reloadAbsences();

    }

    public function openAbsence($id)
    {
        $this->dispatch('open-modal', id: 'view-absence');
    }
    public function loadCalendar($year = null)
    {
        $absences = Absence::query()
            ->whereIn('contract_id', [$this->contractId])
            ->whereYear('start', $year)
            ->where('status', 'allowed')
            ->orderBy('start')
            ->get();

        [$this->calendar, $this->summary] = app(Calendar::class)
            ->buildCalendar($this->contractId, $year ?? date('Y'), $absences);
    }

    public function reloadAbsences()
    {
        $this->contractAbsences = Absence::query()
            ->where('contract_id', $this->contractId)
            ->whereYear('start', $this->year)
            ->with('absenceType')
            ->orderBy('start')
            ->get()
            ->groupBy('status')
            ->toArray();
    }
}
