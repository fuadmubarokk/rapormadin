<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Rapor Madrasah Diniyah</title>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #1f2937;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(135deg, #059669, #0891b2);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            color: white;
        }
        
        .logo i {
            font-size: 2.2rem;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .logo h1 {
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        
        /* Main Content */
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(135deg, #059669, #0891b2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h2 {
            font-size: 1.8rem;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: #374151;
            font-size: 0.95rem;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        
        .form-input.error {
            border-color: #ef4444;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        
        .input-icon .form-input {
            padding-right: 45px;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .error-message i {
            font-size: 0.8rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            accent-color: #059669;
        }
        
        .checkbox-group label {
            color: #6b7280;
            font-size: 0.95rem;
            cursor: pointer;
        }
        
        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .forgot-link {
            color: #059669;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .forgot-link:hover {
            color: #047857;
            text-decoration: underline;
        }
        
        .btn {
            width: 100%;
            padding: 14px 20px;
            background: linear-gradient(135deg, #059669, #0891b2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(5, 150, 105, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .register-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .register-link a {
            color: #059669;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover {
            color: #047857;
            text-decoration: underline;
        }
        
        /* Alert Styles */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        
        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }
        
        /* Footer */
        footer {
            background-color: #1f2937;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .footer p {
            color: #d1d5db;
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .login-container {
                margin: 20px;
                padding: 30px 20px;
            }
            
            .form-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo">
                    <i class="fas fa-mosque"></i>
                    <h1>Rapor Madrasah Diniyah Al Amin</h1>
                </a>
                <nav>
                    <a href="/" style="color: white; text-decoration: none; margin-left: 20px; padding: 8px 16px; border-radius: 6px; transition: background-color 0.3s;">Beranda</a>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="login-container">
            <div class="login-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Session Status Alert (contoh untuk error/success message) -->
            <!-- Uncomment untuk menampilkan pesan error/success -->
            <!--
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Email atau password yang Anda masukkan salah.</span>
            </div>
            -->

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-icon">
                        <input 
                            id="email" 
                            class="form-input" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            placeholder="nama@email.com"
                        >
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-icon">
                        <input 
                            id="password" 
                            class="form-input" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                        <i class="fas fa-lock"></i>
                    </div>
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="checkbox-group">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember_me">Ingat saya</label>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                    Masuk
                </button>
            </form>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Sistem Rapor Madrasah Digital. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>
</html>