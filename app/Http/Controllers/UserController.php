<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon; 

class UserController extends Controller
{
    
    public function listUser(){
        try {
            $users = User::whereNull('deleted_at')->get();

            return response()->json([
                'status' => 'success',
                'data' => $users->map(fn($user) => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' =>$user->email,
                    'tanggal_lahir'=>$user->tanggal_lahir->format('Y-m-d')
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function registrasi(Request $request){

        $validasi = Validator::make($request->all(), [
            'username' => 'required|string',
            'email' => 'required|email|unique:tbl_user,email',
            'tanggal_lahir' => 'required|date_format:Y-m-d',
        ], [
            'username.required' => 'Username wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date_format' => 'Format tanggal lahir harus YYYY-MM-DD.',
        ]);
    
        if ($validasi->fails()) {
            return response()->json([
                'message' => $validasi->errors()->first()
            ], 400);
        }
    
        $passwordDummy = Hash::make($request->tanggal_lahir);
    
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => $passwordDummy,
            'tanggal_lahir' => $request->tanggal_lahir
        ]);
    
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'tanggal_lahir' => $user->tanggal_lahir->format('Y-m-d'),
            ]
        ], 201);
    }
    
    public function login(Request $request){
        $validasi = Validator::make($request->all(), [
            'username' => 'required|email',
            'password' => 'required|string|regex:/^\d{8}$/',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.email' => 'Username harus berupa email yang valid.',
            'password.required' => 'Password wajib diisi.',
            'password.regex' => 'Password harus berupa tanggal lahir dengan format YYYYMMDD.',
        ]);
    
        if ($validasi->fails()) {
            return response()->json([
                'message' => $validasi->errors()->first(),
            ], 400);
        }
    
        $user = User::where('email', $request->username)->first();
    
        if (!$user) {
            return response()->json([
                'message' => 'Username tidak ditemukan',
            ], 404);
        }
    
        $tanggalLahirFormatted = Carbon::parse($user->tanggal_lahir)->format('Ymd');
    
        if ($request->password !== $tanggalLahirFormatted) {
            return response()->json([
                'message' => 'Password salah.',
            ], 401);
        }
    
        $token = $user->createToken('auth_token')->plainTextToken;
    
        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
        ], 200);
    
    }

    public function update(Request $request, $id){
        try {
            $user = User::where('id', $id)->whereNull('deleted_at')->first();
    
            if (!$user) {
                return response()->json([
                    'message' => 'User tidak ditemukan atau sudah dihapus',
                ], 404);
            }
    
           $validasi = Validator::make($request->all(), [
                'username' => 'sometimes|required|string|unique:tbl_user,username,' . $id,
                'email' => 'sometimes|required|email|unique:tbl_user,email,' . $id,
                'tanggal_lahir' => 'sometimes|required|date_format:Y-m-d',
            ], [
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
                'tanggal_lahir.date_format' => 'Format tanggal lahir harus YYYY-MM-DD.',
            ]);
    
            if ($validasi->fails()) {
                return response()->json([
                    'message' =>$validasi->errors()->first(),
                ], 400);
            }
    
            $data = $request->only(['username', 'email', 'tanggal_lahir']);
    
            if (isset($data['tanggal_lahir'])) {
                $data['password'] = Hash::make($data['tanggal_lahir']);
            }
    
            $user->update($data);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Data user berhasil diupdate',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'tanggal_lahir' => $user->tanggal_lahir->format('Y-m-d'),
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function hapus($id){
        try {
            $user = User::where('id', $id)->whereNull('deleted_at')->first();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan atau sudah dihapus',
                ], 404);
            }

            $user->deleted_at = now();
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil',
        ]);
    }
  

   
}
