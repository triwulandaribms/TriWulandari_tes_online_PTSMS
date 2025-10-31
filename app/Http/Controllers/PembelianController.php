<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{
    public function tampilAll(){
        $pembelian = Pembelian::with('details')->whereNull('deleted_at')->get();
        return response()->json([
            'status' => 'success',
            'data' => $pembelian
        ]);
    }

    public function tampil($id){
        $pembelian = Pembelian::with('details')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$pembelian) {
            return response()->json([
                'status' => 'error',
                'message' => 'data pembelian tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $pembelian
        ]);
    }

    
    public function tambah(Request $request){
        $validator = Validator::make($request->all(), [
            'tanggal'       => 'required|date',
            'keterangan'    => 'required|string',
            'details'       => 'required|array|min:1',
            'details.*.kode_barang' => 'required|exists:tbl_barang,kode_barang',
            'details.*.qty'         => 'required|integer|min:1',
            'details.*.harga'       => 'required|numeric|min:0'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
    
        DB::beginTransaction();
    
        try {
            $total_harga = collect($request->details)->sum(function($item){
                return $item['qty'] * $item['harga'];
            });
    
            $header = Pembelian::create([
                'tanggal'    => $request->tanggal,
                'keterangan' => $request->keterangan,
                'total_harga'=> $total_harga
            ]);
    
            foreach ($request->details as $detail) {
                PembelianDetail::create([
                    'pembelian_id' => $header->id,
                    'kode_barang'  => $detail['kode_barang'],
                    'qty'          => $detail['qty'],
                    'harga'        => $detail['harga']
                ]);
            }
    
            DB::commit();
    
            $header->load('details.barang');
    
            return response()->json([
                'status' => 'success',
                'data' => $header
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan pembelian: '.$e->getMessage()
            ], 500);
        }
    }
    

    public function update(Request $request, $id){
        $pembelian = Pembelian::with('details')->where('id', $id)->whereNull('deleted_at')->first();

        if (!$pembelian) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'tanggal'    => 'sometimes|required|date',
            'keterangan' => 'sometimes|required|string',
            'details'    => 'sometimes|required|array|min:1',
            'details.*.kode_barang' => 'required_with:details|exists:tbl_barang,kode_barang',
            'details.*.qty'         => 'required_with:details|integer|min:1',
            'details.*.harga'       => 'required_with:details|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($request->has('tanggal')) {
                $pembelian->tanggal = $request->tanggal;
            }
            if ($request->has('keterangan')) {
                $pembelian->keterangan = $request->keterangan;
            }

            if ($request->has('details')) {
                $pembelian->details()->delete();

                foreach ($request->details as $detail) {
                    PembelianDetail::create([
                        'pembelian_id' => $pembelian->id,
                        'kode_barang'  => $detail['kode_barang'],
                        'qty'          => $detail['qty'],
                        'harga'        => $detail['harga']
                    ]);
                }

                $pembelian->total_harga = collect($request->details)->sum(function($item){
                    return $item['qty'] * $item['harga'];
                });
            }

            $pembelian->save();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $pembelian->load('details')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update pembelian: '.$e->getMessage()
            ], 500);
        }
    }

    public function hapus($id){
        $pembelian = Pembelian::where('id', $id)->whereNull('deleted_at')->first();

        if (!$pembelian) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pembelian tidak ditemukan'
            ], 404);
        }

        $pembelian->deleted_at = now();
        $pembelian->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Pembelian berhasil dihapus'
        ]);
    }

    public function report(Request $request){
        $tanggal      = $request->query('tanggal');
        $kode_barang  = $request->query('kode_barang');

        $query = PembelianDetail::with('pembelian', 'barang')
            ->whereHas('pembelian', function($q) use($tanggal){
                if ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                }
            });

        if ($kode_barang) {
            $query->where('kode_barang', $kode_barang);
        }

        $data = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
