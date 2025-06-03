<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Dashboard;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

final class ExpensesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Kiadások';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        /* $paymentType = $this->getData()['payment_type'] ?? null; */
        $paymentType = PaymentTypes::SINGLE;
        $heading = 'Kiadások';

        if ($paymentType) {
            $heading .= ' ('.($paymentType === PaymentTypes::RECURRING ? 'Ismétlődő' : 'Egyszeri').')';
        }

        return $heading;
    }

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
        /*  $paymentType = $this->getData()['payment_type'] ?? null; */
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

                $query = Expense::query()
                    ->whereDay('payment_date', $date)
                    ->whereStatus(PaymentStatuses::PAID);

                // Apply payment type filter if specified
                if ($paymentType) {
                    $query->where('payment_type', $paymentType);
                }

                $dayExpense = $query->sum('amount');

                $amounts->push($dayExpense);
            }
        }

        return [
            'days' => $days->toArray(),
            'amounts' => $amounts->toArray(),
        ];
    }
}
