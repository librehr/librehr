<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Expense;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use function Filament\Support\format_number;

class UserExpenseMonth extends BaseWidget
{
    protected function getStats(): array
    {
        $user = \Auth::user();
        $pending = Expense::query()->where('contract_id', $user->getActiveContractId())
            ->where('paid', false)
            ->sum('amount');

        $paid = Expense::query()->where('contract_id', $user->getActiveContractId())
            ->whereMonth('paid_at', now())
            ->where('paid', true)
            ->sum('amount');

        $pendingColor = $pending > 0 ? 'red' : null;
        return [
            Stat::make('Paid this month', Number::currency($paid ,config('librehr.currency')))
            ->icon('heroicon-m-check-circle'),
            Stat::make('Pending', Number::currency($pending, config('librehr.currency')))
        ];
    }
}
