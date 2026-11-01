<?php

namespace App\Http\Requests\AssessmentCriterion;

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
        $festivalId = session('selected_festival_id');

        return [
            'assessment_type' => ['required', 'in:innovation,institution'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('assessment_criteria', 'name')
                    ->where('festival_id', $festivalId)
                    ->where('assessment_type', $this->assessment_type),
            ],
            'weight' => ['required', 'integer', 'min:1', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'assessment_type.required' => 'Jenis penilaian wajib dipilih.',
            'assessment_type.in' => 'Jenis penilaian tidak valid.',
            'name.required' => 'Nama kriteria wajib diisi.',
            'name.max' => 'Nama kriteria maksimal 255 karakter.',
            'name.unique' => 'Nama kriteria sudah ada untuk festival dan jenis penilaian ini.',
            'weight.required' => 'Bobot wajib diisi.',
            'weight.integer' => 'Bobot harus berupa angka.',
            'weight.min' => 'Bobot minimal 1.',
            'weight.max' => 'Bobot maksimal 100.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
            'sort_order.min' => 'Urutan minimal 0.',
        ];
    }
}
