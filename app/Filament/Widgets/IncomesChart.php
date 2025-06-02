<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatuses;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

final class IncomesChart extends ChartWidget
{
    protected static ?string $heading = 'Bevételek';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getIncomeData();

        return [
            'datasets' => [
                [
                    'label' => __('Bevételek'),
                    'data' => $data['amounts'],
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $data['days'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    private function getIncomeData(): array
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

                $dayIncome = Income::query()
                    ->wherePaymentDate($date)
                    ->whereStatus(PaymentStatuses::PAID)
                    ->sum('amount');

                $amounts->push($dayIncome);
            }
        }

        return [
            'days' => $days->toArray(),
            'amounts' => $amounts->toArray(),
        ];
    }
}
