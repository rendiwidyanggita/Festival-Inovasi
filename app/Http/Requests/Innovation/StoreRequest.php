<?php

namespace App\Http\Requests\Innovation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'award_category_id' => ['required', 'exists:award_categories,id'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'name' => ['required', 'string', 'max:255'],
            'innovator_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $awardCategoryId = $this->award_category_id;

            if ($awardCategoryId) {
                $awardCategory = \App\Models\AwardCategory::find($awardCategoryId);

                if ($awardCategory) {
                    // Check if award category is of type 'innovation'
                    if ($awardCategory->festivalCategory->assessment_type->value !== 'innovation') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Subkategori harus berjenis penilaian inovasi.'
                        );
                    }

                    // Check if scoring_status is preparation
                    if ($awardCategory->scoring_status->value !== 'preparation') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Inovasi hanya dapat ditambahkan pada subkategori yang masih dalam persiapan.'
                        );
                    }
                }
            }

            // Validate institution is in the same festival
            $institutionId = $this->institution_id;
            if ($institutionId && $awardCategoryId) {
                $institution = \App\Models\Institution::find($institutionId);
                $awardCategory = \App\Models\AwardCategory::find($awardCategoryId);

                if ($institution && $awardCategory) {
                    if ($institution->festival_id !== $awardCategory->festival_id) {
                        $validator->errors()->add(
                            'institution_id',
                            'Instansi harus berada dalam festival yang sama.'
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
            'institution_id.required' => 'Instansi wajib dipilih.',
            'institution_id.exists' => 'Instansi tidak valid.',
            'name.required' => 'Nama inovasi wajib diisi.',
            'name.max' => 'Nama inovasi maksimal 255 karakter.',
            'innovator_name.required' => 'Nama inovator wajib diisi.',
            'innovator_name.max' => 'Nama inovator maksimal 255 karakter.',
        ];
    }
}
