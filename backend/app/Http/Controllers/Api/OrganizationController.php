<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        $query = Organization::query()->orderBy('code');

        if ($admin->org_code !== null) {
            $query->where('code', $admin->org_code);
        }

        return response()->json($query->get(['code', 'name']));
    }
}
