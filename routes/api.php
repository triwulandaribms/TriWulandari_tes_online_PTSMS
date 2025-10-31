<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembelianController;

Route::post('/register', [UserController::class, 'registrasi']); 
Route::post('/login', [UserController::class, 'login']);       
Route::put('/update-user-by/{id}', [UserController::class, 'update']);
Route::delete('/delete-user-by/{id}', [UserController::class, 'hapus']); 
Route::post('/logout', [UserController::class, 'logout']);       



Route::middleware('auth.jwt')->group(function () {

    Route::get('/get-all/barang', [BarangController::class, 'index']);
    Route::post('/add-barang', [BarangController::class, 'store']);
    Route::get('/get-barang-by/{id}', [BarangController::class, 'show']);
    Route::put('/update-barang-by/{id}', [BarangController::class, 'update']);
    Route::delete('/delete-barang-by/{id}', [BarangController::class, 'destroy']); 

    Route::get('/get-all/pembelian', [PembelianController::class, 'index']);
    Route::get('/get-pembelian-by/{id}', [PembelianController::class, 'show']);
    Route::post('/add-pembelian', [PembelianController::class, 'store']); // 
    Route::put('/update-pembelian-by/{id}', [PembelianController::class, 'update']);
    Route::delete('/delete-pembelian-by/{id}', [PembelianController::class, 'destroy']); 

    Route::post('/logout', [UserController::class, 'logout']);
});
