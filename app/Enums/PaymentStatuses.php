<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentStatuses: string implements HasLabel
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case INVOICED = 'invoiced';
    case PAID = 'paid';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::PENDING => __('Pending'),
            self::INVOICED => __('Invoiced'),
            self::PAID => __('Paid'),
        };
    }
}
