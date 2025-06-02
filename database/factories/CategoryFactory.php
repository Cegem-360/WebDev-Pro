<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\BudgetItemTypes;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categoryName = $this->faker->randomElement(
            [
                'Food', 'Transport', 'Entertainment', 'Utilities', 'Health', 'Education',
                'Clothing', 'Housing', 'Miscellaneous',
            ]
        );
        $budgetItemType = $this->faker->randomElement(BudgetItemTypes::class);
        // Ellenőrizzük, hogy létezik-e már ez a kategória
        $existingCategory = Category::whereName($categoryName)->whereBudgetItemType($budgetItemType)->first();

        if ($existingCategory) {
            // Ha létezik, visszaadjuk annak adatait
            return [
                'name' => $existingCategory->name,
                'budget_item_type' => $existingCategory->budget_item_type,
            ];
        }

        // Ha még nem létezik, új kategóriát hozunk létre
        return [
            'name' => $categoryName,
            'budget_item_type' => $budgetItemType,
        ];
    }
}
