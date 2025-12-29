{{-- INDEX: users/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Manajemen User</h1>
            <p class="text-sm sm:text-base text-gray-600">Kelola akun pengguna sistem</p>
        </div>

        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center justify-center px-4 sm:px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition w-full sm:w-auto">
            <i class="fas fa-user-plus mr-2"></i>
            Tambah User
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        @php
        $stats = [
        'total' => count($users),
        'admin' => collect($users)->where('role', 'admin')->count(),
        'operator' => collect($users)->where('role', 'operator')->count(),
        'employee' => collect($users)->where('role', 'employee')->count(),
        ];
        @endphp

        <div class="bg-white rounded-lg shadow p-3 sm:p-4">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Total Users</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Admin</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $stats['admin'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Operator</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $stats['operator'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-3 sm:p-4">
            <p class="text-xs sm:text-sm text-gray-500 mb-1">Employee</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $stats['employee'] }}</p>
        </div>
    </div>

    <!-- Desktop Table View (hidden on mobile) -->
    <div class="hidden md:block bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="gradient-bg text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Name</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Role</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Created</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $usr)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($usr['name'], 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $usr['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $usr['email'] }}</td>
                        <td class="px-6 py-4">
                            @php
                            $roleConfig = [
                            'admin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-crown'],
                            'operator' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-cog'],
                            'employee' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-user'],
                            ];
                            $role = $roleConfig[$usr['role']] ?? $roleConfig['employee'];
                            @endphp
                            <span
                                class="inline-flex items-center px-3 py-1 {{ $role['bg'] }} {{ $role['text'] }} rounded-full text-xs font-semibold">
                                <i class="fas {{ $role['icon'] }} mr-1"></i>
                                {{ ucfirst($usr['role']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $usr['created_at'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.users.edit', $usr['id']) }}"
                                    class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $usr['id']) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus user ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-gray-300 text-4xl mb-2"></i>
                            <p>Belum ada user</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card View (visible on mobile only) -->
    <div class="md:hidden space-y-4">
        @forelse($users as $usr)
        @php
        $roleConfig = [
        'admin' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-crown'],
        'operator' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-cog'],
        'employee' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-user'],
        ];
        $role = $roleConfig[$usr['role']] ?? $roleConfig['employee'];
        @endphp

        <div class="bg-white rounded-xl shadow-lg p-4">
            <!-- User Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-lg">
                        {{ strtoupper(substr($usr['name'], 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $usr['name'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $usr['email'] }}</p>
                    </div>
                </div>

                <span
                    class="inline-flex items-center px-3 py-1 {{ $role['bg'] }} {{ $role['text'] }} rounded-full text-xs font-semibold">
                    <i class="fas {{ $role['icon'] }} mr-1"></i>
                    {{ ucfirst($usr['role']) }}
                </span>
            </div>

            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>
                    <span>Dibuat: {{ $usr['created_at'] }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $usr['id']) }}"
                    class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>

                <form action="{{ route('admin.users.destroy', $usr['id']) }}" method="POST" class="flex-1"
                    data-confirm="Yakin hapus user {{ $usr['name'] }}?"
                    onsubmit="if(!confirm(this.dataset.confirm)) return false;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-16 bg-white rounded-xl shadow-lg">
            <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada User</h3>
            <p class="text-gray-500 mb-6">Mulai tambahkan user pertama Anda</p>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah User
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection