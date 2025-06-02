<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\PaymentStatuses;
use App\Filament\Widgets\CategoryDistributionChart;
use App\Filament\Widgets\ExpensesChart;
use App\Filament\Widgets\IncomesChart;
use App\Filament\Widgets\MonthlyComparison;
use App\Filament\Widgets\StatsOverview;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

final class FinancialStats extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Statisztikák';

    protected static ?int $navigationSort = 3;

    /**
     * @var view-string
     */
    protected static string $view = 'filament.pages.financial-stats';

    public function getTitle(): string|Htmlable
    {
        return __('Pénzügyi Statisztikák');
    }

    public function getMonthlyIncome(): int
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Income::query()
            ->whereMonth('payment_date', '=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');
    }

    public function getMonthlyExpense(): int
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Expense::query()
            ->whereMonth('payment_date', '=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');
    }

    public function getYearlyIncome(): int
    {
        $currentYear = Carbon::now()->startOfYear();

        return Income::query()
            ->whereYear('payment_date', '=', $currentYear)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');
    }

    public function getYearlyExpense(): int
    {
        $currentYear = Carbon::now()->startOfYear();

        return Expense::query()
            ->whereYear('payment_date', '=', $currentYear)
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount');
    }

    public function getMonthlyIncomesByCategory(): Collection
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Income::query()
            ->whereMonth('payment_date', '=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->groupBy('categories')
            ->orderBy('amount', 'desc')
            ->get();
    }

    public function getMonthlyExpensesByCategory(): Collection
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Expense::query()
            ->whereMonth('payment_date', '=', $currentMonth)
            ->whereStatus(PaymentStatuses::PAID)
            ->groupBy('categories')
            ->orderBy('amount', 'desc')
            ->get();
    }

    public function getIncomeExpenseByMonth(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            $monthName = $month->translatedFormat('F');

            $monthlyIncome = Income::query()
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereStatus(PaymentStatuses::PAID)
                ->sum('amount');

            $monthlyExpense = Expense::query()
                ->whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                ->whereStatus(PaymentStatuses::PAID)
                ->sum('amount');

            $months[] = $monthName;
            $incomes[] = $monthlyIncome;
            $expenses[] = $monthlyExpense;
        }

        return [
            'months' => $months,
            'incomes' => $incomes,
            'expenses' => $expenses,
        ];
    }

    /**
     * @return array<class-string<Widget>>
     */
    protected function getHeaderWidgets(): array
    {
        return [
            //  StatsOverview::class,
            //  MonthlyComparison::class,
        ];
    }

    /**
     * @return array<class-string<Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [
            // ExpensesChart::class,
            // IncomesChart::class,
            // CategoryDistributionChart::class,
        ];
    }
}
