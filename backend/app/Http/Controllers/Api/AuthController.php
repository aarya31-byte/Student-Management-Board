<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthController extends Controller
{
    public function __construct(private JwtService $jwt)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $admin = Admin::where('username', $request->string('username'))->first();

        if (! $admin || ! Hash::check($request->string('password'), $admin->password_hash)) {
            throw new HttpException(401, 'Invalid username or password.');
        }

        return response()->json([
            'token' => $this->jwt->issue($admin),
            'adminName' => $admin->full_name,
        ]);
    }

    // Stateless JWT — nothing to invalidate server-side. Kept for symmetry
    // and so a future token-revocation list has somewhere to hook in.
    public function logout(): JsonResponse
    {
        return response()->json(['message' => 'Logged out.']);
    }

    public function me(Request $request): JsonResponse
    {
        $admin = $request->attributes->get('admin');

        return response()->json([
            'username' => $admin->username,
            'full_name' => $admin->full_name,
            'role' => $admin->role,
            'org_code' => $admin->org_code,
        ]);
    }
}
