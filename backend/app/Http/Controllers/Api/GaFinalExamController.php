<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGaFinalExamRequest;
use App\Http\Requests\UpdateGaFinalExamRequest;
use App\Models\GaFinalExam;
use App\Support\MarksGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GaFinalExamController extends Controller
{
    // Reads go through ga_final_exam_results — percentage/grade/status are
    // computed there by Postgres, mirroring GtAssessmentController.
    public function index(Request $request): JsonResponse
    {
        $query = DB::table('ga_final_exam_results');

        if ($studentId = $request->query('studentId')) {
            $query->where('student_id', $studentId);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(StoreGaFinalExamRequest $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        $exam = GaFinalExam::create([
            'student_id' => $request->input('student_id'),
            'subject_id' => $request->input('subject_id'),
            'total_marks' => $request->input('total_marks'),
            'obtained_marks' => $request->input('obtained_marks'),
            'created_by' => $admin->id,
        ]);

        return response()->json($this->resultRow($exam->id), 201);
    }

    public function update(UpdateGaFinalExamRequest $request, string $id): JsonResponse
    {
        $exam = GaFinalExam::findOrFail($id);

        $exam->fill($request->only(['subject_id', 'total_marks', 'obtained_marks']));

        MarksGuard::ensure((float) $exam->obtained_marks, (float) $exam->total_marks, 'obtained_marks');

        $exam->save();

        return response()->json($this->resultRow($exam->id));
    }

    public function destroy(string $id): JsonResponse
    {
        GaFinalExam::findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    private function resultRow(string $id): object
    {
        return DB::table('ga_final_exam_results')->where('id', $id)->first();
    }
}
