@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/users/{{ $user['id'] }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $user['name']) }}" required>
                        @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email', $user['email']) }}" required>
                        @error('email')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Minimal 6 karakter</div>
                        @error('password')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation">
                    </div>
                    
                    <div class="mb-4">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user['role']) == $role ? 'selected' : '' }}>
                                {{ ucfirst($role) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                        <a href="/admin/users" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection