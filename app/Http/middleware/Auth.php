<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthJwtMiddleware
{
    public function handle(Request $request, Closure $next){
        
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token sudah kadaluarsa'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak ditemukan atau terjadi kesalahan'
            ], 401);
        }

        return $next($request);
    }
}
