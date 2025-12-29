<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    * {
        font-family: 'Inter', sans-serif;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .float-animation {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">

    <div class="text-center max-w-2xl">

        <!-- 404 Illustration -->
        <div class="float-animation mb-8">
            <div class="text-9xl font-bold text-white opacity-20 mb-4">404</div>
            <div
                class="w-48 h-48 mx-auto bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <i class="fas fa-exclamation-triangle text-white text-7xl"></i>
            </div>
        </div>

        <!-- Message -->
        <div class="bg-white bg-opacity-95 backdrop-blur-lg rounded-2xl shadow-2xl p-8 mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Oops! Halaman Tidak Ditemukan</h1>
            <p class="text-gray-600 text-lg mb-6">
                Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.
            </p>

            <!-- Search or Navigation -->
            <div class="space-y-4">
                <div class="relative">
                    <input type="text" placeholder="Cari halaman yang Anda butuhkan..."
                        class="w-full px-6 py-4 rounded-lg border-2 border-gray-300 focus:border-purple-500 focus:outline-none"
                        onkeypress="if(event.key==='Enter') alert('Search functionality coming soon!')">
                    <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Quick Links -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <a href="/dashboard"
                        class="px-6 py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold flex items-center justify-center space-x-2">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="/assets"
                        class="px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center justify-center space-x-2">
                        <i class="fas fa-boxes"></i>
                        <span>Daftar Aset</span>
                    </a>

                    <a href="/login"
                        class="px-6 py-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold flex items-center justify-center space-x-2">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="text-white text-sm">
            <p class="mb-2">Jika masalah berlanjut, silakan hubungi administrator sistem.</p>
            <p>Error Code: <span class="font-mono bg-white bg-opacity-20 px-2 py-1 rounded">404</span></p>
        </div>

        <!-- Back Button -->
        <button onclick="window.history.back()"
            class="mt-6 inline-flex items-center text-white hover:text-purple-200 transition">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke halaman sebelumnya
        </button>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>

</html>