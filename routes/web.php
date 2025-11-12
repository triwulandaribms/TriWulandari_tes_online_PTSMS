<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn() => view('user.login'));
Route::get('/register', fn() => view('user.register'));
Route::get('/barang', fn() => view('barang.index'));
Route::get('/barang/form', fn() => view('barang.form'));
Route::get('/pembelian', fn() => view('pembelian.index'));
Route::get('/report', fn() => view('pembelian.report'));
Route::get('/pembelian/form', fn() => view('pembelian.form'));
