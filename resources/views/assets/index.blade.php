<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aset</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 4px; font-size: 14px; border: none; cursor: pointer; }
        .btn-add { background-color: #2563eb; }
        .btn-edit { background-color: #f59e0b; }
        .btn-delete { background-color: #dc2626; }
        .alert { padding: 10px; background-color: #d1fae5; color: #065f46; margin-bottom: 15px; }
    </style>
</head>
<body>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Daftar Inventaris Aset</h2>
        <a href="{{ url('/assets/create') }}" class="btn btn-add">+ Tambah Barang</a>
    </div>

    @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($assets)
                @foreach($assets as $key => $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['location'] }}</td>
                    <td>
                        {{-- Cek jika ada status, jika tidak default strip --}}
                        {{ $item['status'] ?? '-' }}
                    </td>
                    <td>
                        <!-- Tombol Edit (Nanti kita buat) -->
                        <a href="{{ url('/assets/'.$key.'/edit') }}" class="btn btn-edit">Edit</a>

                        <!-- Form Delete -->
                        <form action="{{ url('/assets/'.$key) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data aset.</td>
                </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
