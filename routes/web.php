<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// === ROUTES MANAJEMEN ASET ===

// 1. Read (Menampilkan Daftar Aset)
Route::get('/assets', [AssetController::class, 'index']);

// 2. Create (Form Tambah & Proses Simpan)
// PENTING: Route 'create' harus ditaruh SEBELUM route '{id}'
Route::get('/assets/create', [AssetController::class, 'create']);
Route::post('/assets', [AssetController::class, 'store']);

// 3. Edit (Form Edit & Proses Update)
Route::get('/assets/{id}/edit', [AssetController::class, 'edit']); // Form Edit
Route::put('/assets/{id}', [AssetController::class, 'update']);    // Proses Update (Pakai PUT)

// 4. Delete (Hapus Data)
Route::delete('/assets/{id}', [AssetController::class, 'destroy']);
