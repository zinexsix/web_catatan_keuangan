<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'phone', 'avatar', 'google_id', 'google_token', 'password'])]
#[Hidden(['password', 'remember_token', 'google_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * User's categories (income & expense).
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * User's financial transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * User's monthly budgets.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * User's saving targets/goals.
     */
    public function savingTargets(): HasMany
    {
        return $this->hasMany(SavingTarget::class);
    }

    /**
     * User's recurring bills.
     */
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    /**
     * User's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * User's app settings (theme, language, currency).
     */
    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class);
    }

    public function getOrCreateSetting(): Setting
    {
        $setting = $this->setting;
        if (!$setting) {
            $setting = $this->setting()->firstOrCreate([], [
                'theme' => 'light',
                'language' => 'id',
                'currency' => 'IDR',
            ]);
            $this->setRelation('setting', $setting);
        }
        return $setting;
    }
}
