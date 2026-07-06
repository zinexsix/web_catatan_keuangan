@extends('layouts.app')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <h3 class="fw-bold mb-1">Catat Transaksi Baru</h3>
        <p class="text-secondary mb-0">Catat setiap pemasukan atau pengeluaran keuangan Anda secara detail.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 col-12 mx-auto">
        <div class="card glass-card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Judul Transaksi -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-semibold">Judul Transaksi <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="cth: Gaji bulanan, Beli Makan Siang" value="{{ old('title') }}" required autofocus>
                    </div>

                    <div class="row">
                        <!-- Jenis Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-semibold">Jenis Transaksi <span class="text-danger">*</span></label>
                            <select name="type" id="typeSelect" class="form-select" required>
                                <option value="expense" {{ $defaultType === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                <option value="income" {{ $defaultType === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            </select>
                        </div>

                        <!-- Kategori (Dynamic filtered by JS) -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="categorySelect" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-type="{{ $cat->type }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Tanggal Transaksi -->
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ old('date', \Carbon\Carbon::now()->toDateString()) }}" required>
                        </div>

                        <!-- Nominal -->
                        <div class="col-md-6 mb-3">
                            <label for="amount" class="form-label fw-semibold">Nominal Rupiah (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="cth: 50000" min="0.01" step="any" value="{{ old('amount') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Metode Pembayaran -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label fw-semibold">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                <option value="e-wallet" {{ old('payment_method') === 'e-wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="qris" {{ old('payment_method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                                <option value="other" {{ old('payment_method') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <!-- Bukti Foto (Optional) -->
                        <div class="col-md-6 mb-3">
                            <label for="receipt_image" class="form-label fw-semibold">Bukti Foto <span class="text-secondary small">(Opsional, Maks 2MB)</span></label>
                            <input type="file" name="receipt_image" id="receipt_image" class="form-control" accept="image/*">
                        </div>
                    </div>

                    <!-- Receipt Image Preview (Hidden initially) -->
                    <div id="receipt-preview-container" class="mb-3 d-none text-center">
                        <span class="d-block small text-secondary mb-1">Preview Bukti Foto:</span>
                        <img id="receipt-preview" src="#" alt="Preview Bukti" class="img-thumbnail rounded" style="max-height: 200px;">
                    </div>

                    <!-- Deskripsi (Optional) -->
                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Deskripsi / Catatan Tambahan <span class="text-secondary small">(Opsional)</span></label>
                        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Tambahkan catatan singkat mengenai transaksi ini...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('transactions.index') }}" class="btn btn-secondary px-4 py-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2"><i class="fas fa-save me-1"></i> Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const typeSelect = document.getElementById('typeSelect');
    const categorySelect = document.getElementById('categorySelect');

    function filterCategories() {
        const selectedType = typeSelect.value;
        
        // Loop through all options in category selector
        Array.from(categorySelect.options).forEach(option => {
            if (option.value === "") return; // Skip placeholder
            
            const categoryType = option.getAttribute('data-type');
            if (categoryType === selectedType) {
                option.style.display = 'block';
                option.disabled = false;
            } else {
                option.style.display = 'none';
                option.disabled = true;
                if (option.selected) {
                    categorySelect.value = ""; // Clear selection if it doesn't match type
                }
            }
        });
    }

    typeSelect.addEventListener('change', filterCategories);
    filterCategories(); // Run initially
});
</script>
@endsection
