<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Presence</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Background with image */
        .bg-with-image {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)),
                url('{{ asset('images/background.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
        }

        /* Login card styling with enhanced glass effect */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.15),
                0 15px 25px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
        }

        /* Input field styling */
        .input-field {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.15);
            background: rgba(255, 255, 255, 1);
        }

        /* Button styling */
        .btn-login {
            background: linear-gradient(45deg, #00bcd4, #0096c7);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #0096c7, #00bcd4);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 188, 212, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Loading animation */
        .btn-loading {
            opacity: 0.8;
            cursor: not-allowed;
        }

        /* Header animation */
        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form animation */
        .slide-up {
            animation: slideUp 0.6s ease-out 0.2s both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced floating elements */
        .floating-decoration {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            animation: float 6s ease-in-out infinite;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .decoration-1 {
            width: 120px;
            height: 120px;
            top: 8%;
            left: 8%;
            animation-delay: 0s;
        }

        .decoration-2 {
            width: 80px;
            height: 80px;
            top: 15%;
            right: 12%;
            animation-delay: 2s;
        }

        .decoration-3 {
            width: 100px;
            height: 100px;
            bottom: 15%;
            left: 15%;
            animation-delay: 4s;
        }

        .decoration-4 {
            width: 60px;
            height: 60px;
            bottom: 25%;
            right: 20%;
            animation-delay: 1s;
        }

        .decoration-5 {
            width: 40px;
            height: 40px;
            top: 50%;
            left: 5%;
            animation-delay: 3s;
        }

        .decoration-6 {
            width: 70px;
            height: 70px;
            top: 40%;
            right: 8%;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg) scale(1);
                opacity: 0.7;
            }
            33% {
                transform: translateY(-15px) rotate(120deg) scale(1.1);
                opacity: 0.9;
            }
            66% {
                transform: translateY(10px) rotate(240deg) scale(0.9);
                opacity: 0.8;
            }
        }

        /* Image loading fallback */
        .bg-image-error {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #00bcd4 100%);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .login-card {
                margin: 1rem;
                background: rgba(255, 255, 255, 0.98);
            }

            .floating-decoration {
                opacity: 0.4;
            }

            .bg-with-image {
                background-attachment: scroll;
                background-size: cover;
            }
        }

        @media (max-width: 480px) {
            .floating-decoration {
                display: none;
            }
        }

        /* Additional overlay for better text readability */
        .overlay-pattern {
            background-image:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(0, 188, 212, 0.2) 0%, transparent 50%);
        }
    </style>
</head>

<body class="bg-with-image bg-fallback flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Overlay pattern for better visual depth -->
    <div class="absolute inset-0 overlay-pattern pointer-events-none"></div>

    <!-- Enhanced Floating Decorations -->
    <div class="floating-decoration decoration-1"></div>
    <div class="floating-decoration decoration-2"></div>
    <div class="floating-decoration decoration-3"></div>
    <div class="floating-decoration decoration-4"></div>
    <div class="floating-decoration decoration-5"></div>
    <div class="floating-decoration decoration-6"></div>

    <!-- Main Login Card -->
    <div class="w-full max-w-md login-card rounded-2xl p-8 relative z-10">

        <!-- Header -->
        <div class="text-center mb-8 fade-in">
            <div class="mb-4">
                <i class="fas fa-user-circle text-6xl text-cyan-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">SMART PRESENCE</h1>
            <p class="text-lg text-cyan-600 font-medium">SIGN IN</p>
        </div>

        <!-- Error Messages -->
        <div class="slide-up">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg backdrop-blur-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Login Form -->
        <form method="POST" action="{{ route('unified.login') }}" class="space-y-6 slide-up">
            @csrf

            <!-- Username Field -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-cyan-600"></i>Email*
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    value="{{ old('email') }}"
                    class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    placeholder="Enter your Email"
                >
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-2 text-cyan-600"></i>Password*
                </label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="input-field w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent pr-12"
                        placeholder="Enter your password"
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center transition-colors duration-200"
                        onclick="togglePassword()"
                    >
                        <i id="eye-icon" class="fas fa-eye text-gray-400 hover:text-cyan-600"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    id="login-btn"
                    class="btn-login w-full py-3 px-4 text-white font-medium rounded-lg shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500"
                >
                    <span id="btn-text">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk
                    </span>
                </button>
            </div>
        </form>

        <!-- Footer Info -->
        <div class="mt-8 text-center text-sm text-gray-600 slide-up">
            <p>
                <i class="fas fa-info-circle mr-1"></i>
                Sistem akan otomatis mengarahkan Anda ke dashboard sesuai dengan role akun.
            </p>
        </div>

        <!-- Version Info -->
        <div class="mt-4 text-center text-xs text-gray-400">
            <p>Smart Presence v1.0</p>
        </div>
    </div>

    <script>
        // Check if background image loads properly
        function checkBackgroundImage() {
            const body = document.body;
            const img = new Image();

            img.onload = function() {
                console.log('Background image loaded successfully');
                body.classList.add('bg-image-loaded');
            };

            img.onerror = function() {
                console.warn('Background image failed to load, using fallback');
                body.classList.remove('bg-with-image');
                body.classList.add('bg-image-error');
            };

            img.src = '{{ asset('images/background.png') }}';
        }

        // Password toggle function
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
                eyeIcon.classList.add('text-cyan-600');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                eyeIcon.classList.remove('text-cyan-600');
            }
        }

        // Form submission handling - only handle loading state, don't prevent submission
        const loginForm = document.querySelector('form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const button = document.getElementById('login-btn');
                const btnText = document.getElementById('btn-text');

                // Add loading state without preventing form submission
                if (button && btnText) {
                    button.classList.add('btn-loading');
                    button.disabled = true;
                    btnText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Masuk...';
                }

                // Let the form submit naturally - don't prevent default
            });
        }

        // Initialize on page load
        window.addEventListener('load', function() {
            // Check background image
            checkBackgroundImage();

            // Focus on email field
            document.getElementById('email').focus();

            // Add input animations
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('transform', 'scale-105');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('transform', 'scale-105');
                });
            });

            // Form inputs will naturally submit on Enter - no custom handling needed

            // Add parallax effect for floating decorations
            window.addEventListener('mousemove', function(e) {
                const decorations = document.querySelectorAll('.floating-decoration');
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;

                decorations.forEach((decoration, index) => {
                    const speed = (index + 1) * 0.5;
                    const x = (mouseX - 0.5) * speed;
                    const y = (mouseY - 0.5) * speed;

                    decoration.style.transform += ` translate(${x}px, ${y}px)`;
                });
            });
        });

        // Add smooth scrolling for mobile
        if (window.innerHeight < 600) {
            document.body.style.minHeight = '100vh';
            document.body.style.display = 'flex';
            document.body.style.alignItems = 'center';
        }

        // Error message auto-hide
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-red-100, .bg-green-100');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add resize handler for responsive background
        window.addEventListener('resize', function() {
            if (window.innerWidth < 640) {
                document.body.style.backgroundAttachment = 'scroll';
            } else {
                document.body.style.backgroundAttachment = 'fixed';
            }
        });
    </script>

</body>
</html>
