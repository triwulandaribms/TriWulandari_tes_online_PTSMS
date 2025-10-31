<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    public function index(){
        $barang = Barang::whereNull('deleted_at')->get();
        return response()->json($barang);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'kode_barang' => 'required|string|unique:tbl_barang,kode_barang',
            'nama_barang' => 'required|string',
            'harga'       => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','message'=>$validator->errors()->first()],400);
        }

        $barang = Barang::create($request->all());

        return response()->json(['status'=>'success','data'=>$barang]);
    }

    public function show($id){
        $barang = Barang::where('id',$id)->whereNull('deleted_at')->firstOrFail();
        return response()->json($barang);
    }

    public function update(Request $request, $id){
        $barang = Barang::where('id',$id)->whereNull('deleted_at')->firstOrFail();

        $validator = Validator::make($request->all(), [
            'kode_barang' => 'sometimes|required|string|unique:tbl_barang,kode_barang,'.$id,
            'nama_barang' => 'sometimes|required|string',
            'harga'       => 'sometimes|required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=>'error','message'=>$validator->errors()->first()],400);
        }

        $barang->update($request->all());
        return response()->json(['status'=>'success','data'=>$barang]);
    }

    public function hapus($id){
        $barang = Barang::where('id',$id)->whereNull('deleted_at')->firstOrFail();
        $barang->deleted_at = now();
        $barang->save();
        return response()->json(['status'=>'success','message'=>'Barang berhasil dihapus']);
    }
}
