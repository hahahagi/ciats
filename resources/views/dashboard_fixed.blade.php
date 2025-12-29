@extends('layouts.app')

@section('title', $title)

@section('content')
<!-- Welcome Section -->
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 30px; color: white; margin-bottom: 30px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin: 0 0 8px 0; font-weight: 700; font-size: 28px;">Selamat Datang, {{ $user['name'] }}! 👋</h2>
            <p style="margin: 0; opacity: 0.95; font-size: 14px;">Anda login sebagai <strong style="text-transform: capitalize;">{{ $user['role'] }}</strong></p>
        </div>
        <div style="font-size: 60px; opacity: 0.9;">
            @if($user['role'] == 'admin')
                <i class="bi bi-shield-lock"></i>
            @elseif($user['role'] == 'operator')
                <i class="bi bi-person-badge"></i>
            @else
                <i class="bi bi-person"></i>
            @endif
        </div>
    </div>
</div>

<!-- Quick Stats -->
@if(in_array($user['role'], ['admin', 'operator']))
<div class="row mb-30" style="margin-bottom: 30px;">
    @php
    $assetsRef = app('firebase.database')->getReference('assets')->getValue();
    $totalAssets = $assetsRef ? count($assetsRef) : 0;
    $transactionsRef = app('firebase.database')->getReference('transactions')->getValue();
    
    $pending = 0;
    $active = 0;
    if ($transactionsRef) {
        foreach ($transactionsRef as $tx) {
            if (($tx['status'] ?? '') == 'waiting_approval') $pending++;
            if (($tx['status'] ?? '') == 'active') $active++;
        }
    }
    @endphp
    
    <div class="col-md-6 col-lg-3" style="margin-bottom: 15px;">
        <div class="stat-card" style="border-top-color: #667eea; border-top-width: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label" style="margin-bottom: 8px;">Total Aset</div>
                    <div class="stat-value" style="color: #667eea; font-size: 32px;">{{ $totalAssets }}</div>
                </div>
                <div class="stat-icon" style="color: #667eea; font-size: 40px;">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3" style="margin-bottom: 15px;">
        <div class="stat-card" style="border-top-color: #f39c12; border-top-width: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label" style="margin-bottom: 8px;">Menunggu Persetujuan</div>
                    <div class="stat-value" style="color: #f39c12; font-size: 32px;">{{ $pending }}</div>
                </div>
                <div class="stat-icon" style="color: #f39c12; font-size: 40px;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3" style="margin-bottom: 15px;">
        <div class="stat-card" style="border-top-color: #27ae60; border-top-width: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label" style="margin-bottom: 8px;">Peminjaman Aktif</div>
                    <div class="stat-value" style="color: #27ae60; font-size: 32px;">{{ $active }}</div>
                </div>
                <div class="stat-icon" style="color: #27ae60; font-size: 40px;">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3" style="margin-bottom: 15px;">
        <div class="stat-card" style="border-top-color: #3498db; border-top-width: 5px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div class="stat-label" style="margin-bottom: 8px;">Total Pengguna</div>
                    @php
                    $usersRef = app('firebase.database')->getReference('users')->getValue();
                    $totalUsers = $usersRef ? count($usersRef) : 0;
                    @endphp
                    <div class="stat-value" style="color: #3498db; font-size: 32px;">{{ $totalUsers }}</div>
                </div>
                <div class="stat-icon" style="color: #3498db; font-size: 40px;">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Quick Actions -->
