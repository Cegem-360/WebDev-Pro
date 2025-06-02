<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Database\Seeder;

final class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expense::factory()->count(10)->recycle(Category::factory()->create())->create();
    }
}
