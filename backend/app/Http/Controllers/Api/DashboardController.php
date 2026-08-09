<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(string $org): JsonResponse
    {
        $row = $org === 'gt'
            ? DB::selectOne('select * from gt_dashboard_stats()')
            : DB::selectOne('select * from ga_dashboard_stats()');

        return response()->json($row);
    }
}
