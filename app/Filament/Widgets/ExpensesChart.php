<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

final class ExpensesChart extends ChartWidget
{
    protected static ?string $heading = 'Kiadások';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getExpenseData();

        return [
            'datasets' => [
                [
                    'label' => __('Expenses'),
                    'data' => $data['amounts'],
                    'backgroundColor' => '#EF4444',
                    'borderColor' => '#DC2626',
                ],
            ],
            'labels' => $data['days'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getExpenseData(): array
    {
        $days = collect();
        $amounts = collect();

        // Get data for the current month by day
        $currentMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $daysInMonth = $endOfMonth->day;

        // Create all days in current month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate(
                $currentMonth->year,
                $currentMonth->month,
                $day
            );

            if ($date->lessThanOrEqualTo(Carbon::now())) {
                $days->push($date->day);

                $dayExpense = Expense::query()
                    ->wherePaymentDate($date)
                    ->whereStatus(PaymentStatuses::PAID)
                    ->sum('amount');

                $amounts->push($dayExpense);
            }
        }

        return [
            'days' => $days->toArray(),
            'amounts' => $amounts->toArray(),
        ];
    }
}
