<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use App\Observers\IncomeObserver;
use Database\Factories\IncomeFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(IncomeObserver::class)]
final class Income extends Model
{
    /** @use HasFactory<IncomeFactory> */
    use HasFactory;

    public static function getDateBetweenIncome($startDate, $endDate)
    {
        return (int) (self::whereBetween('payment_date', [$startDate, $endDate])
            ->whereStatus(PaymentStatuses::PAID)
            ->sum('amount'));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'payment_type' => PaymentTypes::class,
            'status' => PaymentStatuses::class,
            'payment_date' => 'date:Y-m-d',
        ];
    }
}
