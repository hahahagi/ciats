<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransactionController;

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
// Dashboard Routes
// =======================
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// =======================
// Asset Routes
// =======================
Route::prefix('assets')->group(function () {
    // Public / All Roles (Controller handles auth check)
    Route::get('/', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/create', [AssetController::class, 'create'])->name('assets.create'); // Operator only
    Route::post('/', [AssetController::class, 'store'])->name('assets.store'); // Operator only
    Route::get('/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('assets.edit'); // Operator only
    Route::put('/{id}', [AssetController::class, 'update'])->name('assets.update'); // Operator only
    Route::delete('/{id}', [AssetController::class, 'destroy'])->name('assets.destroy'); // Operator only
    Route::get('/{id}/print', [AssetController::class, 'printQR'])->name('assets.printQR');
});

// =======================
// Transaction Routes
// =======================
Route::prefix('transactions')->group(function () {
    // Employee / All
    Route::get('/catalog', [TransactionController::class, 'catalog'])->name('transactions.catalog');
    Route::get('/request/{assetId}', [TransactionController::class, 'requestForm'])->name('transactions.requestForm');
    Route::post('/request', [TransactionController::class, 'submitRequest'])->name('transactions.submitRequest');
    Route::get('/my-requests', [TransactionController::class, 'myRequests'])->name('transactions.myRequests');

    // Operator & Admin
    Route::get('/pending', [TransactionController::class, 'pendingApprovals'])->name('transactions.pendingApprovals');
    Route::post('/{id}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/{id}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');

    Route::get('/{id}/checkout', [TransactionController::class, 'checkoutForm'])->name('transactions.checkoutForm');
    Route::post('/{id}/checkout', [TransactionController::class, 'processCheckout'])->name('transactions.processCheckout');

    Route::get('/{id}/checkin', [TransactionController::class, 'checkinForm'])->name('transactions.checkinForm');
    Route::post('/{id}/checkin', [TransactionController::class, 'processCheckin'])->name('transactions.processCheckin');

    Route::get('/active', [TransactionController::class, 'activeLoans'])->name('transactions.activeLoans');

    // Admin Only
    Route::get('/all', [TransactionController::class, 'allTransactions'])->name('transactions.allTransactions');
});

// =======================
// Scanner Routes
// =======================
Route::get('/scanner', [TransactionController::class, 'scanner'])->name('scanner.index');
Route::post('/scanner', [TransactionController::class, 'handleScan'])->name('scanner.handle');

// =======================
// Admin Routes (User Management)
// =======================
Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// =======================
// Debug Routes
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
// Fallback Route
// =======================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
