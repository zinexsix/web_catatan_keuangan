<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SavingTargetRequest extends FormRequest
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
            'target_amount' => ['required', 'numeric', 'min:0.01'],
            'current_amount' => ['required', 'numeric', 'min:0'],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ];
    }

    /**
     * Custom messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama target tabungan wajib diisi.',
            'target_amount.required' => 'Nominal target wajib diisi.',
            'target_amount.min' => 'Nominal target minimal Rp 0.01.',
            'current_amount.required' => 'Nominal terkumpul wajib diisi.',
            'deadline.required' => 'Batas waktu (deadline) wajib ditentukan.',
            'deadline.after_or_equal' => 'Batas waktu tidak boleh tanggal kemarin.',
            'status.required' => 'Status wajib diisi.',
        ];
    }
}
