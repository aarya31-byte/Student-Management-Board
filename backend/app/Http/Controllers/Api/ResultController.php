<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function show(string $org, string $id): JsonResponse
    {
        // Confirms the student exists in this org first, so an invalid id
        // 404s cleanly rather than silently returning an empty summary row.
        Student::where('org_code', $org)->findOrFail($id);

        $result = $this->summaryQuery($org)->where('r.student_id', $id)->first();

        return response()->json($result);
    }

    public function index(string $org): JsonResponse
    {
        return response()->json($this->summaryQuery($org)->get());
    }

    private function summaryQuery(string $org)
    {
        if ($org === 'gt') {
            return DB::table('gt_student_result_summary as r')
                ->join('students as s', 's.id', '=', 'r.student_id')
                ->orderBy('s.display_id')
                ->select(
                    's.id as student_id',
                    's.display_id',
                    's.name',
                    's.batch',
                    'r.total_marks',
                    'r.obtained_marks',
                    'r.percentage',
                    'r.overall_remark'
                );
        }

        return DB::table('ga_student_result_summary as r')
            ->join('students as s', 's.id', '=', 'r.student_id')
            ->orderBy('s.display_id')
            ->select(
                's.id as student_id',
                's.display_id',
                's.name',
                's.batch',
                'r.coding_solved',
                'r.coding_total',
                'r.coding_percentage',
                'r.exam_obtained',
                'r.exam_total',
                'r.exam_percentage',
                'r.exam_grade',
                'r.status'
            );
    }
}
