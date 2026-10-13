<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id', 'category_id', 'date', 'type', 'amount',
    'title', 'description', 'payment_method', 'receipt_image',
])]
class Transaction extends Model
{
    use HasFactory;

    /**
     * Attribute casts.
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Owner of this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category of this transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ──────────────────────────────────────────

    /**
     * Filter by type (income/expense).
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Filter income transactions only.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Filter expense transactions only.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Filter by specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Filter by month and year.
     */
    public function scopeInMonth($query, int $month, int $year)
    {
        return $query->whereMonth('date', $month)->whereYear('date', $year);
    }

    /**
     * Filter by year.
     */
    public function scopeInYear($query, int $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Filter by category.
     */
    public function scopeInCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Search by title or description.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // ── Receipt Storage ─────────────────────────────────

    /**
     * Disk used to store receipt images (public, s3, ...).
     */
    public static function receiptDisk(): string
    {
        return config('filesystems.receipt_disk', 'public');
    }

    /**
     * Normalize a stored receipt value to its relative path ("receipts/xxx.jpg").
     * Supports legacy values saved as full URLs or "/storage/receipts/...".
     */
    public static function receiptPath(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        $pos = strpos($value, 'receipts/');

        return $pos === false ? null : substr($value, $pos);
    }

    /**
     * Store an uploaded receipt and return its relative path.
     */
    public static function storeReceipt(UploadedFile $file): string
    {
        if (static::receiptDisk() !== 'database') {
            return $file->store('receipts', static::receiptDisk());
        }

        $path = 'receipts/'.$file->hashName();

        ReceiptFile::create([
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'content' => base64_encode($file->get()),
        ]);

        return $path;
    }

    /**
     * Delete a stored receipt file from the receipt disk.
     */
    public static function deleteReceipt(?string $value): void
    {
        if (! $path = static::receiptPath($value)) {
            return;
        }

        if (static::receiptDisk() === 'database') {
            ReceiptFile::where('path', $path)->delete();
        } else {
            Storage::disk(static::receiptDisk())->delete($path);
        }
    }

    // ── Accessors ───────────────────────────────────────

    /**
     * Public URL of the receipt image, built from the current disk/domain.
     */
    public function getReceiptImageAttribute(?string $value): ?string
    {
        $path = static::receiptPath($value);

        if (! $path) {
            return null;
        }

        return static::receiptDisk() === 'database'
            ? route('receipts.show', basename($path))
            : Storage::disk(static::receiptDisk())->url($path);
    }

    /**
     * Format amount as Indonesian Rupiah.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get payment method label in Indonesian.
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash' => 'Cash',
            'transfer' => 'Transfer',
            'e-wallet' => 'E-Wallet',
            'qris' => 'QRIS',
            'other' => 'Lainnya',
            default => $this->payment_method,
        };
    }

    /**
     * Get type label in Indonesian.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    }
}
