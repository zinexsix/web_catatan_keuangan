<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
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
        $userId = auth()->id();
        $categoryId = $this->input('category_id');
        $month = $this->input('month');
        $year = $this->input('year');

        return [
            'category_id' => [
                'nullable', 
                Rule::exists('categories', 'id')->where('user_id', $userId),
                // Unique check combination of user, category, month, year
                Rule::unique('budgets')->where(function ($query) use ($userId, $categoryId, $month, $year) {
                    return $query->where('user_id', $userId)
                        ->where('category_id', $categoryId)
                        ->where('month', $month)
                        ->where('year', $year);
                })->ignore($this->route('budget'))
            ],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'min:2020', 'max:2050'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'category_id.unique' => 'Anggaran untuk kategori ini pada periode yang dipilih sudah diatur sebelumnya.',
            'month.required' => 'Bulan wajib dipilih.',
            'month.between' => 'Bulan tidak valid.',
            'year.required' => 'Tahun wajib diisi.',
            'year.min' => 'Tahun minimal 2020.',
            'amount.required' => 'Nominal anggaran wajib diisi.',
            'amount.min' => 'Nominal anggaran minimal Rp 0.01.',
        ];
    }
}
