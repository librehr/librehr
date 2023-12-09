<?php

namespace App\Observers;

use App\Models\team;

class Team
{
    /**
     * Handle the team "created" event.
     */
    public function created(team $team): void
    {
        //
    }

    /**
     * Handle the team "updated" event.
     */
    public function updated(team $team): void
    {
        //
    }

    /**
     * Handle the team "deleted" event.
     */
    public function deleted(team $team): void
    {
        //
    }

    /**
     * Handle the team "restored" event.
     */
    public function restored(team $team): void
    {
        //
    }

    /**
     * Handle the team "force deleted" event.
     */
    public function forceDeleted(team $team): void
    {
        //
    }
}
