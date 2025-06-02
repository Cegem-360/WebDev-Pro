<?php

declare(strict_types=1);

use App\Enums\BudgetItemTypes;
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
        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique()->comment('The name of the category');
            $table->enum('budget_item_type', array_column(BudgetItemTypes::cases(), 'value'))->nullable()->comment('The type of budget item for this category');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
