<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembelianController;

Route::prefix('api')->group(function () {

    // Auth
    Route::post('/register', [UserController::class, 'registrasi']); 
    Route::post('/login', [UserController::class, 'login']);       

    Route::middleware('auth.jwt')->group(function () {

        // User
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'hapus']); 
        Route::post('/logout', [UserController::class, 'logout']);       

        // Barang
        Route::get('/barang', [BarangController::class, 'index']);
        Route::post('/barang', [BarangController::class, 'store']);
        Route::get('/barang/{id}', [BarangController::class, 'show']);
        Route::put('/barang/{id}', [BarangController::class, 'update']);
        Route::delete('/barang/{id}', [BarangController::class, 'hapus']); 

        // Pembelian
        Route::get('/pembelian', [PembelianController::class, 'index']);
        Route::get('/pembelian/{id}', [PembelianController::class, 'show']);
        Route::post('/pembelian', [PembelianController::class, 'store']); 
        Route::put('/pembelian/{id}', [PembelianController::class, 'update']);
        Route::delete('/pembelian/{id}', [PembelianController::class, 'hapus']); 

        Route::get('/report/pembelian', [PembelianController::class, 'report']);
    });
});
