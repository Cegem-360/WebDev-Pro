<x-filament-panels::page class="fi-dashboard-page" :fullHeight="true" :title="$this->getTitle()">

    {{ $this->filtersForm }}

    <x-filament-widgets::widgets :columns="$this->getColumns()" :data="[...property_exists($this, 'filters') ? ['filters' => $this->filters] : [], ...$this->getWidgetData()]" :widgets="$this->getVisibleWidgets()" />
</x-filament-panels::page>
