<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGaCodingPracticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => [
                'required', 'uuid',
                Rule::exists('students', 'id')->where('org_code', 'ga'),
            ],
            'topic_id' => [
                'nullable',
                Rule::exists('subjects', 'id')->where('org_code', 'ga')->where('kind', 'ga_coding_topic'),
            ],
            'total_problems' => ['required', 'integer', 'gt:0'],
            'solved_problems' => ['required', 'integer', 'min:0', 'lte:total_problems'],
        ];
    }
}
