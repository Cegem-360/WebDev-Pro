<x-filament-panels::page class="py-6">
    <div class="mb-10">
        <h2 class="text-3xl font-bold text-center sm:text-left">{{ Carbon\Carbon::now()->copy()->year }}. évi heti
            pénzügyi jelentés</h2>
    </div>

    <!-- Keresési és szűrési eszközök -->
    <div class="mb-10 flex flex-col sm:flex-row gap-6">
        <x-filament::input.wrapper class="w-full sm:w-1/2">
            <x-filament::input type="number" wire:model.live="search" placeholder="Keresés hét száma szerint..."
                class="w-full text-lg py-2" />
        </x-filament::input.wrapper>

        <div class="w-full sm:w-1/2">
            <select wire:model.live="selectedMonth"
                class="w-full h-12 text-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <option value="all">Összes hónap</option>
                <option value="1">Január</option>
                <option value="2">Február</option>
                <option value="3">Március</option>
                <option value="4">Április</option>
                <option value="5">Május</option>
                <option value="6">Június</option>
                <option value="7">Július</option>
                <option value="8">Augusztus</option>
                <option value="9">Szeptember</option>
                <option value="10">Október</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        </div>
    </div>

    <!-- Összesített adatok -->
    <div class="mb-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
            class="rounded-xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl hover:shadow-green-900/20 hover:border-gray-300 dark:hover:border-gray-700 transition-all">
            <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Összes bevétel</div>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                {{ Number::currency($totalIncome, 'HUF', 'hu', 0) }}</div>
        </div>

        <div
            class="rounded-xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl hover:shadow-red-900/20 hover:border-gray-300 dark:hover:border-gray-700 transition-all">
            <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Összes kiadás</div>
            <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                {{ Number::currency($totalExpense, 'HUF', 'hu', 0) }}</div>
        </div>

        <div
            class="rounded-xl p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 shadow-xl hover:shadow-blue-900/20 hover:border-gray-300 dark:hover:border-gray-700 transition-all">
            <div class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">Egyenleg összesen</div>
            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                {{ Number::currency($totalBalance, 'HUF', 'hu', 0) }}</div>
        </div>
    </div>

    <!-- Aktuális hét kiemelve -->
    @foreach ($filteredWeeks as $week)
        @if ($week['isCurrent'])
            <div class="mb-8">
                <h3 class="text-xl font-semibold mb-4">Aktuális hét</h3>
                <div
                    class="rounded-lg p-6 bg-primary-100/80 dark:bg-primary-950/50 border border-primary-300 dark:border-primary-600 shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-2xl font-bold text-primary-700 dark:text-primary-400">
                            {{ $week['weekNumber'] }}. hét</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($week['startDate'])->format('Y-m-d') }} -
                            {{ \Carbon\Carbon::parse($week['endDate'])->format('Y-m-d') }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-md bg-white/80 dark:bg-gray-900/70 p-4 shadow-sm">
                            <div class="text-green-700 dark:text-green-400 font-medium mb-1">Bevétel</div>
                            <div class="text-2xl font-bold text-green-700 dark:text-green-400">
                                {{ Number::currency($week['income'], 'HUF', 'hu', 0) }}</div>
                        </div>

                        <div class="rounded-md bg-white/80 dark:bg-gray-900/70 p-4 shadow-sm">
                            <div class="text-red-700 dark:text-red-400 font-medium mb-1">Kiadás</div>
                            <div class="text-2xl font-bold text-red-700 dark:text-red-400">
                                {{ Number::currency($week['expense'], 'HUF', 'hu', 0) }}</div>
                        </div>

                        <div class="rounded-md bg-white/80 dark:bg-gray-900/70 p-4 shadow-sm">
                            <div class="text-blue-700 dark:text-blue-400 font-medium mb-1">Egyenleg</div>
                            <div class="text-2xl font-bold text-blue-700 dark:text-blue-400">
                                {{ Number::currency($week['balance'], 'HUF', 'hu', 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Összes hét listázása -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-semibold">Heti kimutatások</h3>
        <div class="text-sm text-gray-400">
            {{ count($filteredWeeks) }} hét / {{ count($weeks) }} összesen
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($filteredWeeks as $week)
            <div
                class="rounded-lg p-6 {{ $week['isCurrent'] ? 'bg-primary-900/20 dark:bg-primary-950/30 border-primary-500 dark:border-primary-600' : 'bg-gray-100 dark:bg-gray-900' }} border border-gray-300 dark:border-gray-800 shadow-xl hover:border-gray-400 dark:hover:border-gray-600 transition-all">
                <div class="flex justify-between items-center mb-3">
                    <div
                        class="text-xl font-bold {{ $week['isCurrent'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-900 dark:text-white' }}">
                        {{ $week['weekNumber'] }}. hét</div>
                    @if ($week['isCurrent'])
                        <div
                            class="px-2 py-1 rounded bg-primary-500/30 dark:bg-primary-700/50 text-xs text-primary-700 dark:text-primary-300 font-medium">
                            Aktuális
                        </div>
                    @endif
                </div>

                <div class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                    {{ \Carbon\Carbon::parse($week['startDate'])->format('Y-m-d') }} -
                    {{ \Carbon\Carbon::parse($week['endDate'])->format('Y-m-d') }}
                </div>

                <div class="mb-2">
                    <div class="text-green-700 dark:text-green-400 font-medium">Bevétel:
                        {{ Number::currency($week['income'], 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="text-red-700 dark:text-red-400 font-medium">Kiadás:
                        {{ Number::currency($week['expense'], 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-300 dark:border-gray-700">
                    <div class="text-blue-700 dark:text-blue-400 font-bold">Egyenleg:
                        {{ Number::currency($week['balance'], 'HUF', 'hu', 0) }}
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-4 p-12 text-center bg-gray-100/80 dark:bg-gray-950/50 border border-gray-300 dark:border-gray-800 rounded-xl shadow-lg">
                <div class="text-gray-700 dark:text-gray-400 text-lg mb-3">Nincs találat a keresési feltételeknek
                    megfelelően</div>
                <button wire:click="$set('search', '')"
                    class="px-4 py-2 bg-primary-600/20 dark:bg-primary-800/50 hover:bg-primary-500/30 dark:hover:bg-primary-700/50 text-primary-800 dark:text-primary-300 rounded-lg transition-all">
                    Összes hét mutatása
                </button>
            </div>
        @endforelse
    </div>

    {{--   <!-- Lapozás ha túl sok adat lenne -->
    @if (count($filteredWeeks) > 50)
        <div class="mt-8 flex justify-center">
            {{ $this->paginator() }}
        </div>
    @endif --}}
</x-filament-panels::page>
