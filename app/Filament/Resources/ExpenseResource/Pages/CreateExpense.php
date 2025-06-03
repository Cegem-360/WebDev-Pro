<?php

declare(strict_types=1);

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Resources\Pages\CreateRecord;

final class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    /*  protected function handleRecordCreation(array $data): Expense
     {
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
