<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function courses(string $org): JsonResponse
    {
        $courses = Course::where('org_code', $org)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($courses);
    }

    // ?kind=gt_subject|ga_exam_subject|ga_coding_topic — the same lookup
    // table backs all three, distinguished by kind (backend_details.md §4).
    public function subjects(Request $request, string $org): JsonResponse
    {
        $query = Subject::where('org_code', $org)->orderBy('name');

        if ($kind = $request->query('kind')) {
            $query->where('kind', $kind);
        }

        return response()->json($query->get(['id', 'name', 'kind']));
    }
}
