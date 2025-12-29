<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - CIATS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif']
                }
            }
        }
    }
    </script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .gradient-bg {
        background: #3b82f6;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
    }

    .input-focus:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-primary {
        background: #3b82f6;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
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

    <!-- Login Container -->
    <div class="w-full max-w-md">

        <!-- Logo & Title -->
        <div class="text-center mb-8 float-animation">
            <div class="inline-block bg-white p-4 rounded-2xl shadow-2xl mb-4">
                <i class="fas fa-box text-blue-600 text-5xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">CIATS</h1>
            <p class="text-blue-100">Corporate IT Asset Tracking System</p>
        </div>

        <!-- Login Card -->
        <div class="glass-effect rounded-2xl shadow-2xl p-8">

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            @endif

            <!-- Error Message -->
            @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    <p>{{ $errors->first() }}</p>
                </div>
            </div>
            @endif

            <h2 class="text-2xl font-bold text-gray-800 mb-6">Masuk ke Akun</h2>

            <!-- Login Form -->
            <form action="/login" method="POST">
                @csrf

                <!-- Email Input -->
                <div class="mb-5">
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-envelope text-blue-600 mr-2"></i>
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition"
                        placeholder="nama@perusahaan.com" required>
                    @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-lock text-blue-600 mr-2"></i>
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition pr-12"
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full text-white font-semibold py-3 rounded-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-white text-sm mt-6">
            © 2024 CIATS - Corporate IT Asset Tracking System
        </p>
    </div>

    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    // Auto hide alerts
    setTimeout(() => {
        const alerts = document.querySelectorAll('[class*="border-l-4"]');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    </script>
</body>

</html>