<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGaFinalExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => [
                'sometimes', 'nullable',
                Rule::exists('subjects', 'id')->where('org_code', 'ga')->where('kind', 'ga_exam_subject'),
            ],
            'total_marks' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'obtained_marks' => ['sometimes', 'required', 'numeric', 'min:0'],
        ];
    }
}
