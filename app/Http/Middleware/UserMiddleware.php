<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'user') {
            abort(403, 'Fitur ini hanya tersedia untuk akun user.');
        }

        return $next($request);
    }
}
