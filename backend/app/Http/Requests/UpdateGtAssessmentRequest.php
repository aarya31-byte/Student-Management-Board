<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGtAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['sometimes', Rule::in(['assignment', 'project'])],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'subject_id' => [
                'sometimes', 'nullable',
                Rule::exists('subjects', 'id')->where('org_code', 'gt')->where('kind', 'gt_subject'),
            ],
            'total_marks' => ['sometimes', 'required', 'numeric', 'gt:0'],
            'obtained_marks' => ['sometimes', 'required', 'numeric', 'min:0'],
            'remark' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
