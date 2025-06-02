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
        $currentMonthStart = Carbon::now()->copy()->startOfMonth();
        $lastMonth = Carbon::now()->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $lastMonth->copy()->endOfMonth();

        // Current month data
        $currentMonthIncome = Income::getDateBetweenIncome($currentMonthStart, $currentMonthStart->copy()->endOfMonth());

        $currentMonthExpense = Expense::getDateBetweenExpense($currentMonthStart, $currentMonthStart->copy()->endOfMonth());

        // Previous month data
        $lastMonthIncome = Income::getDateBetweenIncome($lastMonth, $lastMonthEnd);
        $lastMonthExpense = Expense::getDateBetweenExpense($lastMonth, $lastMonthEnd);

        // Calculate percentage changes with better handling of edge cases
        $incomeChange = 0;
        if ($lastMonthIncome > 0) {
            $incomeChange = round((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 0);
        } elseif ($currentMonthIncome > 0) {
            $incomeChange = 100; // New income when there was none before
        }

        $expenseChange = 0;
        if ($lastMonthExpense > 0) {
            $expenseChange = round((($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 0);
        } elseif ($currentMonthExpense > 0) {
            $expenseChange = 100; // New expenses when there were none before
        }

        // Calculate colors based on financial perspective
        $incomeColor = $incomeChange >= 0 ? 'success' : 'danger';
        $expenseColor = $expenseChange <= 0 ? 'success' : 'danger';
        dump($currentMonthExpense);

        return [
            Stat::make(__('Bevételek változása'), Number::currency($currentMonthIncome, 'HUF', 'hu', 0))
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

            Stat::make(__('Kiadások változása'), Number::currency($currentMonthExpense, 'HUF', 'hu', 0))
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

            Stat::make(__('Megtakarítás'), Number::currency($currentMonthIncome - $currentMonthExpense, 'HUF', 'hu', 0))
                ->description(Carbon::now()->translatedFormat('F'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color(($currentMonthIncome - $currentMonthExpense) >= 0 ? 'success' : 'danger'),
        ];
    }
}
