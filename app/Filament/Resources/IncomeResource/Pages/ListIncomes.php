<?php

declare(strict_types=1);

namespace App\Filament\Resources\IncomeResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

final class ListIncomes extends ListRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
