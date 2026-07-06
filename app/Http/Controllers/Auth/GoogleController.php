<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     */
    public function callback(): RedirectResponse
    {
        try {
            /** @var SocialiteUser $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Log::error('Google login failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update Google credentials
            $user->update([
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Create new user from Google data
            $username = Str::slug($googleUser->getName(), '_') . '_' . Str::random(4);

            $user = User::create([
                'name' => $googleUser->getName(),
                'username' => $username,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'password' => null,
            ]);

            // Create default settings
            $user->setting()->create([
                'theme' => 'light',
                'language' => 'id',
                'currency' => 'IDR',
            ]);

            // Create default categories (same as register)
            $this->createDefaultCategories($user);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }

    /**
     * Create default categories for a new Google user.
     */
    protected function createDefaultCategories(User $user): void
    {
        $incomeCategories = [
            ['name' => 'Gaji', 'icon' => 'fa-money-bill-wave', 'color' => '#10b981'],
            ['name' => 'Bonus', 'icon' => 'fa-gift', 'color' => '#06b6d4'],
            ['name' => 'Investasi', 'icon' => 'fa-chart-line', 'color' => '#8b5cf6'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => '#f59e0b'],
        ];

        $expenseCategories = [
            ['name' => 'Makanan', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
            ['name' => 'Transportasi', 'icon' => 'fa-car', 'color' => '#f97316'],
            ['name' => 'Belanja', 'icon' => 'fa-shopping-bag', 'color' => '#ec4899'],
            ['name' => 'Listrik', 'icon' => 'fa-bolt', 'color' => '#eab308'],
            ['name' => 'Kesehatan', 'icon' => 'fa-heartbeat', 'color' => '#14b8a6'],
            ['name' => 'Hiburan', 'icon' => 'fa-film', 'color' => '#a855f7'],
        ];

        foreach ($incomeCategories as $cat) {
            $user->categories()->create(array_merge($cat, ['type' => 'income']));
        }

        foreach ($expenseCategories as $cat) {
            $user->categories()->create(array_merge($cat, ['type' => 'expense']));
        }
    }
}
