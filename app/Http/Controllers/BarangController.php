<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    
    public function tampilAll(Request $request){
        try {
            $barang = Barang::whereNull('deleted_at')->get();
            return response()->json([
                'status' => 'success',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tampil($id){

        try {
            $barang = Barang::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$barang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tambah(Request $request){

        try {
            $validator = Validator::make($request->all(), [
                'kode_barang' => 'required|string',
                'nama_barang' => 'required|string',
                'harga'       => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $existing = Barang::where('kode_barang', $request->kode_barang)
                ->whereNull('deleted_at')
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kode barang tidak boleh sama'
                ], 400);
            }

            $barang = Barang::create($request->all());

            return response()->json([
                'status' => 'success',
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id){

        try {
            $barang = Barang::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$barang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kode_barang' => 'sometimes|required|string',
                'nama_barang' => 'sometimes|required|string',
                'harga'       => 'sometimes|required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            if ($request->has('kode_barang')) {
                $existing = Barang::where('kode_barang', $request->kode_barang)
                    ->where('id', '<>', $id)
                    ->whereNull('deleted_at')
                    ->first();

                if ($existing) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kode barang sudah digunakan'
                    ], 400);
                }
            }

            $barang->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Berhasil update'
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
            $barang = Barang::where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$barang) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $barang->deleted_at = now();
            $barang->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
