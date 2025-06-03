<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Dashboard;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

final class IncomesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Bevételek';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        /*  $paymentType = $this->getData()['payment_type'] ?? null; */
        $paymentType = PaymentTypes::SINGLE;
        $heading = 'Bevételek';

        if ($paymentType) {
            $heading .= ' ('.($paymentType === PaymentTypes::RECURRING ? 'Ismétlődő' : 'Egyszeri').')';
        }

        return $heading;
    }

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
        /*   $paymentType = $this->getData()['payment_type'] ?? null; */
        $paymentType = PaymentTypes::SINGLE;

        // Get data for the current month by day
        $currentMonth = Carbon::now()->copy()->startOfMonth();
        $endOfMonth = Carbon::now()->copy()->endOfMonth();
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

                $query = Income::query()
                    ->whereDay('payment_date', $date)
                    ->whereStatus(PaymentStatuses::PAID);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }

                $dayIncome = $query->sum('amount');

                $amounts->push($dayIncome);
            }
        }

        return [
            'days' => $days->toArray(),
            'amounts' => $amounts->toArray(),
        ];
    }
}
