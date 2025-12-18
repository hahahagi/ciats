@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2>Dashboard CIATS</h2>
        <p class="text-muted">Selamat datang, {{ $user['name'] }}!</p>
    </div>
</div>

<div class="row">
    <!-- Statistik -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total User</h6>
                        <h2 class="mb-0" id="totalUsers">0</h2>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Inventaris</h6>
                        <h2 class="mb-0">0</h2>
                    </div>
                    <i class="bi bi-box fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Assets</h6>
                        <h2 class="mb-0">0</h2>
                    </div>
                    <i class="bi bi-tag fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Role Anda</h6>
                        <h4 class="mb-0 text-uppercase">{{ $user['role'] }}</h4>
                    </div>
                    <i class="bi bi-person-badge fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <h6>CIATS - Cloud Inventory & Asset Tracking System</h6>
                <p>Sistem ini menggunakan:</p>
                <ul>
                    <li>Laravel 12</li>
                    <li>Firebase Realtime Database</li>
                    <li>Custom Authentication</li>
                    <li>Role-based Access Control</li>
                </ul>
                <div class="alert alert-info">
                    <small>
                        <strong>Note:</strong> Data disimpan di Firebase Cloud Database.
                        Pastikan koneksi internet stabil.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Hak Akses Role</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Manajemen User</th>
                                <th>Inventaris</th>
                                <th>Laporan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-danger">Admin</span></td>
                                <td><i class="bi bi-check-lg text-success"></i></td>
                                <td><i class="bi bi-check-lg text-success"></i></td>
                                <td><i class="bi bi-check-lg text-success"></i></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">Operator</span></td>
                                <td><i class="bi bi-x-lg text-danger"></i></td>
                                <td><i class="bi bi-check-lg text-success"></i></td>
                                <td><i class="bi bi-check-lg text-success"></i></td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-success">Employee</span></td>
                                <td><i class="bi bi-x-lg text-danger"></i></td>
                                <td><i class="bi bi-x-lg text-danger"></i></td>
                                <td><i class="bi bi-x-lg text-danger"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fetch total users (contoh AJAX)
    document.addEventListener('DOMContentLoaded', function() {
        // Simulasi data
        setTimeout(() => {
            document.getElementById('totalUsers').textContent = '3';
        }, 1000);
    });
</script>
@endsection