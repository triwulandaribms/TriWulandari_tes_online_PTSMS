<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSanctumToken
{
    public function handle(Request $request, Closure $next){
        
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mohon masukkan token.'
            ], 401);
        }

        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid.'
            ], 401);
        }

        return $next($request);
    }
}
