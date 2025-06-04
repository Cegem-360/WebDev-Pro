<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Dashboard;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

final class ExpensesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Kiadások';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {

        $paymentType = null;
        if (isset($this->filters['payment_type'])) {
            $paymentType = PaymentTypes::tryFrom($this->filters['payment_type']);
        }

        $heading = 'Kiadások '.match ($paymentType) {
            PaymentTypes::RECURRING => '(Ismétlődő)',
            PaymentTypes::SINGLE => '(Egyszeri)',
            default => '',
        };

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

        // Get data for the current month by day
        $currentMonth = Carbon::now()->copy()->startOfMonth();
        $endOfMonth = Carbon::now()->copy()->endOfMonth();
        $daysInMonth = $endOfMonth->day;
        $paymentType = $this->filters['payment_type'] ?? null;

        $status = $this->filters['payment_status'] ?? PaymentStatuses::PAID;
        // Create all days in current month
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate(
                year: $currentMonth->year,
                month: $currentMonth->month,
                day: $day
            );

            $days->push($day);

            $query = Expense::query()
                ->whereDay('payment_date', $date)
                ->when($paymentType, fn (Builder $query) => $query->wherePaymentType($paymentType))
                ->when($status, fn (Builder $query) => $query->whereStatus($status));

            $dayIncome = $query->sumAmount();

            $amounts->push($dayIncome);

        }

        return [
            'days' => $days->toArray(),
            'amounts' => $amounts->toArray(),
        ];
    }
}
