<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0">✏️ Edit Data Aset</h4>
        </div>
        <div class="card-body">

            <!-- Form mengarah ke route update dengan ID -->
            <form action="{{ url('/assets/'.$id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Ubah method POST jadi PUT agar dikenali Laravel -->

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Barang</label>
                    <input type="text" name="name" class="form-control" value="{{ $asset['name'] ?? '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Kategori</label>
                    <select name="category" class="form-select">
                        <option value="Elektronik" {{ ($asset['category'] ?? '') == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                        <option value="Furniture" {{ ($asset['category'] ?? '') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Kendaraan" {{ ($asset['category'] ?? '') == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                        <option value="Lainnya" {{ ($asset['category'] ?? '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" value="{{ $asset['serial_number'] ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="available" {{ ($asset['status'] ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="in_use" {{ ($asset['status'] ?? '') == 'in_use' ? 'selected' : '' }}>In Use</option>
                        <option value="broken" {{ ($asset['status'] ?? '') == 'broken' ? 'selected' : '' }}>Broken</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Lokasi Penyimpanan</label>
                    <input type="text" name="location" class="form-control" value="{{ $asset['location'] ?? '' }}" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ url('/assets') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
