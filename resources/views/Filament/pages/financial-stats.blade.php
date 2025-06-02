<x-filament-panels::page>
    @php
        $monthlyIncome = $this->getMonthlyIncome();
        $monthlyExpense = $this->getMonthlyExpense();
        $yearlyIncome = $this->getYearlyIncome();
        $yearlyExpense = $this->getYearlyExpense();
        $balance = $monthlyIncome - $monthlyExpense;
        $yearlyBalance = $yearlyIncome - $yearlyExpense;
        $incomeExpenseData = $this->getIncomeExpenseByMonth();
        $incomesByCategory = $this->getMonthlyIncomesByCategory();
        $expensesByCategory = $this->getMonthlyExpensesByCategory();
    @endphp

    <div class="grid gap-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-filament::section>
                <div class="flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Havi bevétel') }}</h3>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-green-600 dark:text-green-400">
                        {{ Number::currency($monthlyIncome, 'HUF', 'hu', 0) }}
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Havi kiadás') }}</h3>
                    <p class="mt-2 text-2xl font-bold tracking-tight text-red-600 dark:text-red-400">
                        {{ Number::currency($monthlyExpense, 'HUF', 'hu', 0) }}
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Havi egyenleg') }}</h3>
                    <p
                        class="mt-2 text-2xl font-bold tracking-tight {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ Number::currency($balance, 'HUF', 'hu', 0) }}
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="flex flex-col">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Éves egyenleg') }}</h3>
                    <p
                        class="mt-2 text-2xl font-bold tracking-tight {{ $yearlyBalance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ Number::currency($yearlyBalance, 'HUF', 'hu', 0) }}
                    </p>
                </div>
            </x-filament::section>
        </div>

        <!-- Income and Expense Chart -->
        <x-filament::section>
            <h2 class="text-xl font-semibold mb-4">{{ __('Bevételek és kiadások alakulása') }}</h2>
            <div class="h-80">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </x-filament::section>

        <!-- Category Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-filament::section>
                <h2 class="text-xl font-semibold mb-4">{{ __('Havi bevételek kategóriánként') }}</h2>
                <div class="h-64">
                    <canvas id="incomeCategoryChart"></canvas>
                </div>

                @if ($incomesByCategory->count() > 0)
                    <div class="mt-4">
                        <ul class="space-y-2">
                            @foreach ($incomesByCategory as $income)
                                <li class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-300">{{ $income->category->name }}</span>
                                    <span
                                        class="font-medium">{{ Number::currency($income->amount, 'HUF', 'hu', 0) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">{{ __('Nincs bevételi adat a jelenlegi hónapban') }}</p>
                    </div>
                @endif
            </x-filament::section>

            <x-filament::section>
                <h2 class="text-xl font-semibold mb-4">{{ __('Havi kiadások kategóriánként') }}</h2>
                <div class="h-64">
                    <canvas id="expenseCategoryChart"></canvas>
                </div>

                @if ($expensesByCategory->count() > 0)
                    <div class="mt-4">
                        <ul class="space-y-2">
                            @foreach ($expensesByCategory as $expense)
                                <li class="flex justify-between">
                                    <span
                                        class="text-gray-600 dark:text-gray-300">{{ $expense->category->name }}</span>
                                    <span
                                        class="font-medium">{{ Number::currency($expense->amount, 'HUF', 'hu', 0) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500">{{ __('Nincs kiadási adat a jelenlegi hónapban') }}</p>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>

    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{--  <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Income and Expense Chart
            const incomeExpenseCtx = document.getElementById('incomeExpenseChart').getContext('2d');
            const incomeExpenseChart = new Chart(incomeExpenseCtx, {
                type: 'line',
                data: {
                    labels: @json($incomeExpenseData['months']),
                    datasets: [{
                            label: '{{ __('Bevétel') }}',
                            data: @json($incomeExpenseData['incomes']),
                            backgroundColor: 'rgba(34, 197, 94, 0.2)',
                            borderColor: 'rgb(34, 197, 94)',
                            tension: 0.2,
                            borderWidth: 2
                        },
                        {
                            label: '{{ __('Kiadás') }}',
                            data: @json($incomeExpenseData['expenses']),
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
                            borderColor: 'rgb(239, 68, 68)',
                            tension: 0.2,
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('hu-HU') + ' Ft';
                                }
                            }
                        }
                    }
                }
            });

            // Income Category Chart
            const incomeCategoryData = @json($incomesByCategory);
            if (incomeCategoryData.length > 0) {
                const incomeCategoryCtx = document.getElementById('incomeCategoryChart').getContext('2d');
                const incomeCategoryChart = new Chart(incomeCategoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: incomeCategoryData.map(item => item.name),
                        datasets: [{
                            data: incomeCategoryData.map(item => item.total),
                            backgroundColor: [
                                '#10B981', '#059669', '#047857', '#065F46', '#064E3B',
                                '#047C3F', '#166534', '#15803D', '#16A34A', '#22C55E'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            // Expense Category Chart
            const expenseCategoryData = @json($expensesByCategory);
            if (expenseCategoryData.length > 0) {
                const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
                const expenseCategoryChart = new Chart(expenseCategoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: expenseCategoryData.map(item => item.name),
                        datasets: [{
                            data: expenseCategoryData.map(item => item.total),
                            backgroundColor: [
                                '#EF4444', '#DC2626', '#B91C1C', '#991B1B', '#7F1D1D',
                                '#F87171', '#FCA5A5', '#FEE2E2', '#FECACA', '#F87171'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script> --}}
</x-filament-panels::page>
