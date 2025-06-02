<x-filament-panels::page>
    <div class="mb-6">
        <h2 class="text-2xl font-bold">{{ Carbon\Carbon::now()->copy()->year }}. évi heti pénzügyi jelentés</h2>
    </div>

    <!-- Keresési és szűrési eszközök -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <x-filament::input.wrapper class="w-full sm:w-1/2">
            <x-filament::input type="number" wire:model.live="search" placeholder="Keresés hét száma szerint..."
                class="w-full" />
        </x-filament::input.wrapper>

        <div class="w-full sm:w-1/2">
            <select wire:model.live="selectedMonth"
                class="w-full h-10 border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
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
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-lg p-5 bg-gray-900 border border-gray-800 shadow-lg">
            <div class="text-lg font-medium text-gray-400">Összes bevétel</div>
            <div class="text-2xl font-bold text-green-500">{{ Number::currency($totalIncome, 'HUF', 'hu', 0) }}</div>
        </div>

        <div class="rounded-lg p-5 bg-gray-900 border border-gray-800 shadow-lg">
            <div class="text-lg font-medium text-gray-400">Összes kiadás</div>
            <div class="text-2xl font-bold text-red-500">{{ Number::currency($totalExpense, 'HUF', 'hu', 0) }}</div>
        </div>

        <div class="rounded-lg p-5 bg-gray-900 border border-gray-800 shadow-lg">
            <div class="text-lg font-medium text-gray-400">Egyenleg összesen</div>
            <div class="text-2xl font-bold text-blue-500">{{ Number::currency($totalBalance, 'HUF', 'hu', 0) }}</div>
        </div>
    </div>

    <!-- Aktuális hét kiemelve -->
    @foreach ($filteredWeeks as $week)
        @if ($week['isCurrent'])
            <div class="mb-6">
                <h3 class="text-xl font-semibold mb-4">Aktuális hét</h3>
                <div class="rounded-lg p-6 bg-primary-950/50 border border-primary-600 shadow-lg">
                    <div class="flex justify-between items-center mb-3">
                        <div class="text-xl font-bold text-primary-400">{{ $week['weekNumber'] }}. hét</div>
                        <div class="text-sm text-gray-400">{{ $week['startDate'] }} - {{ $week['endDate'] }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded-md bg-gray-900/50 p-4">
                            <div class="text-green-500 font-medium">Bevétel</div>
                            <div class="text-2xl font-bold text-green-400">
                                {{ Number::currency($week['income'], 'HUF', 'hu', 0) }}</div>
                        </div>

                        <div class="rounded-md bg-gray-900/50 p-4">
                            <div class="text-red-500 font-medium">Kiadás</div>
                            <div class="text-2xl font-bold text-red-400">
                                {{ Number::currency($week['expense'], 'HUF', 'hu', 0) }}</div>
                        </div>

                        <div class="rounded-md bg-gray-900/50 p-4">
                            <div class="text-blue-500 font-medium">Egyenleg</div>
                            <div class="text-2xl font-bold text-blue-400">
                                {{ Number::currency($week['balance'], 'HUF', 'hu', 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Összes hét listázása -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-semibold">Heti kimutatások</h3>
        <div class="text-sm text-gray-400">
            {{ count($filteredWeeks) }} hét / {{ count($weeks) }} összesen
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($filteredWeeks as $week)
            <div
                class="rounded-lg p-5 {{ $week['isCurrent'] ? 'bg-primary-950/30 border-primary-600' : 'bg-gray-950' }} border border-gray-800 shadow-lg hover:border-gray-600 transition-all">
                <div class="flex justify-between items-center mb-2">
                    <div class="text-lg font-bold {{ $week['isCurrent'] ? 'text-primary-400' : 'text-white' }}">
                        {{ $week['weekNumber'] }}. hét</div>
                    @if ($week['isCurrent'])
                        <div class="px-2 py-1 rounded bg-primary-700/50 text-xs text-primary-300 font-medium">Aktuális
                        </div>
                    @endif
                </div>

                <div class="text-xs text-gray-400 mb-3">{{ $week['startDate'] }} - {{ $week['endDate'] }}</div>

                <div class="mb-2">
                    <div class="text-green-500 font-medium">Bevétel:
                        {{ Number::currency($week['income'], 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="text-red-500 font-medium">Kiadás:
                        {{ Number::currency($week['expense'], 'HUF', 'hu', 0) }}
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-700">
                    <div class="text-blue-500 font-bold">Egyenleg:
                        {{ Number::currency($week['balance'], 'HUF', 'hu', 0) }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 p-8 text-center">
                <div class="text-gray-500 mb-2">Nincs találat a keresési feltételeknek megfelelően</div>
                <button wire:click="$set('search', '')" class="text-primary-500 hover:text-primary-400">
                    Összes hét mutatása
                </button>
            </div>
        @endforelse
    </div>

    {{--   <!-- Lapozás ha túl sok adat lenne -->
    @if (count($filteredWeeks) > 50)
        <div class="mt-6 flex justify-center">
            {{ $this->paginator() }}
        </div>
    @endif --}}
</x-filament-panels::page>
