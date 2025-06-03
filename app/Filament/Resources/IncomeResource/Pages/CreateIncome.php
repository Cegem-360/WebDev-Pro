<?php

declare(strict_types=1);

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use App\Models\Income;
use Filament\Resources\Pages\CreateRecord;

final class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;

    /* protected function handleRecordCreation(array $data): Income
    {

      // Default to 1 if recurring_times is not set
        $recurringTimes = $data['recurring_times'] ?? 1;

        // Remove recurring_times as it's not an actual DB column
        $createData = $data;
        if (isset($createData['recurring_times'])) {
            unset($createData['recurring_times']);
        }

        if ($recurringTimes === 1) {
            return self::getModel()::create($createData);
        }

        // Create additional recurring records for subsequent months
        $baseIncome = self::getModel()::create($createData);

        for ($i = 1; $i < $recurringTimes; $i++) {
            $recurringData = $createData;
            $recurringData['payment_date'] = now()->copy()->addMonths($i)->format('Y-m-d');
            self::getModel()::create($recurringData);
        }

        return $baseIncome;
        $tmp = $data['recurring_times'];
        unset($data['recurring_times']);
        if ($tmp === 1) {

            return self::getModel()::create($data);
        }

        // Create additional recurring records for subsequent months
        $baseIncome = self::getModel()::create($data);

        for ($i = 1; $i < $tmp; $i++) {
            $recurringData = $data;
            $recurringData['payment_date'] = now()->copy()->addMonths($i)->format('Y-m-d');
            self::getModel()::create($recurringData);
        }

        return $baseIncome;
    } */
}
