<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =======================
// Public Routes
// =======================
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =======================
// Debug Firebase (optional)
// =======================
Route::get('/debug-firebase', function () {
    try {
        $path = config('firebase.credentials');

        dd([
            'credentials_path' => $path,
            'file_exists'      => file_exists($path) ? 'YES' : 'NO',
            'is_readable'      => is_readable($path) ? 'YES' : 'NO',
            'full_path'        => realpath($path) ?: 'NOT FOUND',
        ]);
    } catch (\Exception $e) {
        dd('Error: ' . $e->getMessage());
    }
});


// =======================
// Dashboard (manual session check)
// =======================
Route::get('/dashboard', function () {
    if (!session()->has('user')) {
        return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
    }

    return view('dashboard', [
        'user'  => session()->get('user'),
        'title' => 'Dashboard CIATS',
    ]);
});


// =======================
// Admin Routes (User Management)
// =======================
Route::prefix('admin')->group(function () {

    // List user
    Route::get('/users', [UserController::class, 'index']);

    // Form tambah user
    Route::get('/users/create', [UserController::class, 'create']);

    // Simpan user baru
    Route::post('/users', [UserController::class, 'store']);

    // ===============================
    // 🔥 ROUTE EDIT & UPDATE (FIX)
    // ===============================

    // Form edit user
    Route::get('/users/{id}/edit', [UserController::class, 'edit']);

    // Update user
    Route::put('/users/{id}', [UserController::class, 'update']);

    // Hapus user
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});


// =======================
// Debug Session
// =======================
Route::get('/check-session', function () {
    dd(session()->all());
});
