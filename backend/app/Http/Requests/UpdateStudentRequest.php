<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $org = $this->route('org');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'batch' => ['sometimes', 'required', 'string', 'max:255'],
            'duration' => ['sometimes', 'required', 'string', 'max:255'],
            'course_id' => [
                'sometimes',
                'nullable',
                Rule::exists('courses', 'id')->where('org_code', $org),
            ],
        ];
    }
}
