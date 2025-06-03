<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\FinancialReportOverview;
use Filament\Pages\Page;

final class FinancialReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string $view = 'filament.pages.financial-report';

    protected static ?string $navigationGroup = 'Riportok';

    protected static ?string $navigationLabel = 'Pénzügyi jelentés';

    protected static ?int $navigationSort = 3;

    protected function getHeaderWidgets(): array
    {
        return [
            FinancialReportOverview::class,
        ];
    }
}
