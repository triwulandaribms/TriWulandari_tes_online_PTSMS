<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function listUser(){
        try {
            $users = User::whereNull('deleted_at')->get();

            return response()->json([
                'status' => 'success',
                'data' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'username' => $user->username
                    ];
                })
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function registrasi(Request $request){

        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|string|email|unique:tbl_user,username', 
                'password' => 'required|string|min:8', 
            ],
            [
                'username.required' => 'Username wajib diisi.',
                'username.email' => 'Username harus berupa email yang valid.',
                'username.unique' => 'Username tidak boleh sama.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter (YYYYMMDD).',
            ]
        );
    
        $validator->after(function ($validator) use ($request) {

            if ($request->password === $request->username) {
                $validator->errors()->add('password', 'Password tidak boleh sama dengan username.');
            }
    
            if (!preg_match('/^\d{8}$/', $request->password)) {
                $validator->errors()->add('password', 'Password harus berupa tanggal lahir dengan format YYYYMMDD.');
            }
    
            if (!str_ends_with($request->username, '@gmail.com')) {
                $validator->errors()->add('username', 'Username harus berakhiran @gmail.com.');
            }
        });
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
    
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
        ], 201);
    }
    
    public function login(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                'username' => 'required|string|email',
                'password' => 'required|string|size:8',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.email' => 'Username harus berupa email yang valid.',
                'password.required' => 'Password wajib diisi.',
                'password.size' => 'Password harus 8 karakter (YYYYMMDD).',
            ]);
    
            $validator->after(function ($validator) use ($request) {
                if (!str_ends_with($request->username, '@gmail.com')) {
                    $validator->errors()->add('username', 'Username harus berakhiran @gmail.com.');
                }
    
                if (!preg_match('/^\d{8}$/', $request->password)) {
                    $validator->errors()->add('password', 'Password harus berupa tanggal lahir dengan format YYYYMMDD.');
                }
            });
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }
    
            $user = User::where('username', $request->username)->first();
    
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Username tidak ditemukan'
                ], 404);
            }
    
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password salah'
                ], 401);
            }
    
            $token = auth('api')->login($user);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: '.$e->getMessage()
            ], 500);
        }
    
    }

    public function update(Request $request, $id){
        try {
            $user = User::where('id', $id)->whereNull('deleted_at')->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan atau sudah dihapus'
                ], 404);
            }
    
            $validator = Validator::make($request->all(), [
                'username' => 'sometimes|required|string|email|unique:tbl_user,username,' . $id,
                'password' => 'sometimes|required|string|size:8',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.email' => 'Username harus berupa email yang valid.',
                'username.unique' => 'Username tidak boleh sama.',
                'password.required' => 'Password wajib diisi.',
                'password.size' => 'Password harus 8 karakter (YYYYMMDD).',
            ]);
    
            $validator->after(function ($validator) use ($request) {
                if (isset($request->username) && !str_ends_with($request->username, '@gmail.com')) {
                    $validator->errors()->add('username', 'Username harus berakhiran @gmail.com.');
                }
    
                if (isset($request->password)) {
                    if (!preg_match('/^\d{8}$/', $request->password)) {
                        $validator->errors()->add('password', 'Password harus berupa tanggal lahir dengan format YYYYMMDD.');
                    }
    
                    if (isset($request->username) && $request->password === $request->username) {
                        $validator->errors()->add('password', 'Password tidak boleh sama dengan username.');
                    }
                }
            });
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }
    
            $data = $request->only(['username', 'password']);
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
    
            $user->update($data);
    
            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil diupdate',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function hapus($id){
        try {
            $user = User::where('id', $id)->whereNull('deleted_at')->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan atau sudah dihapus'
                ], 404);
            }

            $user->deleted_at = now();
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(){
        try {
            auth('api')->logout();
            return response()->json([
                'status' => 'success',
                'message' => 'Logout berhasil'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}


