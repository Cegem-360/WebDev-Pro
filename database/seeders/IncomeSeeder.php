<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Income;
use Illuminate\Database\Seeder;

final class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Income::factory()->count(10)->recycle(Category::factory()->create())->create(['payment_date' => now()]);
    }
}
