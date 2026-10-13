<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;

function makeUserData(User $user, string $label, ?string $receipt = null): Category
{
    $category = Category::create(['user_id' => $user->id, 'name' => "Kategori {$label}", 'type' => 'expense']);

    Transaction::create([
        'user_id' => $user->id, 'category_id' => $category->id, 'date' => '2026-10-01',
        'type' => 'expense', 'amount' => 10000, 'title' => "Transaksi {$label}",
        'payment_method' => 'cash', 'receipt_image' => $receipt,
    ]);

    Budget::create(['user_id' => $user->id, 'category_id' => $category->id, 'month' => 10, 'year' => 2026, 'amount' => 50000]);

    return $category;
}

function backupFile(array $payload): UploadedFile
{
    return UploadedFile::fake()->createWithContent('backup.json', json_encode($payload));
}

test('backup only contains the current user data', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    makeUserData($alice, 'Alice');
    makeUserData($bob, 'Bob');

    $response = $this->actingAs($alice)->get('/settings/backup');
    $response->assertOk();

    $payload = json_decode($response->streamedContent(), true);

    expect($payload['app'])->toBe('catatan-keuangan')
        ->and($payload)->not->toHaveKey('data.users')
        ->and(collect($payload['data']['transactions'])->pluck('title')->all())->toBe(['Transaksi Alice'])
        ->and(collect($payload['data']['categories'])->pluck('name')->all())->toBe(['Kategori Alice']);
});

test('restore replaces only the current user data and remaps categories', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    makeUserData($alice, 'Alice');
    makeUserData($bob, 'Bob');

    $payload = json_decode($this->actingAs($alice)->get('/settings/backup')->streamedContent(), true);
    $payload['data']['transactions'][0]['title'] = 'Transaksi Dipulihkan';

    $this->actingAs($alice)
        ->post('/settings/restore', ['backup_file' => backupFile($payload)])
        ->assertRedirect('/settings')
        ->assertSessionHas('success');

    $restored = $alice->transactions()->with('category')->get();
    expect($restored)->toHaveCount(1)
        ->and($restored[0]->title)->toBe('Transaksi Dipulihkan')
        ->and($restored[0]->category->user_id)->toBe($alice->id)
        ->and($alice->budgets()->first()->category_id)->toBe($restored[0]->category_id)
        ->and($bob->transactions()->pluck('title')->all())->toBe(['Transaksi Bob'])
        ->and($bob->categories()->count())->toBe(1);
});

test('restore cannot claim receipt files of other users', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    makeUserData($alice, 'Alice');
    makeUserData($bob, 'Bob', 'receipts/bob.jpg');

    $payload = json_decode($this->actingAs($alice)->get('/settings/backup')->streamedContent(), true);
    $payload['data']['transactions'][0]['receipt_image'] = 'receipts/bob.jpg';

    $this->actingAs($alice)->post('/settings/restore', ['backup_file' => backupFile($payload)]);

    expect($alice->transactions()->first()->getRawOriginal('receipt_image'))->toBeNull();
});

test('restore rejects files that are not app backups', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/settings/restore', ['backup_file' => UploadedFile::fake()->createWithContent('backup.sql', 'DROP TABLE users;')])
        ->assertSessionHas('error');

    expect(User::count())->toBe(1);
});

test('transactions cannot use categories of other users', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $bobCategory = makeUserData($bob, 'Bob');

    $this->actingAs($alice)
        ->post('/transactions', [
            'date' => '2026-10-01', 'type' => 'expense', 'category_id' => $bobCategory->id,
            'amount' => 1000, 'title' => 'Curang', 'payment_method' => 'cash',
        ])
        ->assertSessionHasErrors('category_id');
});

test('receipt url is built from the stored path regardless of legacy format', function () {
    expect(Transaction::receiptPath('http://localhost/storage/receipts/a.jpg'))->toBe('receipts/a.jpg')
        ->and(Transaction::receiptPath('/storage/receipts/a.jpg'))->toBe('receipts/a.jpg')
        ->and(Transaction::receiptPath('receipts/a.jpg'))->toBe('receipts/a.jpg')
        ->and((new Transaction(['receipt_image' => '/storage/receipts/a.jpg']))->receipt_image)
        ->toBe(rtrim(config('app.url'), '/').'/storage/receipts/a.jpg');
});
