<?php

namespace App\Http\Requests\FestivalCategory;

use App\Enums\AssessmentType;
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
        $festivalCategory = $this->route('festival_category');
        $festivalId = session('selected_festival_id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('festival_categories', 'name')
                    ->where('festival_id', $festivalId)
                    ->ignore($festivalCategory->id),
            ],
            'assessment_type' => ['sometimes', 'required', 'in:innovation,institution'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $festivalCategory = $this->route('festival_category');

            // Only validate assessment_type change if it's being changed
            if ($this->has('assessment_type') && $this->assessment_type !== $festivalCategory->assessment_type->value) {
                // Check if any award category has scoring_status != preparation
                $hasScoring = $festivalCategory->awardCategories()
                    ->where('scoring_status', '!=', 'preparation')
                    ->exists();

                if ($hasScoring) {
                    $validator->errors()->add(
                        'assessment_type',
                        'Jenis penilaian tidak dapat diubah karena sudah ada subkategori yang dalam proses penilaian.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Nama kategori sudah ada di festival ini.',
            'assessment_type.required' => 'Jenis penilaian wajib dipilih.',
            'assessment_type.in' => 'Jenis penilaian tidak valid.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
        ];
    }
}
