<?php

declare(strict_types=1);

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
}
