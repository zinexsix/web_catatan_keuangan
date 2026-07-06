<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
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
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_day' => ['required', 'integer', 'between:1,31'],
            'recurrence' => ['required', 'in:monthly,yearly'],
            'status' => ['required', 'in:pending,paid,overdue'],
            'category' => ['nullable', 'string', 'max:100'],
            'icon' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama tagihan wajib diisi.',
            'amount.required' => 'Nominal tagihan wajib diisi.',
            'amount.min' => 'Nominal tagihan minimal Rp 0.01.',
            'due_day.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_day.between' => 'Tanggal jatuh tempo harus di antara tanggal 1 - 31.',
            'recurrence.required' => 'Periode tagihan wajib ditentukan.',
            'status.required' => 'Status tagihan wajib ditentukan.',
            'icon.required' => 'Icon wajib dipilih.',
        ];
    }
}
