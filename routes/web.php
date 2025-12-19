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
// Helper Functions (Temp)
// =======================
function checkAuth() {
    if (!session()->has('user')) {
        return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
    }
    return null;
}

function checkRole($roles) {
    $user = session()->get('user');
    if (!$user || !in_array($user['role'], $roles)) {
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
    return null;
}

// =======================
// Asset Routes
// =======================
Route::prefix('assets')->group(function () {
    // Semua role bisa lihat
    Route::get('/', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::get('/{id}/print', [AssetController::class, 'printQR'])->name('assets.printQR');
    
    // Hanya operator yang bisa CRUD
    Route::get('/create', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator']);
        if ($roleCheck) return $roleCheck;
        return app(AssetController::class)->create();
    })->name('assets.create');
    
    Route::post('/', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator']);
        if ($roleCheck) return $roleCheck;
        return app(AssetController::class)->store(request());
    })->name('assets.store');
    
    Route::get('/{id}/edit', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator']);
        if ($roleCheck) return $roleCheck;
        return app(AssetController::class)->edit($id);
    })->name('assets.edit');
    
    Route::put('/{id}', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator']);
        if ($roleCheck) return $roleCheck;
        return app(AssetController::class)->update(request(), $id);
    })->name('assets.update');
    
    Route::delete('/{id}', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator']);
        if ($roleCheck) return $roleCheck;
        return app(AssetController::class)->destroy($id);
    })->name('assets.destroy');
});

// =======================
// Transaction Routes
// =======================
Route::prefix('transactions')->group(function () {
    // Untuk semua karyawan
    Route::get('/catalog', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        return app(TransactionController::class)->catalog();
    })->name('transactions.catalog');
    
    Route::get('/request/{assetId}', function ($assetId) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        return app(TransactionController::class)->requestForm($assetId);
    })->name('transactions.requestForm');
    
    Route::post('/request', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        return app(TransactionController::class)->submitRequest(request());
    })->name('transactions.submitRequest');
    
    Route::get('/my-requests', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        return app(TransactionController::class)->myRequests();
    })->name('transactions.myRequests');
    
    // Untuk operator & admin
    Route::get('/pending', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator', 'admin']);
        if ($roleCheck) return $roleCheck;
        return app(TransactionController::class)->pendingApprovals();
    })->name('transactions.pendingApprovals');
    
    Route::get('/active', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['operator', 'admin']);
        if ($roleCheck) return $roleCheck;
        return app(TransactionController::class)->activeLoans();
    })->name('transactions.activeLoans');
    
    // Hanya untuk admin
    Route::get('/all', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(TransactionController::class)->allTransactions();
    })->name('transactions.allTransactions');
});

// =======================
// Admin Routes (User Management)
// =======================
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->index();
    })->name('admin.users.index');
    
    Route::get('/users/create', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->create();
    })->name('admin.users.create');
    
    Route::post('/users', function () {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->store(request());
    })->name('admin.users.store');
    
    Route::get('/users/{id}/edit', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->edit($id);
    })->name('admin.users.edit');
    
    Route::put('/users/{id}', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->update(request(), $id);
    })->name('admin.users.update');
    
    Route::delete('/users/{id}', function ($id) {
        $authCheck = checkAuth();
        if ($authCheck) return $authCheck;
        $roleCheck = checkRole(['admin']);
        if ($roleCheck) return $roleCheck;
        return app(UserController::class)->destroy($id);
    })->name('admin.users.destroy');
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

Route::get('/check-session', function () {
    dd(session()->all());
});

// =======================
// Fallback Route
// =======================
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});