<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

final class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        $monthlyIncome = Income::getDateBetweenIncome($currentMonth, $currentMonth->copy()->endOfMonth());

        $monthlyExpense = Expense::getDateBetweenExpense($currentMonth, $currentMonth->copy()->endOfMonth());

        $balance = $monthlyIncome - $monthlyExpense;
        $color = $balance >= 0 ? 'success' : 'danger';

        $recurringIncome = Income::query()
            ->where('payment_type', PaymentTypes::RECURRING)
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        $recurringExpense = Expense::query()
            ->where('payment_type', PaymentTypes::RECURRING)
            ->where('status', PaymentStatuses::PAID)
            ->sum('amount');

        return [
            Stat::make(__('Havi bevétel'), Number::currency($monthlyIncome, 'HUF', 'hu', 0))
                ->description(__('Az aktuális hónap bevétele'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Havi kiadás'), Number::currency($monthlyExpense, 'HUF', 'hu', 0))
                ->description(__('Az aktuális hónap kiadása'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Egyenleg'), Number::currency($balance, 'HUF', 'hu', 0))
                ->description(__('Havi bevétel és kiadás különbsége'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($color),
        ];
    }
}
