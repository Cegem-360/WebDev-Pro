<?php

declare(strict_types=1);

namespace App\Filament\Resources\IncomeResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

final class ViewIncome extends ViewRecord
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
