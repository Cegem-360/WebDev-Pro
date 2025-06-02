<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentTypes: string implements HasLabel
{
    case SINGLE = 'single';
    case RECURRING = 'recurring';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SINGLE => __('Single Pay'),
            self::RECURRING => __('Recurring Pay'),
        };
    }
}
