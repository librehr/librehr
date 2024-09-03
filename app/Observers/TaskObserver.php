<?php

namespace App\Observers;

use App\Models\task;
use App\Services\Notifications;

class TaskObserver
{
    /**
     * Handle the task "created" event.
     */
    public function created(task $task): void
    {
        rdump('new_task', [
            'created' => $task->toArray()
        ]);
    }

    /**
     * Handle the task "updated" event.
     */
    public function updated(task $task): void
    {
        $user = \Auth::user();

        if ($task->isDirty('priority')) {
            $task = $task->load('contracts');
            foreach (data_get($task, 'contracts', []) as $contract) {
                $task->priority_changed_by = $user;
                Notifications::notify(
                    Notifications\Resources\TaskPriorityChanged::class,
                    $task,
                    data_get($contract, 'user_id')
                );
            }
        }

        if ($task->isDirty('status')) {
            $task = $task->load('contracts');
            foreach (data_get($task, 'contracts', []) as $contract) {
                $task->status_changed_by = $user;
                Notifications::notify(
                    Notifications\Resources\TaskStatusChanged::class,
                    $task,
                    data_get($contract, 'user_id')
                );
            }
        }
    }

    /**
     * Handle the task "deleted" event.
     */
    public function deleted(task $task): void
    {
        //
    }

    /**
     * Handle the task "restored" event.
     */
    public function restored(task $task): void
    {
        //
    }

    /**
     * Handle the task "force deleted" event.
     */
    public function forceDeleted(task $task): void
    {
        //
    }
}
