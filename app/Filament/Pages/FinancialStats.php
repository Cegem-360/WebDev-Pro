<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\PaymentStatuses;
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

    public function getMonthlyIncome()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Income::getDateBetweenIncome($currentMonth, $currentMonth->copy()->endOfMonth());
    }

    public function getMonthlyExpense()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        return Expense::getDateBetweenExpense($currentMonth, $currentMonth->copy()->endOfMonth());
    }

    public function getYearlyIncome()
    {
        $currentYear = Carbon::now()->startOfYear();

        return Income::getDateBetweenIncome($currentYear, $currentYear->copy()->endOfYear());
    }

    public function getYearlyExpense()
    {
        $currentYear = Carbon::now()->startOfYear();

        return Expense::getDateBetweenExpense($currentYear, $currentYear->copy()->endOfYear());
    }

    public function getMonthlyIncomesByCategory(): Collection
    {
        $currentMonth = Carbon::now()->copy()->startOfMonth();

        return Income::query()
            ->whereBetween('payment_date', [$currentMonth, $currentMonth->copy()->endOfMonth()])
            ->whereStatus(PaymentStatuses::PAID)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($incomes, $categoryName) {
                return [
                    'category' => $categoryName,
                    'total' => $incomes->sum('amount'),
                ];
            })
            ->values();
    }

    public function getMonthlyExpensesByCategory(): Collection
    {
        $currentMonth = Carbon::now()->copy()->startOfMonth();

        return Expense::query()
            ->whereBetween('payment_date', [$currentMonth, $currentMonth->copy()->endOfMonth()])
            ->whereStatus(PaymentStatuses::PAID)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($incomes, $categoryName) {
                return [
                    'category' => $categoryName,
                    'total' => $incomes->sum('amount'),
                ];
            })
            ->values();
    }

    public function getIncomeExpenseByMonth(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->copy()->subMonths($i);
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            $monthName = $month->copy()->translatedFormat('F');

            $monthlyIncome = Income::getDateBetweenIncome($startOfMonth, $endOfMonth);

            $monthlyExpense = Expense::getDateBetweenExpense($startOfMonth, $endOfMonth);

            $months[] = $monthName;
            $incomes[$monthName] = $monthlyIncome;
            $expenses[$monthName] = $monthlyExpense;
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

        ];
    }

    /**
     * @return array<class-string<Widget>>
     */
    protected function getFooterWidgets(): array
    {
        return [

        ];
    }
}
