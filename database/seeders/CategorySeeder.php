<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

final class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->create(['name' => 'Technology']);
        Category::factory()->create(['name' => 'Business']);
        Category::factory()->create(['name' => 'Health']);
        Category::factory()->create(['name' => 'Education']);
        Category::factory()->create(['name' => 'Entertainment']);
    }
}
