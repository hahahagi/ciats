@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Lokasi</h1>
            <p class="text-gray-600">Kelola lokasi penyimpanan aset</p>
        </div>

        <a href="{{ route('locations.create') }}"
            class="mt-4 md:mt-0 inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Tambah Lokasi
        </a>
    </div>

    <!-- Locations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($locations as $location)
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover relative group">
            <div class="flex items-start justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                </div>

                <div class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('locations.edit', $location['id']) }}"
                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('locations.destroy', $location['id']) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $location['name'] }}</h3>
            <p class="text-gray-600 text-sm mb-4">{{ $location['description'] ?: 'Tidak ada deskripsi' }}</p>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between text-sm text-gray-500">
                <span><i class="fas fa-calendar-alt mr-1"></i> {{ date('d M Y', $location['created_at'] ?? time()) }}</span>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-white rounded-xl shadow-sm">
            <div class="bg-gray-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-map-marker-slash text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900">Belum ada lokasi</h3>
            <p class="text-gray-500 mt-1">Mulai dengan menambahkan lokasi baru.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
