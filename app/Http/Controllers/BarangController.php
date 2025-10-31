<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller{
    
    public function index() { return Barang::all(); }
    public function show($id) { return Barang::findOrFail($id); }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'kode_barang'=>'required|string|unique:barang,kode_barang',
            'nama_barang'=>'required|string',
            'harga'=>'required|numeric'
        ]);

        if ($validator->fails()) return response()->json(['status'=>'error','message'=>$validator->errors()->first()],400);

        $barang = Barang::create($request->all());
        return response()->json(['status'=>'success','barang'=>$barang],201);
    }

    public function update(Request $request,$id) {
        $barang = Barang::findOrFail($id);
        $barang->update($request->only(['kode_barang','nama_barang','harga']));
        return response()->json(['status'=>'success','barang'=>$barang]);
    }

    public function destroy($id) {
        $barang = Barang::findOrFail($id);
        $barang->delete();
        return response()->json(['status'=>'success','message'=>'Barang berhasil dihapus']);
    }
}
