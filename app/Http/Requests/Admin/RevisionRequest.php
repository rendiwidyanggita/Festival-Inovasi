<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan permintaan revisi wajib diisi.',
            'reason.string' => 'Alasan harus berupa teks.',
            'reason.max' => 'Alasan maksimal 1000 karakter.',
        ];
    }
}
