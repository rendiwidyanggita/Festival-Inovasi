<?php

namespace App\Http\Requests\Institution;

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
        $institution = $this->route('institution');
        $isRosterLocked = $institution->awardCategory->isRosterLocked();

        // If roster is locked (scoring_status != preparation), only name can be updated
        if ($isRosterLocked) {
            return [
                'name' => ['required', 'string', 'max:255'],
            ];
        }

        return [
            'award_category_id' => [
                'sometimes',
                'required',
                'exists:award_categories,id',
            ],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $institution = $this->route('institution');
            $isRosterLocked = $institution->awardCategory->isRosterLocked();

            // If roster is locked, prevent changing award_category_id to a different category
            if ($isRosterLocked && $this->filled('award_category_id') && (int) $this->award_category_id !== (int) $institution->award_category_id) {
                $validator->errors()->add(
                    'award_category_id',
                    'Subkategori tidak dapat diubah karena penilaian sudah dimulai.'
                );
            }

            // If roster is not locked and changing award_category_id, validate it
            if (!$isRosterLocked && $this->has('award_category_id')) {
                $awardCategory = \App\Models\AwardCategory::find($this->award_category_id);

                if ($awardCategory) {
                    if ($awardCategory->festivalCategory->assessment_type->value !== 'institution') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Subkategori harus berjenis penilaian instansi.'
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
