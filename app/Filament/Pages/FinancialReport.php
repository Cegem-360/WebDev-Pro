<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\FinancialReport\FinancialReportOverview;
use App\Models\Expense;
use App\Models\Income;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Page;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;

final class FinancialReport extends Page
{
    use HasFiltersForm;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'Filament.pages.financial-report';

    protected static ?string $navigationGroup = 'Riportok';

    protected static ?string $navigationLabel = 'Pénzügyi jelentés';

    protected static ?int $navigationSort = 3;

    public function getTitle(): string|Htmlable
    {
        return __('Financial Report');
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('year')
                    ->label('Év')
                    ->options(function () {
                        // Mindkét modellből pluck-oljuk a payment_date-et, majd évre szűrjük
                        $incomeDates = Income::pluck('payment_date');
                        $expenseDates = Expense::pluck('payment_date');
                        $years = $incomeDates
                            ->merge($expenseDates)
                            ->map(fn ($date) => $date->year)
                            ->unique()
                            ->sortDesc()
                            ->values();

                        return $years->combine($years)->toArray();
                    })
                    ->placeholder('Válassz évet')
                    ->default(now()->year),
                Select::make('month')
                    ->label('Hónap')
                    ->options([
                        1 => 'Január',
                        2 => 'Február',
                        3 => 'Március',
                        4 => 'Április',
                        5 => 'Május',
                        6 => 'Június',
                        7 => 'Július',
                        8 => 'Augusztus',
                        9 => 'Szeptember',
                        10 => 'Október',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->placeholder('Válassz hónapot')
                    ->default(now()->month),
            ])
            ->columns(1);
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            FinancialReportOverview::class,
        ];
    }

    public function getTotalIncome(bool $paid = false): Builder
    {

        return Income::query()
            ->when(
                $this->filters['year'] ?? null,
                fn ($query) => $query->whereYear('payment_date', $this->filters['year'])
            )
            ->when(
                $this->filters['month'] ?? null,
                fn ($query) => $query->whereMonth('payment_date', $this->filters['month'])
            )
            ->when(
                $paid,
                fn ($query) => $query->paid()
            )
            ->when(
                ! $paid,
                fn ($query) => $query->unpaid()
            );
    }

    public function getTotalExpense(bool $paid = false): Builder
    {

        return Expense::query()
            ->when(
                $this->filters['year'] ?? null,
                fn ($query) => $query->whereYear('payment_date', $this->filters['year'])
            )
            ->when(
                $this->filters['month'] ?? null,
                fn ($query) => $query->whereMonth('payment_date', $this->filters['month'])
            )
            ->when(
                $paid,
                fn ($query) => $query->paid()
            )
            ->when(
                ! $paid,
                fn ($query) => $query->unpaid()
            );
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int|string|array
    {
        return 2;
    }
}
