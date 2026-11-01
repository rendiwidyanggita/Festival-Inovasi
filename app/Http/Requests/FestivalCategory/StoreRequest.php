<?php

namespace App\Http\Requests\FestivalCategory;

use App\Enums\AssessmentType;
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('festival_categories', 'name')->where('festival_id', $festivalId),
            ],
            'assessment_type' => ['required', 'in:innovation,institution'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
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
