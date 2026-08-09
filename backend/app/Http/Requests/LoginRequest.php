<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    // backend_details.md §7 expects a plain string at `detail` for login
    // failures specifically (the frontend alerts it directly) — flatten
    // instead of the structured per-field array the app uses elsewhere.
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'detail' => 'Username and password are required.',
        ], 422));
    }
}
