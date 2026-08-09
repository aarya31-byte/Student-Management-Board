<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGaCodingPracticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic_id' => [
                'sometimes', 'nullable',
                Rule::exists('subjects', 'id')->where('org_code', 'ga')->where('kind', 'ga_coding_topic'),
            ],
            'total_problems' => ['sometimes', 'required', 'integer', 'gt:0'],
            'solved_problems' => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }
}
