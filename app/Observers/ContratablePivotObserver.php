<?php

namespace App\Observers;

use App\Models\Pivots\ContratablePivot;
use App\Services\Notifications;

class ContratablePivotObserver
{
    /**
     * Handle the ContratablePivot "created" event.
     */
    public function created(ContratablePivot $contratablePivot): void
    {
        $this->notifyTask($contratablePivot, 'create');
    }

    /**
     * Handle the ContratablePivot "updated" event.
     */
    public function updated(ContratablePivot $contratablePivot): void
    {
        //
    }

    /**
     * Handle the ContratablePivot "deleted" event.
     */
    public function deleted(ContratablePivot $contratablePivot): void
    {
        $this->notifyTask($contratablePivot, 'delete');
    }

    /**
     * Handle the ContratablePivot "restored" event.
     */
    public function restored(ContratablePivot $contratablePivot): void
    {
        //
    }

    /**
     * Handle the ContratablePivot "force deleted" event.
     */
    public function forceDeleted(ContratablePivot $contratablePivot): void
    {
        //
    }

    protected function notifyTask($contratablePivot, $action): void
    {
        if (data_get($contratablePivot, 'contratable_type') !== 'App\Models\Task') {
            return;
        }

        $contratablePivot->load('contratable');
        $contratablePivot->user = \Auth::user();

        if ($action === 'create') {
            Notifications::notify(
                Notifications\Resources\TaskAssigned::class,
                $contratablePivot,
                data_get($contratablePivot, 'contract.user_id')
            );
        }

        if ($action === 'delete') {
            Notifications::notify(
                Notifications\Resources\TaskUnassigned::class,
                $contratablePivot,
                data_get($contratablePivot, 'contract.user_id')
            );
        }
    }
}
