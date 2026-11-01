<?php

namespace App\Http\Requests\Jury;

use App\Models\AssessmentCriterion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SubmitAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('scores') && $this->has('criteria')) {
            $this->merge(['scores' => $this->input('criteria')]);
        }
    }

    public function rules(): array
    {
        return [
            'scores' => ['required', 'array'],
            'scores.*' => ['required', 'integer', 'min:6', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'scores.required' => 'Data nilai wajib diisi.',
            'scores.array' => 'Format nilai tidak valid.',
            'scores.*.required' => 'Semua kriteria harus diisi.',
            'scores.*.integer' => 'Nilai harus berupa bilangan bulat.',
            'scores.*.min' => 'Nilai minimal adalah 6.',
            'scores.*.max' => 'Nilai maksimal adalah 10.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $awardCategory = $this->route('awardCategory');
            $assessmentType = $awardCategory->assessment_type;

            $criteriaIds = AssessmentCriterion::where('festival_id', $awardCategory->festival_id)
                ->where('assessment_type', $assessmentType)
                ->pluck('id')
                ->toArray();

            $providedIds = array_keys($this->scores ?? []);

            $missing = array_diff($criteriaIds, $providedIds);

            if (!empty($missing)) {
                $validator->errors()->add('scores', 'Semua kriteria penilaian harus diisi.');
            }
        });
    }
}
