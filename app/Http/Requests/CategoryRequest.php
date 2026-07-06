<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:income,expense'],
            'color' => ['required', 'string', 'size:7', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'icon' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'type.required' => 'Jenis kategori wajib ditentukan.',
            'type.in' => 'Jenis kategori harus Pemasukan atau Pengeluaran.',
            'color.required' => 'Warna wajib diisi.',
            'color.regex' => 'Format warna hex tidak valid.',
            'icon.required' => 'Icon wajib diisi.',
        ];
    }
}
