<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapor Madrasah Diniyah</title>
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
            background-color: #f8fafc;
            color: #1f2937;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background: linear-gradient(135deg, #059669, #0891b2);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
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
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.05);
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
        
        nav {
            display: flex;
            align-items: center;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transition: left 0.3s ease;
        }
        
        nav a:hover::before {
            left: 0;
        }
        
        /* Button Styles */
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #059669;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        
        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .btn:hover::after {
            width: 300px;
            height: 300px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background-color: #0891b2;
        }
        
        .btn-secondary:hover {
            background-color: #0e7490;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23059669' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 24px;
            color: #1f2937;
            font-weight: 700;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            color: #4b5563;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        /* Features Section */
        .features {
            padding: 100px 0;
            background-color: #ffffff;
        }
        
        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #1f2937;
            font-weight: 700;
        }
        
        .features-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #6b7280;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
        }
        
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #e2e8f0;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #059669, #0891b2);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            font-size: 2rem;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.3);
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: #1f2937;
            font-weight: 600;
        }
        
        .feature-card p {
            color: #6b7280;
            line-height: 1.7;
        }
        
        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, #059669, #0891b2);
            padding: 100px 0;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .cta-content {
            position: relative;
            z-index: 1;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        .cta p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .cta .btn {
            background-color: white;
            color: #059669;
            font-weight: 600;
        }
        
        .cta .btn:hover {
            background-color: #f3f4f6;
        }
        
        .cta .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .cta .btn-secondary:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        /* Footer - Simplified */
        footer {
            background-color: #1f2937;
            color: white;
            padding: 30px 0;
            text-align: center;
        }
        
        .footer-bottom {
            color: #d1d5db;
            font-size: 0.9rem;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            nav a {
                margin: 5px 10px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .features-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 30px;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 70px 0;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .features {
                padding: 70px 0;
            }
            
            .features h2 {
                font-size: 2rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .cta {
                padding: 70px 0;
            }
            
            .cta h2 {
                font-size: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-mosque"></i>
                    <h1>Rapor Madrasah Diniyah</h1>
                </div>
                <nav>
                    <a href="#beranda">Beranda</a>
                    <a href="#fitur">Fitur</a>
                    <a href="{{ route('login') }}">Masuk</a>
                </nav>
            </div>
        </div>
    </header>

    <section class="hero" id="beranda">
        <div class="container hero-content">
            <h1>Rapor Madrasah Diniyah Al Amin Cintamulya</h1>
            <p>Sistem rapor digital yang membantu madrasah mengelola nilai akademik santri dengan mudah, efisien, dan terintegrasi</p>
            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Masuk
                </a>
            </div>
        </div>
    </section>

    <section class="features" id="fitur">
        <div class="container">
            <h2>Fitur Unggulan</h2>
            <p class="features-subtitle">Fitur canggih untuk membantu proses pembelajaran di madrasah</p>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Analisis Nilai</h3>
                    <p>Analisis mendalam tentang perkembangan akademik santri dengan grafik dan statistik yang informatif</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Manajemen Santri</h3>
                    <p>Kelola data santri, kelas, dan informasi akademik dengan mudah dan efisien</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Rapor Digital</h3>
                    <p>Buat rapor digital yang dapat diakses kapan saja dan di mana saja oleh orang tua/wali santri</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Akses Mobile</h3>
                    <p>Akses sistem dari berbagai perangkat dengan tampilan yang responsif dan user-friendly</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Keamanan Data</h3>
                    <p>Perlindungan data santri dengan enkripsi tingkat enterprise dan sistem backup otomatis</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Komunikasi</h3>
                    <p>Fasilitas komunikasi antara guru, santri, dan orang tua untuk mendukung pembelajaran</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Sistem Rapor Madrasah Digital. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>
</body>
</html>