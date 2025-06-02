<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

final class WeeklyReportDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Létrehozunk egy kategóriát a bevételekhez és kiadásokhoz
        $incomeCategory = Category::factory()->create(['name' => 'Demo bevétel']);
        $expenseCategory = Category::factory()->create(['name' => 'Demo kiadás']);

        // Beállítjuk az aktuális évet és a hetek számát
        $today = Carbon::now();
        $currentYear = $today->year;
        $currentWeek = $today->weekOfYear;
        $weeksInYear = Carbon::create($currentYear, 12, 31)->weekOfYear;

        // A képen látható első 4 hét adatai
        $firstFourWeeks = [
            1 => [
                'income' => 600000,
                'expense' => 1110000,
                'balance' => -510000,
            ],
            2 => [
                'income' => 125000,
                'expense' => 25000,
                'balance' => 100000,
            ],
            3 => [
                'income' => 460000,
                'expense' => 140000,
                'balance' => 320000,
            ],
            4 => [
                'income' => 95000,
                'expense' => 0,
                'balance' => 95000,
            ],
        ];

        // Generáljunk adatokat az év minden hetére
        $demoData = [];

        for ($weekNumber = 1; $weekNumber <= $weeksInYear; $weekNumber++) {
            if (isset($firstFourWeeks[$weekNumber])) {
                // Az első 4 héthez használjuk a megadott adatokat
                $demoData[$weekNumber] = $firstFourWeeks[$weekNumber];
            } else {
                // A többi héthez generálunk random adatokat
                $income = rand(50000, 800000);
                $expense = rand(0, 500000);
                $balance = $income - $expense;

                $demoData[$weekNumber] = [
                    'income' => $income,
                    'expense' => $expense,
                    'balance' => $balance,
                ];
            }
        }

        // Létrehozunk minden hétre megfelelő adatokat
        foreach ($demoData as $weekNumber => $weekData) {
            $weekStartDate = Carbon::create($currentYear)->setISODate($currentYear, $weekNumber)->startOfWeek();
            $weekEndDate = Carbon::create($currentYear)->setISODate($currentYear, $weekNumber)->endOfWeek();

            // Bevétel létrehozása
            if ($weekData['income'] > 0) {
                Income::create([
                    'category_id' => $incomeCategory->id,
                    'payment_date' => $weekStartDate->copy()->addDays(rand(0, 6)),
                    'description' => $weekNumber.'. heti bevétel',
                    'amount' => $weekData['income'],
                    'payment_type' => PaymentTypes::SINGLE,
                    'status' => PaymentStatuses::PAID,
                ]);
            }

            // Kiadás létrehozása
            if ($weekData['expense'] > 0) {
                Expense::create([
                    'category_id' => $expenseCategory->id,
                    'payment_date' => $weekStartDate->copy()->addDays(rand(0, 6)),
                    'description' => $weekNumber.'. heti kiadás',
                    'amount' => $weekData['expense'],
                    'payment_type' => PaymentTypes::SINGLE,
                    'status' => PaymentStatuses::PAID,
                ]);
            }
        }
    }
}
