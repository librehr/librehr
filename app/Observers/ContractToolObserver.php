<?php

namespace App\Observers;

use App\Models\ContractTool;
use App\Services\Notifications;

class ContractToolObserver
{
    /**
     * Handle the ContractTool "created" event.
     */
    public function created(ContractTool $contractTool): void
    {
        Notifications::notify(
            Notifications\Resources\ContractToolDelivered::class,
            $contractTool->load(['contract', 'tool', 'deliveredBy'])
        );
    }

    /**
     * Handle the ContractTool "updated" event.
     */
    public function updated(ContractTool $contractTool): void
    {
        //
    }

    /**
     * Handle the ContractTool "deleted" event.
     */
    public function deleted(ContractTool $contractTool): void
    {
        //
    }

    /**
     * Handle the ContractTool "restored" event.
     */
    public function restored(ContractTool $contractTool): void
    {
        //
    }

    /**
     * Handle the ContractTool "force deleted" event.
     */
    public function forceDeleted(ContractTool $contractTool): void
    {
        //
    }
}
