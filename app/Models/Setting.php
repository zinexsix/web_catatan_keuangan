<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'theme', 'language', 'currency'])]
class Setting extends Model
{
    /**
     * Owner of these settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if dark mode is enabled.
     */
    public function getIsDarkModeAttribute(): bool
    {
        return $this->theme === 'dark';
    }
}
