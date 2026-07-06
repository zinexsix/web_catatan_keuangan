<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users', 'alpha_dash'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['required', 'string', 'max:15', 'unique:users', 'regex:/^[0-9+\-\s]{9,15}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'phone.regex' => 'Format nomor HP tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Create default settings
        $user->setting()->create([
            'theme' => 'light',
            'language' => 'id',
            'currency' => 'IDR',
        ]);

        // Create default categories
        $this->createDefaultCategories($user);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Create default categories for a new user.
     */
    protected function createDefaultCategories(User $user): void
    {
        $incomeCategories = [
            ['name' => 'Gaji', 'icon' => 'fa-money-bill-wave', 'color' => '#10b981'],
            ['name' => 'Bonus', 'icon' => 'fa-gift', 'color' => '#06b6d4'],
            ['name' => 'Investasi', 'icon' => 'fa-chart-line', 'color' => '#8b5cf6'],
            ['name' => 'Freelance', 'icon' => 'fa-laptop-code', 'color' => '#f59e0b'],
            ['name' => 'Pendapatan Lainnya', 'icon' => 'fa-plus-circle', 'color' => '#64748b'],
        ];

        $expenseCategories = [
            ['name' => 'Makanan', 'icon' => 'fa-utensils', 'color' => '#ef4444'],
            ['name' => 'Transportasi', 'icon' => 'fa-car', 'color' => '#f97316'],
            ['name' => 'Belanja', 'icon' => 'fa-shopping-bag', 'color' => '#ec4899'],
            ['name' => 'Listrik', 'icon' => 'fa-bolt', 'color' => '#eab308'],
            ['name' => 'Internet', 'icon' => 'fa-wifi', 'color' => '#3b82f6'],
            ['name' => 'Kesehatan', 'icon' => 'fa-heartbeat', 'color' => '#14b8a6'],
            ['name' => 'Pendidikan', 'icon' => 'fa-graduation-cap', 'color' => '#6366f1'],
            ['name' => 'Hiburan', 'icon' => 'fa-film', 'color' => '#a855f7'],
            ['name' => 'Pengeluaran Lainnya', 'icon' => 'fa-ellipsis-h', 'color' => '#64748b'],
        ];

        foreach ($incomeCategories as $cat) {
            $user->categories()->create(array_merge($cat, ['type' => 'income']));
        }

        foreach ($expenseCategories as $cat) {
            $user->categories()->create(array_merge($cat, ['type' => 'expense']));
        }
    }
}
