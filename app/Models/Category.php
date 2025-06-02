<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BudgetItemTypes;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    protected function casts(): array
    {
        return [
            'budget_item_type' => BudgetItemTypes::class,
        ];
    }
}
