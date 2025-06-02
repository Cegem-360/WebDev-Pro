<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'payment_type' => PaymentTypes::class,
            'status' => PaymentStatuses::class,
            'payment_date' => 'date:Y-m-d',
        ];
    }
}
