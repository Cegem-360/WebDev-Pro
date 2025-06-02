<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class MonthlyComparison extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Current month data
        $currentMonthIncome = Income::query()
            ->wherePaymentDate('>=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');

        $currentMonthExpense = Expense::query()
            ->wherePaymentDate('>=', $currentMonth)->whereMonth('payment_date', '=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');

        // Previous month data
        $lastMonthIncome = Income::query()
            ->wherePaymentDate('>=', $lastMonth)
            ->wherePaymentDate('<=', $lastMonthEnd)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');

        $lastMonthExpense = Expense::query()
            ->wherePaymentDate('>=', $lastMonth)
            ->wherePaymentDate('<=', $lastMonthEnd)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');

        // Calculate differences
        $incomeChange = $lastMonthIncome > 0
            ? round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 1)
            : 0;

        $expenseChange = $lastMonthExpense > 0
            ? round((($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 1)
            : 0;

        // Calculate colors based on financial perspective
        $incomeColor = $incomeChange >= 0 ? 'success' : 'danger';
        $expenseColor = $expenseChange <= 0 ? 'success' : 'danger';

        return [
            Stat::make(__('Bevételek változása'), number_format($currentMonthIncome, 0, '.', ' ').' Ft')
                ->description($incomeChange >= 0
                    ? __(':percent% növekedés az előző hónaphoz képest', ['percent' => abs($incomeChange)])
                    : __(':percent% csökkenés az előző hónaphoz képest', ['percent' => abs($incomeChange)])
                )
                ->descriptionIcon($incomeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($incomeColor)
                ->chart([
                    $lastMonthIncome / 1000,
                    $currentMonthIncome / 1000,
                ]),

            Stat::make(__('Kiadások változása'), number_format($currentMonthExpense, 0, '.', ' ').' Ft')
                ->description($expenseChange <= 0
                    ? __(':percent% csökkenés az előző hónaphoz képest', ['percent' => abs($expenseChange)])
                    : __(':percent% növekedés az előző hónaphoz képest', ['percent' => abs($expenseChange)])
                )
                ->descriptionIcon($expenseChange <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
                ->color($expenseColor)
                ->chart([
                    $lastMonthExpense / 1000,
                    $currentMonthExpense / 1000,
                ]),

            Stat::make(__('Megtakarítás'), number_format($currentMonthIncome - $currentMonthExpense, 0, '.', ' ').' Ft')
                ->description(Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color(($currentMonthIncome - $currentMonthExpense) >= 0 ? 'success' : 'danger'),
        ];
    }
}
