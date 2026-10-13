<?php

use App\Models\Category;
use App\Models\ReceiptFile;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

function transactionPayload(Category $category, array $extra = []): array
{
    return [
        'date' => '2026-10-01', 'type' => 'expense', 'category_id' => $category->id,
        'amount' => 15000, 'title' => 'Makan siang', 'payment_method' => 'cash',
    ] + $extra;
}

test('receipts can be stored in and served from the database', function () {
    config(['filesystems.receipt_disk' => 'database']);

    $user = User::factory()->create();
    $category = Category::create(['user_id' => $user->id, 'name' => 'Makan', 'type' => 'expense']);

    $this->actingAs($user)
        ->post('/transactions', transactionPayload($category, [
            'receipt_image' => UploadedFile::fake()->image('struk.jpg', 50, 50),
        ]))
        ->assertRedirect('/transactions');

    $transaction = $user->transactions()->first();
    $path = $transaction->getRawOriginal('receipt_image');

    expect($path)->toStartWith('receipts/')
        ->and(ReceiptFile::where('path', $path)->exists())->toBeTrue()
        ->and($transaction->receipt_image)->toBe(route('receipts.show', basename($path)));

    $this->actingAs($user)->get($transaction->receipt_image)
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg');

    // Other users cannot view it.
    $this->actingAs(User::factory()->create())->get($transaction->receipt_image)->assertNotFound();

    // Deleting the transaction removes the stored image.
    $this->actingAs($user)->delete("/transactions/{$transaction->id}");
    expect(ReceiptFile::count())->toBe(0);
});

test('google login button is hidden until it is configured', function () {
    config(['services.google.client_id' => null, 'services.google.client_secret' => null]);

    $this->get('/login')->assertOk()->assertDontSee('Login dengan Google');
    $this->get('/auth/google')->assertRedirect('/login')->assertSessionHas('error');

    config(['services.google.client_id' => 'id', 'services.google.client_secret' => 'secret']);

    $this->get('/login')->assertSee('Login dengan Google');
});

test('google users without a password can set one', function () {
    $user = User::factory()->create(['password' => null]);

    $this->actingAs($user)
        ->put('/password', ['password' => 'rahasia-baru-123', 'password_confirmation' => 'rahasia-baru-123'])
        ->assertSessionHasNoErrors();

    expect(Hash::check('rahasia-baru-123', $user->fresh()->password))->toBeTrue();
});

test('google users without a password can delete their account', function () {
    $user = User::factory()->create(['password' => null]);

    $this->actingAs($user)->delete('/profile')->assertRedirect('/');

    expect(User::find($user->id))->toBeNull();
});