<div class="card-custom" style="margin-bottom: 30px;">
    <div class="card-header">
        <i class="bi bi-lightning-fill"></i> Aksi Cepat
    </div>
    <div class="card-body">
        <div class="row" style="gap: 15px; display: flex; flex-wrap: wrap;">
            @if($user['role'] == 'employee')
                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.catalog') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div class="quick-action-title">Jelajahi Katalog</div>
                        <small style="color: #999; font-size: 12px; display: block;">Lihat semua aset yang tersedia</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.myRequests') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </div>
                        <div class="quick-action-title">Request Saya</div>
                        <small style="color: #999; font-size: 12px; display: block;">Lihat status permintaan Anda</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.activeLoans') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="quick-action-title">Peminjaman Aktif</div>
                        <small style="color: #999; font-size: 12px; display: block;">Kelola peminjaman Anda</small>
                    </a>
                </div>
            @endif

            @if(in_array($user['role'], ['operator', 'admin']))
                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('assets.index') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-box-seam-fill"></i>
                        </div>
                        <div class="quick-action-title">Kelola Aset</div>
                        <small style="color: #999; font-size: 12px; display: block;">Mengelola semua aset sistem</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.pendingApprovals') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="quick-action-title">Persetujuan Tertunda</div>
                        <small style="color: #999; font-size: 12px; display: block;">{{ $pending }} menunggu persetujuan</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('scanner.index') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-qr-code"></i>
                        </div>
                        <div class="quick-action-title">Scanner QR</div>
                        <small style="color: #999; font-size: 12px; display: block;">Scan kode QR aset</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.activeLoans') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="quick-action-title">Peminjaman Aktif</div>
                        <small style="color: #999; font-size: 12px; display: block;">{{ $active }} peminjaman sedang berlangsung</small>
                    </a>
                </div>
            @endif

            @if($user['role'] == 'admin')
                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('transactions.allTransactions') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="quick-action-title">Semua Transaksi</div>
                        <small style="color: #999; font-size: 12px; display: block;">Lihat riwayat semua transaksi</small>
                    </a>
                </div>

                <div style="flex: 0 1 calc(33.333% - 10px); min-width: 250px;">
                    <a href="{{ route('admin.users.index') }}" class="quick-action-card" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                        <div class="quick-action-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="quick-action-title">Manajemen Pengguna</div>
                        <small style="color: #999; font-size: 12px; display: block;">{{ $totalUsers }} pengguna terdaftar</small>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card-custom">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Aktivitas Terbaru
    </div>
    <div class="card-body">
        @php
        $recentTransactions = [];
        if ($transactionsRef) {
            foreach ($transactionsRef as $id => $tx) {
                $tx['id'] = $id;
                $recentTransactions[] = $tx;
            }
            usort($recentTransactions, function($a, $b) {
                return ($b['requested_at'] ?? 0) <=> ($a['requested_at'] ?? 0);
            });
            $recentTransactions = array_slice($recentTransactions, 0, 8);
        }
        @endphp

        @if(empty($recentTransactions))
            <div style="text-align: center; padding: 40px 20px; color: #999;">
                <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Tidak ada aktivitas terbaru</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 30%;"><i class="bi bi-box"></i> Aset</th>
                            <th style="width: 25%;"><i class="bi bi-person"></i> Pengguna</th>
                            <th style="width: 20%;"><i class="bi bi-bookmark"></i> Status</th>
                            <th style="width: 25%;"><i class="bi bi-calendar"></i> Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $tx)
                        <tr>
                            <td><strong>{{ $tx['asset_name'] ?? 'N/A' }}</strong></td>
                            <td>{{ $tx['user_name'] ?? 'N/A' }}</td>
                            <td>
                                @php
                                $status = $tx['status'] ?? '';
                                @endphp
                                @switch($status)
                                    @case('waiting_approval')
                                        <span class="badge-status badge-status-pending">Menunggu</span>
                                        @break
                                    @case('approved')
                                        <span class="badge-status badge-status-approved">Disetujui</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge-status badge-status-rejected">Ditolak</span>
                                        @break
                                    @case('active')
                                        <span class="badge-status badge-status-active">Aktif</span>
                                        @break
                                    @case('completed')
                                        <span class="badge-status badge-status-completed">Selesai</span>
                                        @break
                                    @default
                                        <span class="badge-status">{{ str_replace('_', ' ', ucfirst($status)) }}</span>
                                @endswitch
                            </td>
                            <td style="font-size: 13px;">{{ isset($tx['requested_at']) ? date('d/m/Y H:i', $tx['requested_at']) : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
.mb-30 { margin-bottom: 30px; }
</style>
@endsection
