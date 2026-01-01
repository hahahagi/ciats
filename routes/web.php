<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\LocationController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;

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
// Category Routes
// =======================
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// =======================
// Location Routes
// =======================
Route::prefix('locations')->group(function () {
    Route::get('/', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('/', [LocationController::class, 'store'])->name('locations.store');
    Route::get('/{id}/edit', [LocationController::class, 'edit'])->name('locations.edit');
    Route::put('/{id}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');
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
// Report Routes
// =======================
Route::prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
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
