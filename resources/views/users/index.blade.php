@extends('layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-8">
        <h2><i class="bi bi-people me-2"></i>Manajemen User</h2>
        <p class="text-muted">Kelola pengguna sistem CIATS</p>
    </div>
    <div class="col-4 text-end">
        <a href="/admin/users/create" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah User
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if(count($users) > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>
                            @if($user['role'] === 'admin')
                                <span class="badge bg-danger role-badge">Admin</span>
                            @elseif($user['role'] === 'operator')
                                <span class="badge bg-warning role-badge">Operator</span>
                            @else
                                <span class="badge bg-success role-badge">Employee</span>
                            @endif
                        </td>
                        <td>{{ $user['created_at'] }}</td>
                        <td>
                            @if($user['id'] !== $user['id']) <!-- Cegah hapus diri sendiri -->
                            <form action="/admin/users/{{ $user['id'] }}" method="POST" 
                                  class="d-inline" onsubmit="return confirm('Hapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <h5>Belum ada user</h5>
            <p class="text-muted">Tambahkan user baru untuk mulai menggunakan sistem</p>
            <a href="/admin/users/create" class="btn btn-primary">Tambah User Pertama</a>
        </div>
        @endif
    </div>
</div>

<div class="alert alert-info mt-3">
    <small>
        <i class="bi bi-info-circle"></i> 
        <strong>Informasi:</strong> Hanya role <span class="badge bg-danger">Admin</span> yang dapat mengelola user.
        Total user: <strong>{{ count($users) }}</strong>
    </small>
</div>
@endsection