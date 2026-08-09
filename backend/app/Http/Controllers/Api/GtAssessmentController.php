<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGtAssessmentRequest;
use App\Http\Requests\UpdateGtAssessmentRequest;
use App\Models\GtAssessment;
use App\Support\MarksGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GtAssessmentController extends Controller
{
    // Reads go through gt_assessment_results, not the gt_assessments table
    // directly — percentage/grade are computed there by Postgres
    // (grade_for_percentage()), the single source of truth for the bands in
    // backend_details.md §3, so this controller never recomputes them.
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('gt_assessment_results');

        if ($studentId = $request->query('studentId')) {
            $query->where('student_id', $studentId);
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(StoreGtAssessmentRequest $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        $assessment = GtAssessment::create([
            'student_id' => $request->input('student_id'),
            'type' => $request->input('type', 'assignment'),
            'name' => $request->input('name'),
            'subject_id' => $request->input('subject_id'),
            'total_marks' => $request->input('total_marks'),
            'obtained_marks' => $request->input('obtained_marks'),
            'remark' => $request->input('remark'),
            'created_by' => $admin->id,
        ]);

        return response()->json($this->resultRow($assessment->id), 201);
    }

    public function update(UpdateGtAssessmentRequest $request, string $id): JsonResponse
    {
        $assessment = GtAssessment::findOrFail($id);

        $assessment->fill($request->only(['type', 'name', 'subject_id', 'total_marks', 'obtained_marks', 'remark']));

        MarksGuard::ensure((float) $assessment->obtained_marks, (float) $assessment->total_marks, 'obtained_marks');

        $assessment->save();

        return response()->json($this->resultRow($assessment->id));
    }

    public function destroy(string $id): JsonResponse
    {
        GtAssessment::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    private function resultRow(string $id): object
    {
        return DB::table('gt_assessment_results')->where('id', $id)->first();
    }
}
