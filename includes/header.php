<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diamond Bright Tours - Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============= VARIABLES CSS ============= */
        :root {
            --color-primary: #0a1a2f;
            --color-secondary: #1a3a5f;
            --color-accent: #d4af37;
            --color-light: #f8f7f3;
            --color-dark: #0c1625;
            --color-light-accent: #f5e7c1;
            --color-transparent: rgba(10, 26, 47, 0.85);
            
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --box-shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        /* ================= RESET Y ESTILOS GENERALES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', 'Segoe UI', Tahoma, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 90px;
            color: var(--color-light);
            line-height: 1.6;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-family: 'Cormorant Garamond', Georgia, serif;
            color: var(--color-accent);
        }

        p {
            max-width: 800px;
            margin: 0 auto 30px;
            color: var(--color-light-accent);
        }

        .demo-placeholder {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 50px;
        }

        .demo-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            border-radius: var(--border-radius);
            padding: 30px;
            transition: var(--transition);
        }

        .demo-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(212, 175, 55, 0.3);
        }

        .demo-item h3 {
            margin-bottom: 15px;
            color: var(--color-accent);
            font-family: 'Cormorant Garamond', Georgia, serif;
        }

        /* ================= HEADER MEJORADO ================= */
        .main-header {
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--color-transparent);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            transition: var(--transition);
        }

        .main-header.scrolled {
            padding: 10px 5%;
            box-shadow: var(--box-shadow);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--color-light);
            text-decoration: none;
            font-family: 'Cormorant Garamond', Georgia, serif;
            letter-spacing: 0.5px;
        }

        .logo:hover {
            transform: scale(1.02);
        }

        .logo i {
            color: var(--color-accent);
            font-size: 2.2rem;
            transition: var(--transition);
        }

        .logo span {
            color: var(--color-accent);
            font-weight: 700;
        }

        .nav-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .main-nav {
            position: relative;
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 10px;
        }

        .main-nav a {
            color: var(--color-light);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-nav a i {
            font-size: 1.1rem;
            width: 20px;
        }

        .main-nav a:hover {
            color: var(--color-accent);
            background: rgba(212, 175, 55, 0.1);
        }

        .main-nav a.active {
            background: rgba(212, 175, 55, 0.2);
            color: var(--color-accent);
        }

        .main-nav a.active:after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--color-accent);
        }

        .reserve-btn {
            background: var(--color-accent);
            color: var(--color-dark);
            border: none;
            padding: 12px 25px;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            box-shadow: 0 4px 10px rgba(212, 175, 55, 0.3);
        }

        .reserve-btn:hover {
            background: var(--color-light);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(212, 175, 55, 0.4);
        }

        .reserve-btn i {
            font-size: 1.1rem;
        }

        /* Menú móvil */
        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--color-light);
            font-size: 1.8rem;
            cursor: pointer;
            z-index: 1001;
        }

        /* Menú desplegable móvil */
        .mobile-nav {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            max-width: 320px;
            height: 100vh;
            background: var(--color-dark);
            z-index: 1000;
            padding: 90px 30px 30px;
            transition: var(--transition);
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        .mobile-nav.active {
            right: 0;
        }

        .mobile-nav ul {
            list-style: none;
        }

        .mobile-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 0;
            color: var(--color-light);
            text-decoration: none;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }

        .mobile-nav a i {
            width: 25px;
            font-size: 1.2rem;
            color: var(--color-accent);
        }

        .mobile-nav a:hover {
            color: var(--color-accent);
            padding-left: 10px;
        }

        .mobile-nav .reserve-btn {
            margin-top: 30px;
            width: 100%;
            justify-content: center;
            padding: 15px;
        }

        .close-menu-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            color: var(--color-light);
            font-size: 1.8rem;
            cursor: pointer;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .main-nav ul {
                gap: 5px;
            }
            
            .main-nav a {
                padding: 10px 12px;
                font-size: 1rem;
            }
        }

        @media (max-width: 992px) {
            .main-nav {
                display: none;
            }
            
            .reserve-btn.desktop {
                display: none;
            }
            
            .mobile-menu-btn {
                display: block;
            }
        }

        @media (max-width: 480px) {
            .logo {
                font-size: 1.6rem;
            }
            
            .logo i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Mejorado -->
    <header class="main-header">
        <div class="logo-container">
            <a href="index.php" class="logo" aria-label="Diamond Bright - Inicio">
                <i class="fas fa-gem"></i>
                Diamond <span>Bright</span>
            </a>
        </div>
        
        <div class="nav-container">
            <nav class="main-nav" aria-label="Navegación principal">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="tours.php"><i class="fas fa-ship"></i> Tours</a></li>
                    <li><a href="club.php"><i class="fas fa-umbrella-beach"></i> Club de Playa</a></li>
                    <li><a href="islamujeres.php"><i class="fas fa-island-tropical"></i> Isla Mujeres</a></li>
                    <li><a href="Parqueacuatico.php"><i class="fas fa-fish"></i> Museo Acuático</a></li>
                    <li><a href="snorkel.php"><i class="fas fa-water"></i> Snorkel</a></li>
                </ul>
            </nav>
            
            <a href="reserva.php" class="reserve-btn desktop">
                <i class="fas fa-calendar-check"></i> Reserva
            </a>
            
            <button class="mobile-menu-btn" aria-label="Menú móvil">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>
    
    <!-- Menú Móvil -->
    <div class="overlay"></div>
    <div class="mobile-nav">
        <button class="close-menu-btn" aria-label="Cerrar menú">
            <i class="fas fa-times"></i>
        </button>
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="tours.php"><i class="fas fa-ship"></i> Tours</a></li>
            <li><a href="club-playa.php"><i class="fas fa-umbrella-beach"></i> Club de Playa</a></li>
            <li><a href="isla-mujeres.php"><i class="fas fa-island-tropical"></i> Isla Mujeres</a></li>
            <li><a href="museo-acuatico.php"><i class="fas fa-fish"></i> Museo Acuático</a></li>
            <li><a href="snorkel.php"><i class="fas fa-water"></i> Snorkel</a></li>
            <li><a href="reserva.php" class="reserve-btn"><i class="fas fa-calendar-check"></i> Reservar Ahora</a></li>
        </ul>
    </div>
    
    <!-- Contenido de demostración -->


    <script>
        // Efecto de scroll para el header
        window.addEventListener('DOMContentLoaded', () => {
            const header = document.querySelector('.main-header');
            
            if (header) {
                window.addEventListener('scroll', () => {
                    header.classList.toggle('scrolled', window.scrollY > 30);
                });
            }
            
            // Menú móvil
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const closeMenuBtn = document.querySelector('.close-menu-btn');
            const mobileNav = document.querySelector('.mobile-nav');
            const overlay = document.querySelector('.overlay');
            
            function toggleMobileMenu() {
                mobileNav.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
            }
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleMobileMenu);
            }
            
            if (closeMenuBtn) {
                closeMenuBtn.addEventListener('click', toggleMobileMenu);
            }
            
            if (overlay) {
                overlay.addEventListener('click', toggleMobileMenu);
            }
            
            // Indicador de página activa
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.main-nav a, .mobile-nav a');
            
            navLinks.forEach(link => {
                const linkPage = link.getAttribute('href');
                if (linkPage === currentPage) {
                    link.classList.add('active');
                }
            });
            
            // Cerrar menú al hacer clic en un enlace (móvil)
            const mobileLinks = document.querySelectorAll('.mobile-nav a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (mobileNav.classList.contains('active')) {
                        toggleMobileMenu();
                    }
                });
            });
        });
    </script>
</body>
</html>