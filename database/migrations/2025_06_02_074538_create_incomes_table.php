<?php

declare(strict_types=1);

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Category::class)->nullable()->cascadeOnUpdate()->nullOnDelete();
            $table->date('payment_date');
            $table->text('description');
            $table->integer('amount');
            $table->integer('recurring_times')
                ->default(1)
                ->comment('Number of times the income is recurring, default is 1 for single payments');
            $table->enum('payment_type', array_column(PaymentTypes::cases(), 'value'))
                ->nullable(false)
                ->default(PaymentTypes::SINGLE->value)
                ->comment('The payment type for the income, e.g., single or recurring');
            $table->enum('status', array_column(PaymentStatuses::cases(), 'value'))
                ->nullable(false)
                ->default(PaymentStatuses::DRAFT->value)
                ->comment('The status of the income, e.g., draft, pending, invoiced, paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
