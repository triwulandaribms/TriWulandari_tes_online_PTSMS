<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembelianController;


    // USER
    Route::get('/list-user', [UserController::class, 'listUser']);       
    Route::post('/register', [UserController::class, 'registrasi']); 
    Route::post('/login', [UserController::class, 'login']);       
    Route::put('/update-user-by/{id}', [UserController::class, 'update']);
    Route::delete('/delete-user-by/{id}', [UserController::class, 'hapus']); 
    Route::post('/logout', [UserController::class, 'logout']);     
    
    // BARANG
    Route::get('/barang', [BarangController::class, 'tampilAll']);
    Route::post('/barang', [BarangController::class, 'tambah']);
    Route::get('/barang/{id}', [BarangController::class, 'tampil']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'hapus']);
    Route::get('/barang', [BarangController::class, 'tampilAll']); 


    // PEMBELIAN
    Route::get('/pembelian', [PembelianController::class, 'tampilAll']);
    Route::get('/pembelian/{id}', [PembelianController::class, 'tampil']);
    Route::post('/pembelian', [PembelianController::class, 'tambah']);
    Route::put('/pembelian/{id}', [PembelianController::class, 'update']);
    Route::delete('/pembelian/{id}', [PembelianController::class, 'hapus']);
    Route::get('/report/pembelian', [PembelianController::class, 'report']);