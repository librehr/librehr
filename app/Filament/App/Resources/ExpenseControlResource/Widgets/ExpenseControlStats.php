<?php

namespace App\Filament\App\Resources\ExpenseControlResource\Widgets;

use App\Models\Expense;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class ExpenseControlStats extends BaseWidget
{
    protected function getStats(): array
    {
        $business = \Auth::user()->getActiveBusiness();
        $pending = Expense::query()
            ->whereIn('status', [
                'pending',
            ])
            ->count();

        $paid = Expense::query()
            ->whereMonth('paid_at', now())
            ->where('paid', true)
            ->sum('amount');

        $pendingColor = $pending > 0 ? 'red' : null;
        return [
            Stat::make('Total Pending', $pending)
                ->color(Color::Orange),
            Stat::make('Paid this month', Number::currency($paid, data_get($business, 'attributes.default_currency', 0) ?? 0))
            ->icon('heroicon-m-check-circle')
        ];
    }
}
