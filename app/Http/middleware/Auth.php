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
            return response()->json(['message' => 'Mohon masukkan token.'], 401);
        }

        $token = explode(' ', $authHeader)[1] ?? null;

        if (!$token) {
            return response()->json(['message' => 'Token tidak valid.'], 401);
        }

        try {

            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            $request->attributes->set('user', $decoded);

            $path = $request->path(); 
            $role = $decoded->role ?? null;

            if (str_contains($path, 'api/admin') && $role !== 'admin') {
                return response()->json(['message' => 'Akses ditolak. Hanya admin yang bisa mengakses.'], 403);
            }

            if (str_contains($path, 'api/seller') && $role !== 'seller') {
                return response()->json(['message' => 'Akses ditolak. Hanya seller yang bisa mengakses.'], 403);
            }

            if (str_contains($path, 'api/buyer') && $role !== 'buyer') {
                return response()->json(['message' => 'Akses ditolak. Hanya buyer yang bisa mengakses.'], 403);
            }

        } catch (Exception $e) {
            return response()->json(['message' => 'Token tidak valid: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
