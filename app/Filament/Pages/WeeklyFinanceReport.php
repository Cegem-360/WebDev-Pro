<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\PaymentStatuses;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

final class WeeklyFinanceReport extends Page
{
    use WithPagination;

    public $weeks = [];

    #[Url(keep: true)]
    public $search = 0;

    public Collection $filteredWeeks;

    public $selectedMonth = 'all';

    public $totalIncome = 0;

    public $totalExpense = 0;

    public $totalBalance = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedMonth' => ['except' => 'all'],
    ];

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Riportok';

    protected static ?string $navigationLabel = 'Heti pénzügyi jelentés';

    protected static ?string $title = 'Heti pénzügyi jelentés';

    protected static string $view = 'Filament.pages.weekly-finance-report';

    public function mount(): void
    {
        $this->loadWeeklyData();
    }

    public function updatedSearch(): void
    {
        $this->applySearch();
    }

    public function updatedSelectedMonth(): void
    {
        $this->applySearch();
    }

    protected function loadWeeklyData(): void
    {
        // Az aktuális év első és utolsó napja
        $startOfYear = Carbon::now()->copy()->startOfYear();
        $endOfYear = Carbon::now()->copy()->endOfYear();
        $currentWeek = Carbon::now()->copy()->weekOfYear;

        // A hetek pontos számának meghatározása az évben
        $year = Carbon::now()->copy()->year;
        $weeksInYear = Carbon::create($year, 12, 28)->weekOfYear; // december 28. mindig az év utolsó hetében van

        // Végigmegyünk az év minden hetén
        for ($weekNumber = 1; $weekNumber <= $weeksInYear; $weekNumber++) {
            // Az adott hét kezdő és záró dátuma
            $startOfWeek = Carbon::now()->copy()->setISODate(Carbon::now()->copy()->year, $weekNumber)->startOfWeek();
            $endOfWeek = $startOfWeek->copy()->endOfWeek();

            // Heti bevétel, kiadás és egyenleg lekérése az adatbázisból
            /*   $weeklyIncome = Income::getDateBetweenIncome($startOfWeek, $endOfWeek); */
            $weeklyIncome = Income::whereBetween('payment_date', [$startOfWeek, $endOfWeek])
                ->whereStatus(PaymentStatuses::PAID)
                ->sum('amount');

            $weeklyExpense = Expense::getDateBetweenExpense($startOfWeek, $endOfWeek);

            $weeklyBalance = $weeklyIncome - $weeklyExpense;

            $this->weeks[] = [
                'weekNumber' => $weekNumber,
                'startDate' => $startOfWeek,
                'endDate' => $endOfWeek,
                'income' => $weeklyIncome,
                'expense' => $weeklyExpense,
                'balance' => $weeklyBalance,
                'isCurrent' => ($weekNumber === $currentWeek),

            ];
        }

        $this->applySearch();
    }

    protected function applySearch(): void
    {
        $this->filteredWeeks = collect($this->weeks);
        // Szűrés keresés alapján
        if ($this->search > 0) {
            $searchTerm = (int) $this->search;

            $this->filteredWeeks = $this->filteredWeeks->filter(function ($week) use ($searchTerm) {
                return $week['weekNumber'] === $searchTerm;
            });
        }

        // Szűrés hónap alapján
        if ($this->selectedMonth !== 'all') {
            $month = (int) $this->selectedMonth;
            $this->filteredWeeks = $this->filteredWeeks->filter(function ($week) use ($month) {
                $weekStartDate = Carbon::parse($week['startDate']);
                $weekEndDate = Carbon::parse($week['endDate']);

                return $weekStartDate->month === $month || $weekEndDate->month === $month;
            });
        }
        // Összesítés kiszámolása
        $this->totalIncome = $this->filteredWeeks->sum('income');
        $this->totalExpense = $this->filteredWeeks->sum('expense');
        $this->totalBalance = $this->totalIncome - $this->totalExpense;

        // Átalakítás tömbbé
        $this->filteredWeeks = $this->filteredWeeks;
    }
}
