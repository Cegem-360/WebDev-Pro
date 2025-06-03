<?php

declare(strict_types=1);

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Filament\Resources\IncomeResource;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertEquals;

uses(RefreshDatabase::class);
/*
it('creates a single income record when recurring_times is 1', function () {
    // Create a user and a category for testing
    $user = User::factory()->create()->first();
    $category = Category::factory()->create();

    // Initial count of incomes
    $initialCount = Income::count();

    // Act as logged in user
    actingAs($user);

    // Submit the form via Livewire
    Livewire::test(IncomeResource\Pages\CreateIncome::class)
        ->set('data.category_id', $category->id)
        ->set('data.payment_date', now()->format('Y-m-d'))
        ->set('data.description', 'Test Income')
        ->set('data.amount', 5000)
        ->set('data.payment_type', PaymentTypes::SINGLE->value)
        ->set('data.recurring_times', 1) // Only one record
        ->set('data.status', PaymentStatuses::DRAFT->value)
        ->call('create');

    // Assert that exactly one record was created
    assertEquals($initialCount + 1, Income::count());
});

it('creates multiple income records when recurring_times is greater than 1', function () {
    // Create a user and a category for testing
    $user = User::factory()->create();
    $category = Category::factory()->create();

    // Initial count of incomes
    $initialCount = Income::count();

    // Number of recurring records to create
    $recurringTimes = 3;

    // Act as logged in user
    actingAs($user);

    // Submit the form via Livewire
    Livewire::test(IncomeResource\Pages\CreateIncome::class)
        ->set('data.category_id', $category->id)
        ->set('data.payment_date', now()->format('Y-m-d'))
        ->set('data.description', 'Test Recurring Income')
        ->set('data.amount', 5000)
        ->set('data.payment_type', PaymentTypes::RECURRING->value)
        ->set('data.recurring_times', $recurringTimes)
        ->set('data.status', PaymentStatuses::DRAFT->value)
        ->call('create');

    // Assert that exactly $recurringTimes records were created
    assertEquals($initialCount + $recurringTimes, Income::count());

    // Verify that the payment dates are correctly set to consecutive months
    $baseDate = now();
    for ($i = 0; $i < $recurringTimes; $i++) {
        $expectedDate = $baseDate->copy()->addMonths($i)->format('Y-m-d');
        $this->assertDatabaseHas('incomes', [
            'description' => 'Test Recurring Income',
            'payment_date' => $expectedDate,
        ]);
    }
});
 */
