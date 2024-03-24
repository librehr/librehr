<?php

namespace App\Observers;

use App\Models\Business;

class BusinessObserver
{
    /**
     * Handle the Business "created" event.
     */
    public function created(Business $business): void
    {
        //
    }

    /**
     * Handle the Business "updated" event.
     */
    public function updated(Business $business): void
    {
        \Cache::forget('business_' . $business->uuid);
    }

    /**
     * Handle the Business "deleted" event.
     */
    public function deleted(Business $business): void
    {
        \Cache::forget('business_' . $business->uuid);
    }

    /**
     * Handle the Business "restored" event.
     */
    public function restored(Business $business): void
    {
        //
    }

    /**
     * Handle the Business "force deleted" event.
     */
    public function forceDeleted(Business $business): void
    {
        //
    }
}
