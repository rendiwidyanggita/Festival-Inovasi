<?php

namespace App\Http\Requests\Jury;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDraftRequest extends FormRequest
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
            'scores.*' => ['nullable', 'integer', 'min:6', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'scores.required' => 'Data nilai wajib diisi.',
            'scores.array' => 'Format nilai tidak valid.',
            'scores.*.integer' => 'Nilai harus berupa bilangan bulat.',
            'scores.*.min' => 'Nilai minimal adalah 6.',
            'scores.*.max' => 'Nilai maksimal adalah 10.',
        ];
    }
}
