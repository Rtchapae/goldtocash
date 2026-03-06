<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if user is admin or manager (both can access admin panel)
        if (! $user || (! $user->isAdmin() && ! $user->isManager())) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}


