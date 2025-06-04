<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

final class MonthlyComparison extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $dateRanges = $this->getDateRanges();

        // Get aggregated data with single database queries
        $currentMonthIncome = $this->getMonthlySum(Income::class, $dateRanges['current']);
        $currentMonthExpense = $this->getMonthlySum(Expense::class, $dateRanges['current']);
        $lastMonthIncome = $this->getMonthlySum(Income::class, $dateRanges['last']);
        $lastMonthExpense = $this->getMonthlySum(Expense::class, $dateRanges['last']);

        // Calculate changes and determine colors
        $incomeData = $this->calculateChange($currentMonthIncome, $lastMonthIncome);
        $expenseData = $this->calculateChange($currentMonthExpense, $lastMonthExpense, true);

        $savings = $currentMonthIncome - $currentMonthExpense;

        return [
            $this->createIncomeStat($currentMonthIncome, $incomeData),
            $this->createExpenseStat($currentMonthExpense, $expenseData),
            $this->createSavingsStat($savings),
        ];
    }

    private function getDateRanges(): array
    {
        $now = Carbon::now();
        $currentMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();

        return [
            'current' => [$currentMonthStart, $currentMonthStart->copy()->endOfMonth()],
            'last' => [$lastMonthStart, $lastMonthStart->copy()->endOfMonth()],
        ];
    }

    private function getMonthlySum(string $model, array $dateRange): int
    {
        return (int) $model::query()
            ->whereBetween('payment_date', $dateRange)
            ->sum('amount');
    }

    private function calculateChange(int $current, int $previous, bool $isExpense = false): array
    {
        $change = 0;
        if ($previous > 0) {
            $change = round((($current - $previous) / $previous) * 100, 0);
        } elseif ($current > 0) {
            $change = 100;
        }

        // For expenses, lower is better; for income, higher is better
        $isPositive = $isExpense ? $change <= 0 : $change >= 0;

        return [
            'change' => $change,
            'color' => $isPositive ? 'success' : 'danger',
            'previous' => $previous,
        ];
    }

    private function createIncomeStat(int $currentIncome, array $incomeData): Stat
    {
        return Stat::make('Bevételek változása', Number::currency($currentIncome, 'HUF', 'hu', 0))
            ->description($incomeData['change'] >= 0
                ? ':percent% növekedés az előző hónaphoz képest'
                : ':percent% csökkenés az előző hónaphoz képest',
                ['percent' => abs($incomeData['change'])]
            )
            ->descriptionIcon($incomeData['change'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($incomeData['color'])
            ->chart([
                $incomeData['previous'] / 1000,
                $currentIncome / 1000,
            ]);
    }

    private function createExpenseStat(int $currentExpense, array $expenseData): Stat
    {
        return Stat::make('Kiadások változása', Number::currency($currentExpense, 'HUF', 'hu', 0))
            ->description($expenseData['change'] <= 0
                ? ':percent% csökkenés az előző hónaphoz képest'
                : ':percent% növekedés az előző hónaphoz képest',
                ['percent' => abs($expenseData['change'])]
            )
            ->descriptionIcon($expenseData['change'] <= 0 ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up')
            ->color($expenseData['color'])
            ->chart([
                $expenseData['previous'] / 1000,
                $currentExpense / 1000,
            ]);
    }

    private function createSavingsStat(int $savings): Stat
    {
        return Stat::make('Megtakarítás', Number::currency($savings, 'HUF', 'hu', 0))
            ->description(Carbon::now()->translatedFormat('F'))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color($savings >= 0 ? 'success' : 'danger');
    }
}
