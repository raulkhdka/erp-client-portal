<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-wrapper {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 80%;
            width: 100%;
            display: flex;
            min-height: 600px;
        }

        .login-form-section {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-visual-section {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .visual-elements {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
        }

        .shape-1 {
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            top: 10%;
            left: 10%;
            animation: float 6s ease-in-out infinite;
        }

        .shape-2 {
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.15);
            top: 60%;
            right: 15%;
            animation: float 8s ease-in-out infinite reverse;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            bottom: 20%;
            left: 25%;
            animation: float 7s ease-in-out infinite;
        }

        .geometric-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .triangle {
            position: absolute;
            width: 0;
            height: 0;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 60px solid rgba(255, 255, 255, 0.1);
            top: 30%;
            right: 30%;
            animation: rotate 20s linear infinite;
        }

        .square {
            position: absolute;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.12);
            transform: rotate(45deg);
            top: 70%;
            right: 60%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: rotate(45deg) scale(1); }
            50% { opacity: 1; transform: rotate(45deg) scale(1.1); }
        }

        .brand-logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .brand-logo i {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2rem;
            margin-right: 12px;
        }

        .brand-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
        }

        .welcome-text {
            margin-bottom: 8px;
        }

        .welcome-text h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a202c;
            margin: 0;
        }

        .welcome-subtitle {
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #ffffff;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 4px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 24px 0;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            margin-right: 8px;
            accent-color: #667eea;
        }

        .form-check-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .divider {
            margin: 32px 0;
            text-align: center;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            background: white;
            padding: 0 16px;
            color: #64748b;
            font-size: 0.875rem;
        }

        .demo-section {
            text-align: center;
        }

        .demo-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .demo-btn {
            padding: 8px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            color: #64748b;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .demo-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-1px);
        }

        .alert {
            background: #fef3cd;
            border: 1px solid #fecaca;
            color: #92400e;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            position: relative;
        }

        .alert .btn-close {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            opacity: 0.7;
        }

        @media (max-width: 1024px) {
            .login-wrapper {
                max-width: 500px;
            }

            .login-visual-section {
                display: none;
            }

            .login-form-section {
                flex: none;
                width: 100%;
                padding: 50px 40px;
            }
        }

        @media (max-width: 768px) {
            .login-wrapper {
                max-width: 400px;
                margin: 20px;
            }

            .login-form-section {
                padding: 40px 30px;
            }

            .demo-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .demo-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Left side - Login Form -->
            <div class="login-form-section">
                <div class="brand-logo">
                    <i class="fas fa-cube"></i>
                    <span class="brand-text">ERP System</span>
                </div>

                <div class="welcome-text">
                    <h2>Welcome back!</h2>
                </div>
                <p class="welcome-subtitle">Please enter your login credentials to continue</p>

                <!-- Session Expired Alert -->
                @if(session('message'))
                <div class="alert" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert">×</button>
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="remember-forgot">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Log in
                    </button>
                </form>

                <div class="divider">
                    <span>Demo Credentials</span>
                </div>

                <div class="demo-section">
                    <div class="demo-buttons">
                        <button type="button" class="demo-btn" onclick="fillCredentials('admin@erp.com', 'password')">
                            <i class="fas fa-user-shield me-1"></i>Admin
                        </button>
                        <button type="button" class="demo-btn" onclick="fillCredentials('employee@erp.com', 'password')">
                            <i class="fas fa-user me-1"></i>Employee
                        </button>
                        <button type="button" class="demo-btn" onclick="fillCredentials('client@erp.com', 'password')">
                            <i class="fas fa-user-tie me-1"></i>Client
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right side - Visual Illustration -->
            <div class="login-visual-section">
                <div class="visual-elements">
                    <!-- Animated shapes -->
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>

                    <!-- Geometric shapes -->
                    <div class="geometric-shapes">
                        <div class="triangle"></div>
                        <div class="square"></div>
                    </div>

                    <!-- Additional decorative elements -->
                    <div style="position: absolute; top: 15%; left: 20%; width: 60px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 12px; transform: rotate(15deg); animation: pulse 3s ease-in-out infinite;"></div>
                    <div style="position: absolute; bottom: 25%; right: 20%; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%; animation: float 5s ease-in-out infinite;"></div>
                    <div style="position: absolute; top: 50%; left: 15%; width: 4px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 2px; animation: pulse 2s ease-in-out infinite;"></div>
                    <div style="position: absolute; top: 35%; right: 25%; width: 30px; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; animation: pulse 2.5s ease-in-out infinite;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh CSRF token periodically to prevent expiration
        setInterval(function() {
            fetch('/csrf-token')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                    document.querySelector('input[name="_token"]').value = data.csrf_token;
                })
                .catch(console.error);
        }, 300000); // Refresh every 5 minutes

        // Fill demo credentials
        function fillCredentials(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
