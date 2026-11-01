<?php

namespace App\Http\Requests\AwardCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $awardCategory = $this->route('award_category');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('award_categories', 'name')
                    ->where('festival_category_id', $awardCategory->festival_category_id)
                    ->ignore($awardCategory->id),
            ],
            'winner_count' => ['required', 'integer', 'min:1', 'max:100'],
            'festival_category_id' => ['sometimes', 'required', 'exists:festival_categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $awardCategory = $this->route('award_category');

            // If trying to change parent category or inherited assessment_type
            if ($this->has('festival_category_id') && $this->festival_category_id != $awardCategory->festival_category_id) {
                // Check if scoring_status != preparation
                if ($awardCategory->scoring_status->value !== 'preparation') {
                    $validator->errors()->add(
                        'festival_category_id',
                        'Kategori induk tidak dapat diubah karena subkategori sudah dalam proses penilaian.'
                    );
                }
            }
        });
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
            'festival_category_id.required' => 'Kategori induk wajib dipilih.',
            'festival_category_id.exists' => 'Kategori induk tidak valid.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
        ];
    }
}
