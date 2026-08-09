<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGaCodingPracticeRequest;
use App\Http\Requests\UpdateGaCodingPracticeRequest;
use App\Models\GaCodingPractice;
use App\Support\MarksGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GaCodingPracticeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GaCodingPractice::query()->with('topic');

        if ($studentId = $request->query('studentId')) {
            $query->where('student_id', $studentId);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function store(StoreGaCodingPracticeRequest $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        $record = GaCodingPractice::create([
            'student_id' => $request->input('student_id'),
            'topic_id' => $request->input('topic_id'),
            'total_problems' => $request->input('total_problems'),
            'solved_problems' => $request->input('solved_problems'),
            'created_by' => $admin->id,
        ]);

        return response()->json($record->load('topic'), 201);
    }

    public function update(UpdateGaCodingPracticeRequest $request, string $id): JsonResponse
    {
        $record = GaCodingPractice::findOrFail($id);

        $record->fill($request->only(['topic_id', 'total_problems', 'solved_problems']));

        MarksGuard::ensure((float) $record->solved_problems, (float) $record->total_problems, 'solved_problems');

        $record->save();

        return response()->json($record->load('topic'));
    }

    public function destroy(string $id): JsonResponse
    {
        GaCodingPractice::findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
