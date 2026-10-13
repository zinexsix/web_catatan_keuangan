<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'type' => ['required', 'in:income,expense'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', auth()->id())],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'payment_method' => ['required', 'in:cash,transfer,e-wallet,qris,other'],
            'receipt_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'], // Max 2MB
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal transaksi wajib diisi.',
            'type.required' => 'Jenis transaksi wajib ditentukan.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'amount.required' => 'Nominal transaksi wajib diisi.',
            'amount.numeric' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal transaksi minimal Rp 0.01.',
            'title.required' => 'Judul transaksi wajib diisi.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'receipt_image.image' => 'Bukti transaksi harus berupa gambar.',
            'receipt_image.mimes' => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'receipt_image.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
