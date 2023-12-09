<?php

namespace App\Observers;

use App\Models\Team;

class TeamObserver
{
    /**
     * Handle the team "created" event.
     */
    public function creating(Team $team): void
    {
        if (auth()->check()) {
            $team->business_id = data_get(auth()->user()->getActiveBusiness(), 'id');
        }
    }

    /**
     * Handle the team "updated" event.
     */
    public function updated(Team $team): void
    {
        //
    }

    /**
     * Handle the team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        //
    }

    /**
     * Handle the team "restored" event.
     */
    public function restored(Team $team): void
    {
        //
    }

    /**
     * Handle the team "force deleted" event.
     */
    public function forceDeleted(Team $team): void
    {
        //
    }
}
