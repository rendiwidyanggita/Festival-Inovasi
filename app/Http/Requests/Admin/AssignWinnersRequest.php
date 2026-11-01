<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignWinnersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('winners') && is_array($this->winners)) {
            $hasSelectedKey = collect($this->winners)->contains(fn ($w) => is_array($w) && array_key_exists('selected', $w));
            
            $filtered = array_filter($this->winners, function ($winner) use ($hasSelectedKey) {
                if (!is_array($winner)) {
                    return false;
                }
                if ($hasSelectedKey) {
                    return !empty($winner['selected']) && !empty($winner['position']);
                }
                return !empty($winner['target_id']) && !empty($winner['position']);
            });
            $this->merge(['winners' => array_values($filtered)]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'winners' => ['required', 'array'],
            'winners.*.target_type' => ['required', 'string', Rule::in(['innovation', 'institution'])],
            'winners.*.target_id' => ['required', 'integer', 'min:1'],
            'winners.*.position' => ['required', 'integer', 'min:1'],
            'winners.*.notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'winners.required' => 'Data pemenang wajib diisi.',
            'winners.array' => 'Format data pemenang tidak valid.',
            'winners.*.target_type.required' => 'Tipe target wajib diisi.',
            'winners.*.target_type.in' => 'Tipe target harus innovation atau institution.',
            'winners.*.target_id.required' => 'ID target wajib diisi.',
            'winners.*.target_id.min' => 'ID target tidak valid.',
            'winners.*.position.required' => 'Posisi juara wajib diisi.',
            'winners.*.position.min' => 'Posisi juara minimal 1.',
            'winners.*.notes.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
