<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Category;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

final class CategoryDistributionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Kategória Eloszlás';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        $paymentType = $this->filters['payment_type'] ?? null;
        $heading = 'Kategória Eloszlás';

        if ($paymentType) {
            $heading .= ' ('.$paymentType.')';
        }

        return $heading;
    }

    protected function getData(): array
    {
        $paymentType = $this->filters['payment_type'] ?? null;
        $dateRange = $this->getCurrentMonthDateRange();

        // Single query to get all categories with their expenses and incomes sums
        $categories = Category::query()
            ->withSum(['expenses' => fn ($query) => $this->applyCommonFilters($query, $paymentType, $dateRange)], 'amount')
            ->withSum(['incomes' => fn ($query) => $this->applyCommonFilters($query, $paymentType, $dateRange)], 'amount')
            ->get();

        // Prepare data arrays
        $expensesData = [];
        $incomesData = [];
        $labels = [];

        foreach ($categories as $category) {
            $labels[] = $category->name;
            $expensesData[] = $category->expenses_sum_amount ?? 0;
            $incomesData[] = $category->incomes_sum_amount ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => __('Kiadások'),
                    'data' => $expensesData,
                    'backgroundColor' => $this->getExpenseColors(),
                ],
                [
                    'label' => __('Bevételek'),
                    'data' => $incomesData,
                    'backgroundColor' => $this->getIncomeColors(),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }

    private function getCurrentMonthDateRange(): array
    {
        $now = Carbon::now();

        return [$now->startOfMonth()->copy(), $now->endOfMonth()->copy()];
    }

    private function applyCommonFilters($query, ?string $paymentType, array $dateRange)
    {
        return $query->paid()
            ->whereBetween('payment_date', $dateRange)
            ->when($paymentType, fn ($q) => $q->wherePaymentType($paymentType));
    }

    private function getExpenseColors(): array
    {
        return [
            '#EF4444', '#F87171', '#FCA5A5', '#FECACA', '#FEE2E2',
            '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D', '#F87171',
        ];
    }

    private function getIncomeColors(): array
    {
        return [
            '#10B981', '#059669', '#047857', '#065F46', '#064E3B',
            '#047C3F', '#166534', '#15803D', '#16A34A', '#22C55E',
        ];
    }
}
