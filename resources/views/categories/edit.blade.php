@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('categories.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>

    <div class="bg-white rounded-xl shadow-xl p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Kategori</h1>

        <form action="{{ route('categories.update', $categoryId) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-gray-700 font-medium mb-2">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ $category['name'] }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ $category['description'] }}</textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">Update Kategori</button>
        </form>
    </div>
</div>
@endsection
