<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

final class CategoryDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Kategória Eloszlás';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getCategoryData();

        return [
            'datasets' => [
                [
                    'label' => __('Kiadások'),
                    'data' => $data['expense_amounts'],
                    'backgroundColor' => [
                        '#EF4444', '#F87171', '#FCA5A5', '#FECACA', '#FEE2E2',
                        '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D', '#F87171',
                    ],
                ],
                [
                    'label' => __('Bevételek'),
                    'data' => $data['income_amounts'],
                    'backgroundColor' => [
                        '#10B981', '#059669', '#047857', '#065F46', '#064E3B',
                        '#047C3F', '#166534', '#15803D', '#16A34A', '#22C55E',
                    ],
                ],
            ],
            'labels' => $data['categories'],
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

    private function getCategoryData(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // Get expense distribution by category
        $expenseByCategories = Expense::query()
            ->wherePaymentDate('>=', $currentMonth)
            ->where('status', PaymentStatuses::PAID)
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get();

        // Get income distribution by category
        $incomeByCategories = Income::query()
            ->wherePaymentDate('>=', $currentMonth)
            ->where('status', PaymentStatuses::PAID)
            ->groupBy('category_id')
            ->orderBy('amount', 'desc')
            ->limit(10)
            ->get();

        $categoryNames = collect([
            ...$expenseByCategories->pluck('name'),
            ...$incomeByCategories->pluck('name'),
        ])->unique()->values()->toArray();

        $expenseAmounts = [];
        foreach ($categoryNames as $category) {
            $expenseTotal = $expenseByCategories->firstWhere('name', $category)?->total ?? 0;
            $expenseAmounts[] = $expenseTotal;
        }

        $incomeAmounts = [];
        foreach ($categoryNames as $category) {
            $incomeTotal = $incomeByCategories->firstWhere('name', $category)?->total ?? 0;
            $incomeAmounts[] = $incomeTotal;
        }

        return [
            'categories' => $categoryNames,
            'expense_amounts' => $expenseAmounts,
            'income_amounts' => $incomeAmounts,
        ];
    }
}
