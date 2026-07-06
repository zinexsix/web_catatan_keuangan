<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'message', 'type', 'is_read', 'data'])]
class Notification extends Model
{
    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'data' => 'array',
        ];
    }

    /**
     * Owner of this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    /**
     * Get icon based on notification type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'danger' => 'fa-times-circle',
            'info' => 'fa-info-circle',
            default => 'fa-bell',
        };
    }

    /**
     * Get color based on notification type.
     */
    public function getColorAttribute(): string
    {
        return match ($this->type) {
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'info' => '#6366f1',
            default => '#64748b',
        };
    }
}
