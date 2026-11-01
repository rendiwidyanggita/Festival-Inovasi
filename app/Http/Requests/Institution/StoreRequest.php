<?php

namespace App\Http\Requests\Institution;

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
        return [
            'award_category_id' => [
                'required',
                'exists:award_categories,id',
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $awardCategoryId = $this->award_category_id;

            if ($awardCategoryId) {
                $awardCategory = \App\Models\AwardCategory::find($awardCategoryId);

                if ($awardCategory) {
                    // Check if award category is of type 'institution'
                    if ($awardCategory->festivalCategory->assessment_type->value !== 'institution') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Subkategori harus berjenis penilaian instansi.'
                        );
                    }

                    // Check if scoring_status is preparation
                    if ($awardCategory->scoring_status->value !== 'preparation') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Instansi hanya dapat ditambahkan pada subkategori yang masih dalam persiapan.'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'award_category_id.required' => 'Subkategori wajib dipilih.',
            'award_category_id.exists' => 'Subkategori tidak valid.',
            'name.required' => 'Nama instansi wajib diisi.',
            'name.max' => 'Nama instansi maksimal 255 karakter.',
        ];
    }
}
