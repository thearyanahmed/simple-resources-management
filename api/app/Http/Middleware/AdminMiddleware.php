<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    const ADMIN_EMAIL = 'admin@admin.com';

    public function handle(Request $request, Closure $next)
    {
        // pseudo auth
        if($request->header('user_email') === self::ADMIN_EMAIL) {
            return $next($request);
        }

        return response()->json(['error' => 'unauthorized.'],Response::HTTP_UNAUTHORIZED);
    }
}
