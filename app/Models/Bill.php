<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'amount', 'due_day', 'recurrence', 'status', 'category', 'icon'])]
class Bill extends Model
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
     * Owner of this bill.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if bill is due within N days.
     */
    public function isDueSoon(int $days = 3): bool
    {
        $today = now()->day;
        $dueDay = $this->due_day;

        // Calculate days until due
        if ($dueDay >= $today) {
            $daysUntil = $dueDay - $today;
        } else {
            // Due day is in next month
            $daysInMonth = now()->daysInMonth;
            $daysUntil = ($daysInMonth - $today) + $dueDay;
        }

        return $daysUntil <= $days && $daysUntil >= 0 && $this->status === 'pending';
    }

    /**
     * Get days until next due date.
     */
    public function getDaysUntilDueAttribute(): int
    {
        $today = now()->day;
        $dueDay = $this->due_day;

        if ($dueDay >= $today) {
            return $dueDay - $today;
        }

        return (now()->daysInMonth - $today) + $dueDay;
    }

    /**
     * Formatted amount in Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'overdue' => 'Terlambat',
            default => $this->status,
        };
    }

    /**
     * Get status badge CSS class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope: upcoming bills (due within N days).
     */
    public function scopeUpcoming($query, int $days = 7)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: overdue bills.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }
}
