@use('App\Enums\PaymentStatuses')
@use('App\Models\Expense')
@use('App\Models\Income')

<x-filament-panels::page>
    <div class="space-y-6">
        <div>
            {{ $this->filtersForm }}
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Bevételek összehasonlítása</h2>

                <x-filament-widgets::widgets :columns="$this->getColumns()" :data="[
                    ...property_exists($this, 'filters') ? ['filters' => $this->filters] : [],
                    ...$this->getWidgetData(),
                ]" :widgets="$this->getVisibleWidgets()" />

            </div>

        </div>
</x-filament-panels::page>
