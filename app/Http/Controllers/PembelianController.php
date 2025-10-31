<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembelianController extends Controller
{

    public function tampilAll(){

        $data = Pembelian::with('details.barang')->whereNull('deleted_at')->get();

        return $data->isEmpty()
            ? response()->json(['message' => 'Data pembelian tidak ditemukan'], 404)
            : response()->json(['status' => 'success', 'data' => $data]);
    }

    
    public function tampil($id){

        $pembelian = Pembelian::with('details.barang')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        return !$pembelian
            ? response()->json([ 'message' => 'Data pembelian tidak ditemukan'], 404)
            : response()->json(['status' => 'success', 'data' => $pembelian]);
    }


    public function tambah(Request $request){

        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }

        $barangIds = collect($request->details)->pluck('barang_id')->unique();
        $barangExist = \App\Models\Barang::whereIn('id', $barangIds)->pluck('id');
        $missingBarang = $barangIds->diff($barangExist);
    
        if ($missingBarang->isNotEmpty()) {
            return response()->json([
                'message' => 'Barang dengan ID ' . $missingBarang->implode(', ') . ' tidak ditemukan'
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($request) {
    
    
                $total_harga = collect($request->details)
                    ->sum(fn($d) => $d['qty'] * $d['harga']);
    
                $pembelian = Pembelian::create([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'total_harga' => $total_harga,
                ]);
    
                foreach ($request->details as $detail) {
                    PembelianDetail::create([
                        'pembelian_id' => $pembelian->id,
                        'kode_barang' => $detail['barang_id'], 
                        'qty' => $detail['qty'],
                        'harga' => $detail['harga'],
                    ]);
                }
    
                return $pembelian->load('details.barang');
            });
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $result->id,
                    'tanggal' => $result->tanggal,
                    'keterangan' => $result->keterangan,
                    'total_harga' => $result->total_harga,
                    'details' => $result->details->map(fn($d) => [
                        'id' => $d->id,
                        'kode_barang' => $d->barang->kode_barang,
                        'nama_barang' => $d->barang->nama_barang,
                        'qty' => $d->qty,
                        'harga' => $d->harga,
                    ])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id){
        
        $validator = Validator::make($request->all(), [
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.barang_id' => 'required|integer',
            'details.*.qty' => 'required|integer|min:1',
            'details.*.harga' => 'required|numeric|min:0'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
    
        $pembelian = Pembelian::find($id);
        if (!$pembelian) {
            return response()->json([
                'message' => 'Data pembelian tidak ditemukan'
            ], 404);
        }
    
        $barangIds = collect($request->details)->pluck('barang_id')->unique();
        $barangExist = \App\Models\Barang::whereIn('id', $barangIds)->pluck('id');
        $missingBarang = $barangIds->diff($barangExist);
    
        if ($missingBarang->isNotEmpty()) {
            return response()->json([
                'message' => 'Barang dengan ID ' . $missingBarang->implode(', ') . ' tidak ditemukan'
            ], 404);
        }

        try {
            $result = DB::transaction(function () use ($request, $pembelian) {
    
                $total_harga = collect($request->details)
                    ->sum(fn($d) => $d['qty'] * $d['harga']);
    
                $pembelian->update([
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'total_harga' => $total_harga,
                ]);
    
                PembelianDetail::where('pembelian_id', $pembelian->id)->delete();
    
                foreach ($request->details as $detail) {
                    PembelianDetail::create([
                        'pembelian_id' => $pembelian->id,
                        'kode_barang' => $detail['barang_id'], 
                        'qty' => $detail['qty'],
                        'harga' => $detail['harga'],
                    ]);
                }
    
                return $pembelian->load('details.barang');
            });
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'id' => $result->id,
                    'tanggal' => $result->tanggal,
                    'keterangan' => $result->keterangan,
                    'total_harga' => $result->total_harga,
                    'details' => $result->details->map(fn($d) => [
                        'id' => $d->id,
                        'kode_barang' => $d->barang->kode_barang,
                        'nama_barang' => $d->barang->nama_barang,
                        'qty' => $d->qty,
                        'harga' => $d->harga,
                    ])
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal update pembelian: ' . $e->getMessage(),
            ], 500);
        }
    }

    
    public function hapus($id){

        $pembelian = Pembelian::where('id', $id)->whereNull('deleted_at')->first();

        if (!$pembelian) {
            return response()->json(['status' => 'error', 'message' => 'Data pembelian tidak ditemukan'], 404);
        }

        $pembelian->delete();
        $pembelian->details()->delete();

        return response()->json(['status' => 'success', 'message' => 'Pembelian berhasil dihapus']);
    }
   

    public function report(Request $request){

        $tanggal = $request->query('tanggal');
        $kode_barang = $request->query('kode_barang');
    
        $query = DB::table('tbl_pembelian_detail as d')
            ->join('tbl_pembelian as p', 'd.pembelian_id', '=', 'p.id')
            ->join('tbl_barang as b', 'd.kode_barang', '=', 'b.id') 
            ->select(
                'p.tanggal',
                'b.kode_barang',
                'b.nama_barang',
                'd.harga as harga_satuan',
                DB::raw('SUM(d.qty) as total_qty'),
                DB::raw('SUM(d.harga * d.qty) as total_harga')
            )
            ->whereNull('p.deleted_at')
            ->whereNull('d.deleted_at')
            ->groupBy('p.tanggal', 'b.kode_barang', 'b.nama_barang', 'd.harga');
    
        if (!empty($tanggal)) {
            $query->whereDate('p.tanggal', $tanggal);
        }
    
        if (!empty($kode_barang)) {
            $query->where('b.kode_barang', $kode_barang);
        }
    
        $data = $query->get()->map(function ($item) {
            return [
                'tanggal' => $item->tanggal,
                'kode_barang' => $item->kode_barang,
                'nama_barang' => $item->nama_barang,
                'harga_satuan' => (float) $item->harga_satuan,
                'total_qty' => (int) $item->total_qty,
                'total_harga' => (float) $item->total_harga,
            ];
        });
    
        if ($data->isEmpty()) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

}
