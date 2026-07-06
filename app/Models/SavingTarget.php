<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'target_amount', 'current_amount', 'deadline', 'status'])]
class SavingTarget extends Model
{
    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'current_amount' => 'decimal:2',
            'deadline' => 'date',
        ];
    }

    /**
     * Owner of this saving target.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Progress percentage towards target.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    /**
     * Remaining amount to reach target.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->target_amount - (float) $this->current_amount);
    }

    /**
     * Check if target has been achieved.
     */
    public function getIsAchievedAttribute(): bool
    {
        return $this->current_amount >= $this->target_amount;
    }

    /**
     * Days remaining until deadline.
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    /**
     * Check if deadline has passed.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline->isPast() && !$this->is_achieved;
    }

    /**
     * Formatted target amount in Rupiah.
     */
    public function getFormattedTargetAttribute(): string
    {
        return 'Rp ' . number_format($this->target_amount, 0, ',', '.');
    }

    /**
     * Formatted current amount in Rupiah.
     */
    public function getFormattedCurrentAttribute(): string
    {
        return 'Rp ' . number_format($this->current_amount, 0, ',', '.');
    }

    /**
     * Scope: active saving targets only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
