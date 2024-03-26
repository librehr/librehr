<?php

namespace App\Observers;

use App\Models\Userable;

class UserableObserver
{
    /**
     * Handle the Userable "created" event.
     */
    public function creating(Userable $userable): void
    {
        if (auth()->check()) {
            $userable->business_id = \Auth::user()->getActiveBusinessId();
        }
    }

    /**
     * Handle the Userable "updated" event.
     */
    public function updating(Userable $userable): void
    {
        if (auth()->check()) {
            $userable->business_id = \Auth::user()->getActiveBusinessId();
        }
    }

    /**
     * Handle the Userable "deleted" event.
     */
    public function deleted(Userable $userable): void
    {
        //
    }

    /**
     * Handle the Userable "restored" event.
     */
    public function restored(Userable $userable): void
    {
        //
    }

    /**
     * Handle the Userable "force deleted" event.
     */
    public function forceDeleted(Userable $userable): void
    {
        //
    }
}
