<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $monthlyIncome = Income::query()
            ->where('date', '>=', $currentMonth)
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        $monthlyExpense = Expense::query()
            ->where('payment_date', '>=', $currentMonth)
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        $balance = $monthlyIncome - $monthlyExpense;
        $color = $balance >= 0 ? 'success' : 'danger';

        $recurringIncome = Income::query()
            ->where('payment_type', 'recurring')
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        $recurringExpense = Expense::query()
            ->where('payment_type', 'recurring')
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        return [
            Stat::make(__('Havi bevétel'), number_format($monthlyIncome, 0, '.', ' ').' Ft')
                ->description(__('Az aktuális hónap bevétele'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Havi kiadás'), number_format($monthlyExpense, 0, '.', ' ').' Ft')
                ->description(__('Az aktuális hónap kiadása'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Egyenleg'), number_format($balance, 0, '.', ' ').' Ft')
                ->description(__('Havi bevétel és kiadás különbsége'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($color),
        ];
    }
}
