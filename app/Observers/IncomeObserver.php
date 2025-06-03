<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\PaymentTypes;
use App\Models\Income;

final class IncomeObserver
{
    /**
     * Handle the Income "created" event.
     */
    public function created(Income $income): void
    {
        if ($income->recurring_times <= 1 || $income->payment_type !== PaymentTypes::RECURRING) {
            // If the income is not recurring, we can just return
            return;
        }
        $recurringData = $income->toArray();
        unset($recurringData['id']);
        $recurringData['recurring_times'] = 1;

        for ($i = 1; $i < $income->recurring_times; $i++) {

            $recurringData['payment_date'] = $income->payment_date->copy()->addMonths($i)->format('Y-m-d');
            Income::create($recurringData);
        }
    }

    /**
     * Handle the Income "updated" event.
     */
    public function updated(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "deleted" event.
     */
    public function deleted(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "restored" event.
     */
    public function restored(Income $income): void
    {
        //
    }

    /**
     * Handle the Income "force deleted" event.
     */
    public function forceDeleted(Income $income): void
    {
        //
    }
}
