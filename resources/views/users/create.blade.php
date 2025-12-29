@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('admin.users.index') }}"
        class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6 text-sm sm:text-base">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>

    <div class="bg-white rounded-xl shadow-xl p-4 sm:p-8">
        <div class="flex items-center space-x-3 mb-6">
            <div class="bg-blue-100 p-3 rounded-lg">
                <i class="fas fa-user-plus text-blue-600 text-xl sm:text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Tambah User Baru</h1>
                <p class="text-sm text-gray-600">Lengkapi form untuk menambah user</p>
            </div>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm sm:text-base">
                    <i class="fas fa-user text-blue-600 mr-2"></i>
                    Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm sm:text-base"
                    placeholder="Nama lengkap" required>
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm sm:text-base">
                    <i class="fas fa-envelope text-blue-600 mr-2"></i>
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm sm:text-base"
                    placeholder="user@ciats.com" required>
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm sm:text-base">
                    <i class="fas fa-user-tag text-blue-600 mr-2"></i>
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm sm:text-base"
                    required>
                    <option value="">Pilih Role</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                </select>
                @error('role')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm sm:text-base">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm sm:text-base"
                    placeholder="••••••••" required>
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm sm:text-base">
                    <i class="fas fa-lock text-blue-600 mr-2"></i>
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-sm sm:text-base"
                    placeholder="••••••••" required>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 sm:p-4 rounded-lg">
                <p class="text-xs sm:text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    User baru akan dapat login menggunakan email dan password yang Anda tentukan.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit"
                    class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition text-sm sm:text-base">
                    <i class="fas fa-save mr-2"></i>Simpan User
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="flex-1 text-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition text-sm sm:text-base">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
