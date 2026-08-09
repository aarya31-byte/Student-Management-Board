<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGtAssessmentRequest extends FormRequest
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
                Rule::exists('students', 'id')->where('org_code', 'gt'),
            ],
            'type' => ['sometimes', Rule::in(['assignment', 'project'])],
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => [
                'nullable',
                Rule::exists('subjects', 'id')->where('org_code', 'gt')->where('kind', 'gt_subject'),
            ],
            'total_marks' => ['required', 'numeric', 'gt:0'],
            'obtained_marks' => ['required', 'numeric', 'min:0', 'lte:total_marks'],
            'remark' => ['nullable', 'string'],
        ];
    }
}
