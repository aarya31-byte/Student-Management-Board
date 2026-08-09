<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrgAccess
{
    /**
     * The only access control in the system (backend_details.md §5): an
     * admin scoped to one org (org_code not null) may never touch the
     * other org's routes. org_code null means access to both.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->attributes->get('admin');
        $org = $request->route('org');

        if (! $admin->hasAccessTo($org)) {
            throw new AuthorizationException("You do not have access to the '{$org}' organization.");
        }

        return $next($request);
    }
}
