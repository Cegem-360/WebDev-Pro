<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Dashboard;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

final class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {

        $currentMonth = Carbon::now()->startOfMonth();

        $paymentType = $this->filters['payment_type'] ?? null;

        $status = $this->filters['payment_status'] ?? PaymentStatuses::PAID;

        $incomeQuery = Income::query()
            ->whereBetween('payment_date', [
                $currentMonth,
                $currentMonth->copy()->endOfMonth(),
            ])
            ->when($paymentType, fn (Builder $query) => $query->wherePaymentType($paymentType))
            ->when($status, fn (Builder $query) => $query->whereStatus($status))
            ->pluck('amount')
            ->sum();
        $expenseQuery = Expense::query()
            ->whereBetween('payment_date', [
                $currentMonth,
                $currentMonth->copy()->endOfMonth(),
            ])
            ->when($paymentType, fn (Builder $query) => $query->wherePaymentType($paymentType))
            ->when($status, fn (Builder $query) => $query->whereStatus($status))
            ->pluck('amount')
            ->sum();
        // Add payment type info to description
        $balance = $incomeQuery - $expenseQuery;
        $color = $balance >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Havi bevétel'.$paymentType, Number::currency($incomeQuery, 'HUF', 'hu', 0))
                ->description('Az aktuális hónap bevétele')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Havi kiadás'.$paymentType, Number::currency($expenseQuery, 'HUF', 'hu', 0))
                ->description('Az aktuális hónap kiadása')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Egyenleg'.$paymentType, Number::currency($balance, 'HUF', 'hu', 0))
                ->description('Havi bevétel és kiadás különbsége')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($color),
        ];
    }
}
