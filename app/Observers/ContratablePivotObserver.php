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
        $this->notifyTeam($contratablePivot, 'create');
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
        $this->notifyTeam($contratablePivot, 'delete');

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

    protected function notifyTeam($contratablePivot, $action): void
    {
        if (data_get($contratablePivot, 'contratable_type') !== 'App\Models\Team') {
            return;
        }

        $contratablePivot->load(['contratable.contracts', 'contract.user:id,name']);
        // Notify about the new supervisor added.
        $usersToNotify = data_get($contratablePivot, 'contratable.contracts.*.user');
        $contratablePivot->user = \Auth::user();
        $contratablePivot->supervisor = data_get($contratablePivot, 'contract.user');

        // Inform about the new supervisor created / deleted to all the team
        foreach ($usersToNotify as $user) {
            // do not notify the supervisor twice if is a member
            if (data_get($contratablePivot, 'supervisor.id') === data_get($user, 'id')) {
                continue;
            }

            if ($action === 'create') {
                Notifications::notify(
                    Notifications\Resources\TeamSupervisorAdded::class,
                    $contratablePivot,
                    data_get($user, 'id')
                );
            }

            if ($action === 'delete') {
                Notifications::notify(
                    Notifications\Resources\TeamSupervisorRemoved::class,
                    $contratablePivot,
                    data_get($user, 'id')
                );
            }
        }

        // Inform the supervisor that has been created / deleted

        if ($action === 'create') {
            Notifications::notify(
                Notifications\Resources\TeamSupervisorAddedNotifySupervisor::class,
                $contratablePivot,
                data_get($contratablePivot, 'supervisor.id')
            );
        }

        if ($action === 'delete') {
            Notifications::notify(
                Notifications\Resources\TeamSupervisorRemovedNotifySupervisor::class,
                $contratablePivot,
                data_get($contratablePivot, 'supervisor.id')
            );
        }
    }
}
