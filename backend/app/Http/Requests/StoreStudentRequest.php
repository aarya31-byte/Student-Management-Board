<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $org = $this->route('org');

        return [
            'name' => ['required', 'string', 'max:255'],
            'batch' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'string', 'max:255'],
            'course_id' => [
                'nullable',
                Rule::exists('courses', 'id')->where('org_code', $org),
            ],
        ];
    }
}
