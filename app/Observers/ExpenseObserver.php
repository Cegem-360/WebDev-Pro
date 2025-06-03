<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\PaymentTypes;
use App\Models\Expense;

final class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        if ($expense->recurring_times <= 1 || $expense->payment_type !== PaymentTypes::RECURRING) {
            // If the expense is not recurring, we can just return
            return;
        }
        $recurringData = $expense->toArray();
        unset($recurringData['id']);
        $recurringData['recurring_times'] = 1;

        for ($i = 1; $i < $expense->recurring_times; $i++) {

            $recurringData['payment_date'] = $expense->payment_date->copy()->addMonths($i)->format('Y-m-d');
            Expense::create($recurringData);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
        //
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
        //
    }
}
