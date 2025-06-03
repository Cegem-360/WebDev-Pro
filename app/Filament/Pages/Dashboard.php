<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Support\Facades\FilamentIcon;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Contracts\Support\Htmlable;

final class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?int $navigationSort = -2;

    /**
     * @var view-string
     */
    protected static string $view = 'Filament.pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return self::$navigationLabel ??
            self::$title ??
            __('Filament/pages/dashboard.navigation-label');
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return self::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }

    public static function getRoutePath(): string
    {
        return self::$routePath;
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int|string|array
    {
        return 2;
    }

    /**
     * @return array<string, mixed>
     */
    public function getWidgetData(): array
    {
        return [
            'payment_type' => $this->filters['payment_type'],
            'payment_status' => $this->filters['payment_status'],
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('Filament/pages/dashboard.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    Select::make('payment_type')
                        ->label('Fizetési mód')
                        ->options(PaymentTypes::class)
                        ->placeholder('Összes fizetési mód')
                        ->live()
                        ->afterStateUpdated(function () {
                            $this->dispatch('filter-changed');
                        }),
                    Select::make('payment_status')
                        ->label('Fizetési státusz')
                        ->options(PaymentStatuses::class)
                        ->placeholder('Összes fizetési státusz')
                        ->live()
                        ->afterStateUpdated(function () {
                            $this->dispatch('filter-changed');
                        }),
                ]),
        ];
    }
}
