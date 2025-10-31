<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthJwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader) {
            return response()->json(['status'=>'error','message' => 'Mohon masukkan token.'], 401);
        }

        $token = explode(' ', $authHeader)[1] ?? null;

        if (!$token) {
            return response()->json(['status'=>'error','message' => 'Token tidak valid.'], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $request->attributes->set('user', $decoded);

        } catch (Exception $e) {
            return response()->json([
                'status'=>'error',
                'message' => 'Token tidak valid: ' . $e->getMessage()
            ], 401);
        }

        return $next($request);
    }
}
