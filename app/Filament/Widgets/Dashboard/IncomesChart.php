<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Dashboard;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

final class IncomesChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Bevételek';

    protected int|string|array $columnSpan = 2;

    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        $paymentType = null;
        if (isset($this->filters['payment_type'])) {
            $paymentType = PaymentTypes::tryFrom($this->filters['payment_type']);
        }

        $heading = 'Bevételek '.match ($paymentType) {
            PaymentTypes::RECURRING => '(Ismétlődő)',
            PaymentTypes::SINGLE => '(Egyszeri)',
            default => '',
        };

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

            $query = Income::query()
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
