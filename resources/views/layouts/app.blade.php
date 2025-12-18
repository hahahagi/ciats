<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIATS - {{ $title ?? 'Cloud Inventory & Asset Tracking System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: bold;
            color: #0d6efd !important;
        }
        .sidebar {
            background-color: #fff;
            min-height: calc(100vh - 56px);
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .main-content {
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .badge-admin { background-color: #dc3545; }
        .badge-operator { background-color: #fd7e14; }
        .badge-employee { background-color: #20c997; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard">
                <i class="bi bi-cloud-fill"></i> CIATS
            </a>
            
            @if(session('user'))
            <div class="d-flex align-items-center">
                <span class="text-light me-3">
                    <i class="bi bi-person-circle"></i> 
                    {{ session('user')['name'] }} 
                    <span class="badge bg-primary ms-2">{{ session('user')['role'] }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
            @endif
        </div>
    </nav>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @if(session('user'))
            <div class="col-md-3 col-lg-2 p-0 sidebar">
                <div class="p-3">
                    <div class="list-group list-group-flush">
                        <a href="/dashboard" class="list-group-item list-group-item-action">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        
                        @if(session('user')['role'] === 'admin')
                        <a href="/admin/users" class="list-group-item list-group-item-action">
                            <i class="bi bi-people me-2"></i> Manajemen User
                        </a>
                        @endif
                        
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="bi bi-box me-2"></i> Inventaris
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="bi bi-tags me-2"></i> Assets
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="bi bi-bar-chart me-2"></i> Laporan
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <div class="{{ session('user') ? 'col-md-9 col-lg-10' : 'col-12' }} main-content">
                <!-- Notifikasi -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <!-- Konten Utama -->
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts setelah 5 detik
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>