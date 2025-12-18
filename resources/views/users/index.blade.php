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
                                <span class="badge bg-danger">Admin</span>
                            @elseif($user['role'] === 'operator')
                                <span class="badge bg-warning">Operator</span>
                            @else
                                <span class="badge bg-success">Employee</span>
                            @endif
                        </td>
                        <td>{{ $user['created_at'] }}</td>
                        <td>
                            <div class="btn-group" role="group">

                                <!-- Edit -->
                                <a href="/admin/users/{{ $user['id'] }}/edit"
                                   class="btn btn-sm btn-warning me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>

                                <!-- Delete -->
                                @if($user['email'] !== session('user')['email'])
                                <form action="/admin/users/{{ $user['id'] }}"
                                      method="POST"
                                      class="d-inline delete-form"
                                      data-name="{{ $user['name'] }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-secondary" disabled
                                        title="Tidak dapat menghapus akun sendiri">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                                @endif

                            </div>
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

<div class="row mt-3">
    <div class="col-md-6">
        <div class="alert alert-info">
            <small>
                <i class="bi bi-info-circle"></i>
                <strong>Informasi:</strong>
                Hanya role <span class="badge bg-danger">Admin</span> yang dapat mengelola user.
                Total user: <strong>{{ count($users) }}</strong>
            </small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-warning">
            <small>
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Perhatian:</strong>
                Anda tidak dapat menghapus akun sendiri.
            </small>
        </div>
    </div>
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const name = this.dataset.name;
            if (!confirm(`Yakin hapus user "${name}"?\nTindakan ini tidak dapat dibatalkan.`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
