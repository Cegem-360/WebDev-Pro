<?php

declare(strict_types=1);

namespace App\Filament\Widgets\FinancialReport;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

final class FinancialReportOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {

        // Build base queries with payment type filter
        $incomeQuery = Income::query();
        $expenseQuery = Expense::query();

        // Calculate totals
        $totalIncome = (int) $incomeQuery->sum('amount');
        $totalExpense = (int) $expenseQuery->sum('amount');

        // Apply status filters on top of payment type filter
        $paidIncome = (int) (clone $incomeQuery)->whereStatus(PaymentStatuses::PAID)->sum('amount');
        $unpaidIncome = (int) (clone $incomeQuery)->whereStatus(PaymentStatuses::DRAFT)->sum('amount');
        $paidExpense = (int) (clone $expenseQuery)->whereStatus(PaymentStatuses::PAID)->sum('amount');
        $unpaidExpense = (int) (clone $expenseQuery)->whereStatus(PaymentStatuses::DRAFT)->sum('amount');

        $netBalance = $totalIncome - $totalExpense;

        return [
            Stat::make('Kifizetett bevétel', Number::currency($paidIncome, 'HUF', 'hu', 0))
                ->description('Teljes megkapott bevétel')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Kifizetetlen bevétel', Number::currency($unpaidIncome, 'HUF', 'hu', 0))
                ->description('Várható bevétel')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Kifizetett kiadás', Number::currency($paidExpense, 'HUF', 'hu', 0))
                ->description('Teljes kifizetett kiadás')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Kifizetetlen kiadás', Number::currency($unpaidExpense, 'HUF', 'hu', 0))
                ->description('Függőben lévő kiadások')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Egyenleg', Number::currency($netBalance, 'HUF', 'hu', 0))
                ->description('Bevétel mínusz kiadás')
                ->descriptionIcon($netBalance >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netBalance >= 0 ? 'success' : 'danger'),
        ];
    }
}
