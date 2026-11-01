<?php

namespace App\Http\Requests\Festival;

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
        $festival = $this->route('festival');

        return [
            'name' => ['required', 'string', 'max:255'],
            'year' => [
                'sometimes',
                'integer',
                'min:2000',
                'max:2100',
                Rule::unique('festivals', 'year')->ignore($festival->id),
            ],
            'organizer_name' => ['nullable', 'string', 'max:255'],
            'organizer_logo_path' => ['nullable', 'string', 'max:500'],
            'report_title' => ['nullable', 'string', 'max:255'],
            'report_footer' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama festival wajib diisi.',
            'name.max' => 'Nama festival maksimal 255 karakter.',
            'year.integer' => 'Tahun harus berupa angka.',
            'year.min' => 'Tahun minimal 2000.',
            'year.max' => 'Tahun maksimal 2100.',
            'year.unique' => 'Tahun sudah terdaftar.',
            'organizer_name.max' => 'Nama penyelenggara maksimal 255 karakter.',
            'organizer_logo_path.max' => 'Path logo maksimal 500 karakter.',
            'report_title.max' => 'Judul laporan maksimal 255 karakter.',
            'report_footer.max' => 'Footer laporan maksimal 500 karakter.',
        ];
    }
}
