<?php

namespace App\Http\Requests\FestivalJury;

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
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('festival_juries')->where(function ($query) use ($festivalId) {
                    return $query->where('festival_id', $festivalId);
                }),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $userId = $this->user_id;

            if ($userId) {
                $user = \App\Models\User::find($userId);

                if ($user) {
                    // Check if user has jury role
                    if (!$user->isJury()) {
                        $validator->errors()->add(
                            'user_id',
                            'User harus memiliki peran juri.'
                        );
                    }

                    // Check if user is active
                    if (!$user->is_active) {
                        $validator->errors()->add(
                            'user_id',
                            'User harus dalam status aktif.'
                        );
                    }

                    // Check if user is not soft-deleted
                    if ($user->trashed()) {
                        $validator->errors()->add(
                            'user_id',
                            'User tidak dapat ditambahkan karena sudah dihapus.'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Juri wajib dipilih.',
            'user_id.exists' => 'Juri tidak valid.',
            'user_id.unique' => 'Juri sudah terdaftar di festival ini.',
        ];
    }
}
