<?php

namespace App\Http\Requests\AwardCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $festivalCategoryId = $this->route('festival_category')->id ?? $this->festival_category_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('award_categories', 'name')->where('festival_category_id', $festivalCategoryId),
            ],
            'winner_count' => ['required', 'integer', 'min:1', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama subkategori wajib diisi.',
            'name.max' => 'Nama subkategori maksimal 255 karakter.',
            'name.unique' => 'Nama subkategori sudah ada di kategori ini.',
            'winner_count.required' => 'Jumlah pemenang wajib diisi.',
            'winner_count.integer' => 'Jumlah pemenang harus berupa angka.',
            'winner_count.min' => 'Jumlah pemenang minimal 1.',
            'winner_count.max' => 'Jumlah pemenang maksimal 100.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
        ];
    }
}
