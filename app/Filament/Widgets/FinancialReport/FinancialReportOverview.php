<?php

declare(strict_types=1);

namespace App\Filament\Widgets\FinancialReport;

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
        $incomeQueryPaid = Income::query()
            ->paid()
            ->when($this->filters['year'] ?? null, fn ($query, $year) => $query->whereYear('payment_date', $year))
            ->when($this->filters['month'] ?? null, fn ($query, $month) => $query->whereMonth('payment_date', $month))
            ->pluck('amount')
            ->sum();
        $expenseQueryPaid = Expense::query()
            ->paid()
            ->when($this->filters['year'] ?? null, fn ($query, $year) => $query->whereYear('payment_date', $year))
            ->when($this->filters['month'] ?? null, fn ($query, $month) => $query->whereMonth('payment_date', $month))
            ->pluck('amount')
            ->sum();
        $incomeQueryUnpaid = Income::query()
            ->unpaid()
            ->when($this->filters['year'] ?? null, fn ($query, $year) => $query->whereYear('payment_date', $year))
            ->when($this->filters['month'] ?? null, fn ($query, $month) => $query->whereMonth('payment_date', $month))
            ->pluck('amount')
            ->sum();

        $expenseQueryUnpaid = Expense::query()
            ->unpaid()
            ->when($this->filters['year'] ?? null, fn ($query, $year) => $query->whereYear('payment_date', $year))
            ->when($this->filters['month'] ?? null, fn ($query, $month) => $query->whereMonth('payment_date', $month))
            ->pluck('amount')->sum();

        $netBalance = ($incomeQueryPaid + $incomeQueryUnpaid) - ($expenseQueryPaid + $expenseQueryUnpaid);

        return [
            Stat::make('Kifizetett bevétel', Number::currency($incomeQueryPaid, 'HUF', 'hu', 0))
                ->description('Teljes megkapott bevétel')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Kifizetett kiadás', Number::currency($expenseQueryPaid, 'HUF', 'hu', 0))
                ->description('Teljes kifizetett kiadás')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
            Stat::make('Egyenleg', Number::currency($netBalance, 'HUF', 'hu', 0))
                ->description('Bevétel mínusz kiadás')
                ->descriptionIcon($netBalance >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netBalance >= 0 ? 'success' : 'danger'),

            Stat::make('Kifizetetlen bevétel', Number::currency($incomeQueryUnpaid, 'HUF', 'hu', 0))
                ->description('Várható bevétel')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Kifizetetlen kiadás', Number::currency($expenseQueryUnpaid, 'HUF', 'hu', 0))
                ->description('Függőben lévő kiadások')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

        ];
    }
}
