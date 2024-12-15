<?php

namespace App\Filament\App\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class UserExpenseMonth extends BaseWidget
{
    protected function getStats(): array
    {
        $user = \Auth::user();
        $business = \Auth::user()->getActiveBusiness();
        $pending = Expense::query()->where('contract_id', $user->getActiveContractId())
            ->where('paid', false)
            ->whereIn('status', [
                'pending',
                'accepted'
            ])
            ->sum('amount');

        $paid = Expense::query()->where('contract_id', $user->getActiveContractId())
            ->whereMonth('paid_at', now())
            ->where('paid', true)
            ->sum('amount');

        $pendingColor = $pending > 0 ? 'red' : null;

        return [
            Stat::make('Paid this month', Number::currency($paid, data_get($business, 'attributes.default_currency', 0) ?? 0))
            ->icon('heroicon-m-check-circle'),
            Stat::make('Total Pending', Number::currency($pending, data_get($business, 'attributes.default_currency', 0) ?? 0))
        ];
    }
}
