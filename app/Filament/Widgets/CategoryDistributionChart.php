<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\TrendValue;

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

        $heading .= ' ('.$paymentType.')';

        return $heading;
    }

    protected function getData(): array
    {
        $paymentType = $this->filters['payment_type'] ?? null;

        $data['expenses'] = Category::query()
            ->whereHas('expenses', function ($query) use ($paymentType) {
                $query->whereStatus(PaymentStatuses::PAID)
                    ->whereBetween('payment_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }
            })
            ->withSum(['expenses' => function ($query) use ($paymentType) {
                $query->whereStatus(PaymentStatuses::PAID)
                    ->whereBetween('payment_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }
            }], 'amount')
            ->get()
            ->map(fn (Category $category) => new TrendValue(
                date: $category->name,
                aggregate: $category->expenses_sum_amount ?? 0
            ));

        $data['incomes'] = Category::query()
            ->whereHas('incomes', function ($query) use ($paymentType) {
                $query->whereStatus(PaymentStatuses::PAID)
                    ->whereBetween('payment_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }
            })
            ->withSum(['incomes' => function ($query) use ($paymentType) {
                $query->whereStatus(PaymentStatuses::PAID)
                    ->whereBetween('payment_date', [
                        Carbon::now()->startOfMonth(),
                        Carbon::now()->endOfMonth(),
                    ]);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }
            }], 'amount')
            ->get()
            ->map(fn (Category $category) => new TrendValue(
                date: $category->name,
                aggregate: $category->incomes_sum_amount ?? 0
            ));

        return [
            'datasets' => [
                [
                    'label' => __('Kiadások'),
                    'data' => $data['expenses']->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        '#EF4444', '#F87171', '#FCA5A5', '#FECACA', '#FEE2E2',
                        '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D', '#F87171',
                    ],
                ],
                [
                    'label' => __('Bevételek'),
                    'data' => $data['incomes']->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => [
                        '#10B981', '#059669', '#047857', '#065F46', '#064E3B',
                        '#047C3F', '#166534', '#15803D', '#16A34A', '#22C55E',
                    ],
                ],
            ],
            'labels' => Category::all()
                ->pluck('name')
                ->toArray(),
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

    private function getCategoryData()
    {
        $currentMonth = Carbon::now()->startOfMonth();

        // Get expense distribution by category
        /* $expenseByCategories = Expense::query()
            ->whereBetween('payment_date', [$currentMonth, $currentMonth->copy()->endOfMonth()])
            ->whereStatus(PaymentStatuses::PAID)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($incomes, $categoryName) {
                return [
                    'category' => $categoryName,
                    'total' => $incomes->sumAmount(),
                ];
            })
            ->values();

        // Get income distribution by category
        $incomeByCategories = Income::query()
            ->whereBetween('payment_date', [$currentMonth, $currentMonth->copy()->endOfMonth()])
            ->whereStatus(PaymentStatuses::PAID)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function ($incomes, $categoryName) {
                return [
                    'category' => $categoryName,
                    'total' => $incomes->sumAmount(),
                ];
            })
            ->values(); */
        /*
                $categoryNames = collect([
                    ...$expenseByCategories->pluck('category'),
                    ...$incomeByCategories->pluck('category'),
                ])->unique()->values()->toArray();
                dump($expenseByCategories);
         */

        /* return [
            'categories' => $categoryNames,
            'expense_amounts' => $expenseByCategories->pluck('total')->toArray(),
            'income_amounts' => $incomeByCategories->pluck('total')->toArray(),
        ]; */
    }
}
