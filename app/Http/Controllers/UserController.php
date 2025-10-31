<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

class UserController extends Controller
{

    public function registrasi(Request $request){

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=>'error',
                'message'=>$validator->errors()->first()
            ],400);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status'=>'success',
            'message'=>'Registrasi berhasil',
            'data'=>$user
        ],201);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'username'=>'required|username',
            'password'=>'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','message'=>$validator->errors()->first()],400);
        }

        $password = Carbon::parse($request->tanggal_lahir)->format('Ymd');
        $credentials = [
            'username' => $request->username,
            'password' => $password
        ];

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['status'=>'error','message'=>'username atau password'],401);
        }

        return response()->json([
            'status'=>'success',
            'message'=>'Login berhasil',
            'token'=>$token,
            'user'=>auth('api')->user()
        ]);
    }


    public function update(Request $request, $id){

        $user = User::where('id', $id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan atau sudah dihapus'
            ], 404);
        }
    
        $validator = Validator::make($request->all(), [
            'username'  => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:6'
        ]);
    
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
            'user' => $user
        ]);
    }
    

    public function hapus($id) {
        $user = User::where('id',$id)->whereNull('deleted_at')->firstOrFail();
        $user->deleted_at = now();
        $user->save();
        return response()->json(['status'=>'success','message'=>'berhasil hapus']);
    }

    public function logout() {
        auth('api')->logout();
        return response()->json(['status'=>'success','message'=>'Logout berhasil']);
    }
}
