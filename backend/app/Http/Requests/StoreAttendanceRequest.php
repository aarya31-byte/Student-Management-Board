<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $org = $this->route('org');

        return [
            'student_id' => [
                'required', 'uuid',
                Rule::exists('students', 'id')->where('org_code', $org),
            ],
            'session_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['present', 'absent'])],
        ];
    }
}
