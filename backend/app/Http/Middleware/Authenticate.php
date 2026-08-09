<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Services\JwtService;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function __construct(private JwtService $jwt)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');

        if (! str_starts_with($header, 'Bearer ')) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $token = substr($header, 7);

        try {
            $adminId = $this->jwt->subjectId($token);
        } catch (RuntimeException $e) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $admin = Admin::find($adminId);

        if (! $admin) {
            throw new AuthenticationException('Unauthenticated.');
        }

        $request->attributes->set('admin', $admin);

        return $next($request);
    }
}
