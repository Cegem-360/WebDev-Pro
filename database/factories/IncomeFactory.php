<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Category;
use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
final class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'payment_date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->numberBetween(1000, 900000),
            'payment_type' => $this->faker->randomElement(PaymentTypes::class),
            'status' => $this->faker->randomElement(PaymentStatuses::class),
        ];
    }
}
