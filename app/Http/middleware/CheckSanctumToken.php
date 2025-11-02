<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSanctumToken
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->bearerToken() || ! Auth::guard('sanctum')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mohon login dulu atau masukkan token yang valid.'
            ], 401);
        }

        return $next($request);
    }
}
