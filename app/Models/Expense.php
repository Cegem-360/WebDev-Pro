<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatuses;
use App\Enums\PaymentTypes;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'payment_date',
        'description',
        'amount',
        'payment_type',
        'status',
    ];

    public static function getDateBetweenExpense($startDate, $endDate)
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
