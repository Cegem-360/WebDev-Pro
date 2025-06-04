<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentTypes;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

final class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        dump($this->filters);
        $currentMonth = Carbon::now()->startOfMonth();
        /*    $paymentType = $this->getData()['payment_type'] ?? null; */
        $paymentType = PaymentTypes::SINGLE;
        // Apply payment type filter if specified
        $incomeQuery = Income::query()
            ->whereBetween('payment_date', [
                $currentMonth,
                $currentMonth->copy()->endOfMonth(),
            ]);

        $expenseQuery = Expense::query()
            ->whereBetween('payment_date', [
                $currentMonth,
                $currentMonth->copy()->endOfMonth(),
            ]);

        $monthlyIncome = $incomeQuery->pluck('amount')->sum();
        $monthlyExpense = $expenseQuery->pluck('amount')->sum();

        $balance = $monthlyIncome - $monthlyExpense;
        $color = $balance >= 0 ? 'success' : 'danger';

        // Add payment type info to description
        $filterDescription = match ($paymentType) {
            PaymentTypes::SINGLE => ' (Egyszeri)',
            PaymentTypes::RECURRING => ' (Ismétlődő)',
            default => 'Összes',
        };

        return [
            Stat::make('Havi bevétel'.$filterDescription, Number::currency($monthlyIncome, 'HUF', 'hu', 0))
                ->description('Az aktuális hónap bevétele')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Havi kiadás'.$filterDescription, Number::currency($monthlyExpense, 'HUF', 'hu', 0))
                ->description('Az aktuális hónap kiadása')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Egyenleg'.$filterDescription, Number::currency($balance, 'HUF', 'hu', 0))
                ->description('Havi bevétel és kiadás különbsége')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($color),
        ];
    }
}
