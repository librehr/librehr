<?php

namespace App\Observers;

use App\Models\Expense;
use App\Services\Notifications;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {

    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        $this->notifyToEmployeeAction($expense);
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }

    public function notifyToEmployeeAction($expense)
    {
        $expense = $expense->load('contract.user:id,name');
        $expense->user = \Auth::user();
        Notifications::notify(
            Notifications\Resources\ExpenseAction::class,
            $expense,
            data_get($expense, 'contract.user.id')
        );
    }
}
