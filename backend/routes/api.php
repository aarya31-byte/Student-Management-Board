<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GaCodingPracticeController;
use App\Http\Controllers\Api\GaFinalExamController;
use App\Http\Controllers\Api\GtAssessmentController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

// -- Auth -------------------------------------------------------------
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth.jwt')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    // -- Organizations --------------------------------------------------
    Route::get('organizations', [OrganizationController::class, 'index']);

    // -- Routes shared by both orgs (Students, Attendance, Results, Dashboard) --
    Route::prefix('orgs/{org}')
        ->whereIn('org', ['gt', 'ga'])
        ->middleware('org.access')
        ->group(function () {
            Route::get('students', [StudentController::class, 'index']);
            Route::get('students/{id}/result', [ResultController::class, 'show']);
            Route::get('students/{id}', [StudentController::class, 'show']);
            Route::post('students', [StudentController::class, 'store']);
            Route::put('students/{id}', [StudentController::class, 'update']);
            Route::delete('students/{id}', [StudentController::class, 'destroy']);

            Route::get('attendance', [AttendanceController::class, 'index']);
            Route::post('attendance', [AttendanceController::class, 'store']);
            Route::put('attendance/{id}', [AttendanceController::class, 'update']);
            Route::delete('attendance/{id}', [AttendanceController::class, 'destroy']);

            Route::get('results', [ResultController::class, 'index']);

            Route::get('dashboard/stats', [DashboardController::class, 'stats']);

            Route::get('courses', [LookupController::class, 'courses']);
            Route::get('subjects', [LookupController::class, 'subjects']);
        });

    // -- GT-only: Assessments (merged Assignments + Marks, see §8) -------
    Route::prefix('orgs/{org}')
        ->whereIn('org', ['gt'])
        ->middleware('org.access')
        ->group(function () {
            Route::get('assessments', [GtAssessmentController::class, 'index']);
            Route::post('assessments', [GtAssessmentController::class, 'store']);
            Route::put('assessments/{id}', [GtAssessmentController::class, 'update']);
            Route::delete('assessments/{id}', [GtAssessmentController::class, 'destroy']);
        });

    // -- GA-only: Coding Practice + Final Exam ---------------------------
    Route::prefix('orgs/{org}')
        ->whereIn('org', ['ga'])
        ->middleware('org.access')
        ->group(function () {
            Route::get('coding-practice', [GaCodingPracticeController::class, 'index']);
            Route::post('coding-practice', [GaCodingPracticeController::class, 'store']);
            Route::put('coding-practice/{id}', [GaCodingPracticeController::class, 'update']);
            Route::delete('coding-practice/{id}', [GaCodingPracticeController::class, 'destroy']);

            Route::get('final-exam', [GaFinalExamController::class, 'index']);
            Route::post('final-exam', [GaFinalExamController::class, 'store']);
            Route::put('final-exam/{id}', [GaFinalExamController::class, 'update']);
            Route::delete('final-exam/{id}', [GaFinalExamController::class, 'destroy']);
        });
});
