<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request, string $org): JsonResponse
    {
        $limit = min((int) $request->query('limit', 20), 100) ?: 20;
        $page = max((int) $request->query('page', 1), 1);
        $search = trim((string) $request->query('search', ''));

        $query = Student::query()
            ->where('org_code', $org)
            ->with('course');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('display_id', 'ilike', "%{$search}%")
                    ->orWhere('name', 'ilike', "%{$search}%")
                    ->orWhere('batch', 'ilike', "%{$search}%")
                    ->orWhere('duration', 'ilike', "%{$search}%")
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('name', 'ilike', "%{$search}%");
                    });
            });
        }

        $total = $query->count();

        $students = $query
            ->orderBy('created_at')
            ->forPage($page, $limit)
            ->get();

        return response()->json([
            'data' => $students,
            'meta' => ['page' => $page, 'limit' => $limit, 'total' => $total],
        ]);
    }

    public function show(string $org, string $id): JsonResponse
    {
        $student = Student::where('org_code', $org)->with('course')->findOrFail($id);

        return response()->json($student);
    }

    public function store(StoreStudentRequest $request, string $org): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        $student = Student::create([
            'org_code' => $org,
            'name' => $request->input('name'),
            'batch' => $request->input('batch'),
            'duration' => $request->input('duration'),
            'course_id' => $request->input('course_id'),
            'created_by' => $admin->id,
        ]);

        // display_id is populated by a DB trigger (trg_student_display_id),
        // not by this insert's in-memory attributes — refresh to pick it up.
        return response()->json($student->refresh()->load('course'), 201);
    }

    public function update(UpdateStudentRequest $request, string $org, string $id): JsonResponse
    {
        $student = Student::where('org_code', $org)->findOrFail($id);

        $student->fill($request->only(['name', 'batch', 'duration', 'course_id']));
        $student->save();

        return response()->json($student->load('course'));
    }

    public function destroy(string $org, string $id): JsonResponse
    {
        $student = Student::where('org_code', $org)->findOrFail($id);

        // Attendance/assessments/coding-practice/exam rows all have
        // `on delete cascade` FKs to students (backend_details.md §3/§4) —
        // deleting the student is enough, no manual cleanup needed.
        $student->delete();

        return response()->json(null, 204);
    }
}
