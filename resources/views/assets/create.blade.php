<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Input Aset ke Firebase</title>
    <!-- CSS Sederhana biar gak sakit mata -->
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #2563eb; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        button:hover { background-color: #1e40af; }
        .alert { padding: 10px; background-color: #d1fae5; color: #065f46; margin-bottom: 15px; border-radius: 5px;}
    </style>
</head>
<body>

    <h2>Uji Coba Input Barang</h2>
    <p>Data ini akan langsung masuk ke Firebase.</p>

    <!-- Menampilkan pesan sukses jika ada -->
    @if(session('success'))
        <div class="alert">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ url('/assets') }}" method="POST">
        @csrf <!-- Token keamanan wajib di Laravel -->

        <div class="form-group">
            <label>Nama Barang / Aset</label>
            <input type="text" name="name" placeholder="Contoh: Laptop Dell Latitude" required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select name="category">
                <option value="Elektronik">Elektronik</option>
                <option value="Furniture">Furniture</option>
                <option value="Kendaraan">Kendaraan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="form-group">
            <label>Serial Number (Opsional)</label>
            <input type="text" name="serial_number" placeholder="SN-12345">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="available">Available</option>
                <option value="in_use">In Use</option>
                <option value="broken">Broken</option>
            </select>
        </div>

        <div class="form-group">
            <label>Lokasi Penyimpanan</label>
            <input type="text" name="location" placeholder="Contoh: Gudang A, Rak 2" required>
        </div>

        <button type="submit">Simpan ke Firebase</button>
    </form>

    <br>
    <hr>
    <a href="{{ url('/assets') }}">Lihat Data JSON (Cek Hasil)</a>

</body>
</html>
