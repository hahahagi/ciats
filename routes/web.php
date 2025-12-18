<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug route untuk testing Firebase
Route::get('/debug-firebase', function () {
    try {
        $path = config('firebase.credentials');
        
        $debugInfo = [
            'credentials_path' => $path,
            'file_exists' => file_exists($path) ? 'YES' : 'NO',
            'is_readable' => is_readable($path) ? 'YES' : 'NO',
            'full_path' => realpath($path) ?: 'NOT FOUND',
        ];
        
        dd($debugInfo);
        
    } catch (\Exception $e) {
        dd('Error: ' . $e->getMessage());
    }
});

// Protected routes - VERSI SEDERHANA TANPA MIDDLEWARE DULU
Route::get('/dashboard', function () {
    // Cek session manual dulu
    if (!session()->has('user')) {
        return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
    }
    
    $user = session()->get('user');
    return view('dashboard', [
        'user' => $user,
        'title' => 'Dashboard CIATS',
    ]);
});

// Admin routes - VERSI SEDERHANA DULU
Route::prefix('admin')->group(function () {
    Route::get('/users', function () {
        // Cek session manual
        if (!session()->has('user')) {
            return redirect('/login');
        }
        
        // Cek role manual
        $user = session()->get('user');
        if ($user['role'] !== 'admin') {
            session()->flash('error', 'Akses ditolak!');
            return redirect('/dashboard');
        }
        
        // Instansiasi controller
        $controller = new UserController();
        return $controller->index();
    });
    
    Route::get('/users/create', function () {
        if (!session()->has('user')) {
            return redirect('/login');
        }
        
        $user = session()->get('user');
        if ($user['role'] !== 'admin') {
            session()->flash('error', 'Akses ditolak!');
            return redirect('/dashboard');
        }
        
        $controller = new UserController();
        return $controller->create();
    });
    
    Route::post('/users', function (Illuminate\Http\Request $request) {
        if (!session()->has('user')) {
            return redirect('/login');
        }
        
        $user = session()->get('user');
        if ($user['role'] !== 'admin') {
            session()->flash('error', 'Akses ditolak!');
            return redirect('/dashboard');
        }
        
        $controller = new UserController();
        return $controller->store($request);
    });
    
    Route::delete('/users/{id}', function ($id) {
        if (!session()->has('user')) {
            return redirect('/login');
        }
        
        $user = session()->get('user');
        if ($user['role'] !== 'admin') {
            session()->flash('error', 'Akses ditolak!');
            return redirect('/dashboard');
        }
        
        $controller = new UserController();
        return $controller->destroy($id);
    });
});

// Route untuk testing session
Route::get('/check-session', function () {
    dd(session()->all());
});