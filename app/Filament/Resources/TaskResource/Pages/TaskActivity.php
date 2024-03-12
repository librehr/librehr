<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Enums\TaskPriorityEnum;
use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Support\Colors\Color;

class TaskActivity extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TaskResource::class;

    protected static string $view = 'filament.resources.task-resource.pages.task-activity';

    public $task;

    public function mount($record): void
    {
        $this->reformatTask($record);
    }

    protected function getHeaderActions(): array
    {
        return [
           Action::make('Change Status')
            ->color(Color::Gray)
            ->form([
                Radio::make('status')
                    ->options(collect(\App\Enums\TaskStatusEnum::cases())
                        ->pluck('name','value'))
                ->default(fn () => data_get($this, 'task.status'))
            ])->action(function ($data) {
                Task::query()
                    ->find(data_get($this->task, 'id'))
                    ->update(['status' => data_get($data, 'status')]);
                $this->reformatTask(data_get($this, 'task.id'));
            })
        ];
    }

    private function reformatTask($record)
    {
        $this->task = Task::query()->find($record)->toArray();

        data_set($this->task, 'priorityIcon', '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 "> <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" /> </svg>');
    }
}
