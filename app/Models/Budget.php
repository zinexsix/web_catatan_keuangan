<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'category_id', 'month', 'year', 'amount'])]
class Budget extends Model
{
    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Owner of this budget.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related category (optional).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get total expenses for this budget's period.
     */
    public function getSpentAmountAttribute(): float
    {
        $query = Transaction::where('user_id', $this->user_id)
            ->where('type', 'expense')
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year);

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Get remaining budget amount.
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) $this->amount - $this->spent_amount;
    }

    /**
     * Get usage percentage (0-100+).
     */
    public function getUsagePercentageAttribute(): float
    {
        if ($this->amount <= 0) return 0;
        return round(($this->spent_amount / $this->amount) * 100, 1);
    }

    /**
     * Check if budget is exceeded.
     */
    public function getIsExceededAttribute(): bool
    {
        return $this->spent_amount > $this->amount;
    }

    /**
     * Check if budget is near limit (>= 80%).
     */
    public function getIsWarningAttribute(): bool
    {
        return $this->usage_percentage >= 80 && !$this->is_exceeded;
    }

    /**
     * Get formatted budget amount in Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get formatted spent amount in Rupiah.
     */
    public function getFormattedSpentAttribute(): string
    {
        return 'Rp ' . number_format($this->spent_amount, 0, ',', '.');
    }
}
