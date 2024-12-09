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
        $contractTool = $contractTool->load(['contract', 'tool', 'deliveredBy']);
        Notifications::notify(
            Notifications\Resources\ContractToolDelivered::class,
            $contractTool,
            data_get($contractTool, 'contract.user_id')
        );
    }

    /**
     * Handle the ContractTool "updated" event.
     */
    public function updating(ContractTool $contractTool): void
    {
        $contractTool->returned_to = \Auth::id();
    }

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
