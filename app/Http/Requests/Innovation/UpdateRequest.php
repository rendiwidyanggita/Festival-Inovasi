<?php

namespace App\Http\Requests\Innovation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $innovation = $this->route('innovation');
        $isRosterLocked = $innovation->awardCategory->isRosterLocked();

        // If roster is locked (scoring_status != preparation), only name and innovator_name can be updated
        if ($isRosterLocked) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'innovator_name' => ['required', 'string', 'max:255'],
            ];
        }

        return [
            'award_category_id' => ['sometimes', 'required', 'exists:award_categories,id'],
            'institution_id' => ['sometimes', 'required', 'exists:institutions,id'],
            'name' => ['required', 'string', 'max:255'],
            'innovator_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $innovation = $this->route('innovation');
            $isRosterLocked = $innovation->awardCategory->isRosterLocked();

            // If roster is locked, prevent changing award_category_id and institution_id to different ones
            if ($isRosterLocked) {
                if ($this->filled('award_category_id') && (int) $this->award_category_id !== (int) $innovation->award_category_id) {
                    $validator->errors()->add(
                        'award_category_id',
                        'Subkategori tidak dapat diubah karena penilaian sudah dimulai.'
                    );
                }
                if ($this->filled('institution_id') && (int) $this->institution_id !== (int) $innovation->institution_id) {
                    $validator->errors()->add(
                        'institution_id',
                        'Instansi tidak dapat diubah karena penilaian sudah dimulai.'
                    );
                }
            }

            // If roster is not locked and changing award_category_id, validate it
            if (!$isRosterLocked && $this->has('award_category_id')) {
                $awardCategory = \App\Models\AwardCategory::find($this->award_category_id);

                if ($awardCategory) {
                    if ($awardCategory->festivalCategory->assessment_type->value !== 'innovation') {
                        $validator->errors()->add(
                            'award_category_id',
                            'Subkategori harus berjenis penilaian inovasi.'
                        );
                    }
                }
            }

            // Validate institution is in the same festival
            if (!$isRosterLocked && $this->has('institution_id') && $this->has('award_category_id')) {
                $institution = \App\Models\Institution::find($this->institution_id);
                $awardCategory = \App\Models\AwardCategory::find($this->award_category_id);

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
