<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BudgetItemTypes: string implements HasLabel
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::INCOME => __('Income'),
            self::EXPENSE => __('Expense'),
            self::SAVINGS => __('Savings'),
            self::INVESTMENT => __('Investment'),
        };
    }
}
