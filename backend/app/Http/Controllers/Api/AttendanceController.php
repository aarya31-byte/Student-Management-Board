<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function index(Request $request, string $org): JsonResponse
    {
        $query = Attendance::where('org_code', $org);

        if ($studentId = $request->query('studentId')) {
            $query->where('student_id', $studentId);
        }

        if ($date = $request->query('date')) {
            $query->where('session_date', $date);
        }

        return response()->json($query->orderBy('session_date', 'desc')->get());
    }

    public function store(StoreAttendanceRequest $request, string $org): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        // Enforced at the DB level too (unique (student_id, session_date)),
        // but checked here first for a clean, documented {"detail": ...}
        // message instead of a raw constraint-violation error.
        $exists = Attendance::where('student_id', $request->input('student_id'))
            ->where('session_date', $request->input('session_date'))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'session_date' => 'Attendance has already been marked for this student on this date.',
            ]);
        }

        $attendance = Attendance::create([
            'student_id' => $request->input('student_id'),
            'org_code' => $org,
            'session_date' => $request->input('session_date'),
            'status' => $request->input('status'),
            'created_by' => $admin->id,
        ]);

        return response()->json($attendance, 201);
    }

    public function update(UpdateAttendanceRequest $request, string $org, string $id): JsonResponse
    {
        $attendance = Attendance::where('org_code', $org)->findOrFail($id);

        $attendance->status = $request->input('status');
        $attendance->save();

        return response()->json($attendance);
    }

    public function destroy(string $org, string $id): JsonResponse
    {
        $attendance = Attendance::where('org_code', $org)->findOrFail($id);
        $attendance->delete();

        return response()->json(null, 204);
    }
}
