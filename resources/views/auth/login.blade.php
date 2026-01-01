<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Rapor Madrasah Diniyah</title>
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #047857;
            --secondary: #0891b2;
            --accent: #7c3aed;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #e2e8f0;
            --error: #ef4444;
            --success: #10b981;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.1);
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark);
            line-height: 1.5;
            padding: 20px;
        }
        
        /* Main Login Container - Compact */
        .login-container {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            position: relative;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: var(--radius) var(--radius) 0 0;
        }
        
        /* Header Section - Compact */
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .logo-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            box-shadow: 0 6px 15px rgba(5, 150, 105, 0.2);
        }
        
        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.2;
            margin-bottom: 0.25rem;
        }
        
        .logo-text h1 span {
            display: block;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
        }
        
        .logo-text p {
            color: var(--gray);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Form Styles - Compact */
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .input-container {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 0.95rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 1px solid var(--light-gray);
            border-radius: var(--radius-sm);
            font-size: 0.95rem;
            transition: var(--transition);
            background-color: white;
            font-family: inherit;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            font-size: 0.95rem;
            padding: 0.25rem;
        }
        
        /* Form Options - Compact */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
            font-size: 0.9rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }
        
        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover {
            text-decoration: underline;
        }
        
        /* Submit Button - Compact */
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(5, 150, 105, 0.2);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Error Message */
        .error-message {
            color: var(--error);
            font-size: 0.8rem;
            margin-top: 0.25rem;
            padding-left: 0.5rem;
        }
        
        /* Alert */
        .alert {
            padding: 0.875rem 1rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-error {
            background-color: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error);
        }
        
        /* Responsive untuk mobile */
        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                max-width: 100%;
            }
            
            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }
        
        /* Very small screens */
        @media (max-height: 700px) {
            .login-container {
                padding: 1.75rem 1.5rem;
            }
            
            .logo {
                margin-bottom: 1.25rem;
            }
            
            .form-group {
                margin-bottom: 1rem;
            }
            
            .form-options {
                margin-bottom: 1.5rem;
            }
        }
        
        @media (max-height: 600px) {
            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
            
            .logo-text h1 {
                font-size: 1.3rem;
            }
            
            .logo-text h1 span {
                font-size: 1.1rem;
            }
            
            .logo-text p {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header -->
        <div class="login-header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-mosque"></i>
                </div>
                <div class="logo-text">
                    <h1>
                        Login Rapor Digital Madin
                        <span>RDM</span>
                    </h1>
                    <p>Sistem Pengelolaan Nilai</p>
                </div>
            </div>
        </div>

        <!-- Alert (optional) -->
        <!--
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>Email atau password salah</span>
        </div>
        -->

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            <!-- Email Field -->
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="input-container">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
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
                </div>
                @error('email')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="input-container">
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <input 
                        id="password" 
                        class="form-input" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    >
                    <button type="button" class="password-toggle" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Options -->
            <div class="form-options">
                <div class="checkbox-group">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label for="remember_me">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">
                    Lupa password?
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                <span>Masuk</span>
            </button>
        </form>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Auto focus on email field
        document.getElementById('email').focus();
    </script>
</body>
</html>