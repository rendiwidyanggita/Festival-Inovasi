<?php

namespace App\Http\Requests\Festival;

use App\Enums\FestivalStatus;
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
            'year' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:festivals,year'],
            'name' => ['required', 'string', 'max:255'],
            'organizer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'year.required' => 'Tahun wajib diisi.',
            'year.integer' => 'Tahun harus berupa angka.',
            'year.min' => 'Tahun minimal 2000.',
            'year.max' => 'Tahun maksimal 2100.',
            'year.unique' => 'Tahun sudah terdaftar.',
            'name.required' => 'Nama festival wajib diisi.',
            'name.max' => 'Nama festival maksimal 255 karakter.',
            'organizer_name.max' => 'Nama penyelenggara maksimal 255 karakter.',
        ];
    }
}
