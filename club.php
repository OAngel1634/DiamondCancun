<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club de Playa Premium | DiamondPrueba</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Paleta de colores playa premium */
            --color-sand: #F5E7C1;
            --color-ocean: #0a1a2f;
            --color-turquoise: #48cae4;
            --color-gold: #D4AF37;
            --color-light: #f8f7f3;
            --color-dark: #0c1625;
            --color-accent: #d4af37;
            
            /* Transiciones */
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            
            /* Tipografía premium */
            --font-heading: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Montserrat', 'Segoe UI', Tahoma, sans-serif;
            
            /* Dimensiones */
            --border-radius: 10px;
            --box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            --box-shadow-hover: 0 20px 50px rgba(0, 0, 0, 0.15);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            background: linear-gradient(to bottom, var(--color-ocean), #143d6b);
            color: var(--color-light);
            line-height: 1.7;
            overflow-x: hidden;
            min-height: 100vh;
            position: relative;
        }
        
        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 2;
        }
        
        header {
            background: rgba(10, 26, 47, 0.95);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.8rem;
            font-weight: 600;
            font-family: var(--font-heading);
            letter-spacing: 1px;
            color: var(--color-light);
        }
        
        .logo i {
            color: var(--color-gold);
            font-size: 2.2rem;
        }
        
        .logo span {
            color: var(--color-gold);
            font-weight: 700;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            gap: 25px;
        }
        
        nav a {
            color: var(--color-light);
            text-decoration: none;
            font-weight: 400;
            padding: 10px 0;
            position: relative;
            overflow: hidden;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        
        nav a:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--color-gold);
            transition: var(--transition);
        }
        
        nav a:hover:after {
            width: 100%;
        }
        
        nav a:hover {
            color: var(--color-gold);
        }
        
        .hero {
            position: relative;
            height: 90vh;
            overflow: hidden;
            margin-bottom: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(10, 26, 47, 0.7), rgba(10, 26, 47, 0.7)), 
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
        }
        
        .hero-content {
            position: relative;
            text-align: center;
            padding: 40px;
            max-width: 900px;
            z-index: 2;
        }
        
        .hero-content h1 {
            font-size: 4.5rem;
            margin-bottom: 25px;
            font-family: var(--font-heading);
            font-weight: 600;
            letter-spacing: 1px;
            color: var(--color-light);
            line-height: 1.1;
        }
        
        .hero-content h1 span {
            color: var(--color-gold);
            display: block;
            font-weight: 400;
            font-size: 3.5rem;
            margin-top: 10px;
        }
        
        .hero-content p {
            font-size: 1.4rem;
            max-width: 700px;
            margin: 0 auto 40px;
            font-weight: 300;
            color: var(--color-sand);
        }
        
        .cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: var(--color-light);
            padding: 16px 45px;
            border-radius: 0;
            font-weight: 400;
            font-size: 1.1rem;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--color-gold);
            position: relative;
            overflow: hidden;
            z-index: 1;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: var(--font-heading);
        }
        
        .cta-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color-gold);
            transform: translateX(-100%);
            transition: var(--transition);
            z-index: -1;
        }
        
        .cta-button:hover {
            color: var(--color-dark);
        }
        
        .cta-button:hover:before {
            transform: translateX(0);
        }
        
        .cta-button i {
            margin-left: 12px;
            font-size: 1.1rem;
            transition: var(--transition);
        }
        
        .cta-button:hover i {
            transform: translateX(5px);
        }
        
        .premium-badge {
            display: inline-block;
            background: rgba(212, 175, 55, 0.1);
            color: var(--color-gold);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            letter-spacing: 1.5px;
            border: 1px solid var(--color-gold);
            margin-bottom: 30px;
            font-family: var(--font-heading);
        }
        
        /* Sección de características */
        .features-section {
            padding: 80px 0;
            background: linear-gradient(to bottom, var(--color-ocean), #0d2a4e);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
            font-size: 2.8rem;
            color: var(--color-light);
            position: relative;
            font-family: var(--font-heading);
            font-weight: 500;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 2px;
            background: var(--color-gold);
            margin: 20px auto;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }
        
        .feature-card:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--color-gold);
            transform: translateX(-100%);
            transition: var(--transition);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .feature-card:hover:after {
            transform: translateX(0);
        }
        
        .feature-card i {
            font-size: 3.5rem;
            color: var(--color-gold);
            margin-bottom: 25px;
            transition: var(--transition);
        }
        
        .feature-card h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            font-family: var(--font-heading);
            letter-spacing: 0.5px;
            font-weight: 500;
            color: var(--color-light);
        }
        
        .feature-card p {
            font-weight: 300;
            font-size: 1.05rem;
            color: #d1d0cd;
        }
        
        /* Sección de buffet */
        .buffet-section {
            padding: 100px 0;
            background: linear-gradient(rgba(10, 26, 47, 0.9), rgba(10, 26, 47, 0.9)), 
                        url('https://images.unsplash.com/photo-1564759077034-936b00d65837?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-attachment: fixed;
            position: relative;
        }
        
        .buffet-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 50px;
            align-items: center;
        }
        
        .buffet-text {
            flex: 1;
            min-width: 300px;
        }
        
        .buffet-image {
            flex: 1;
            min-width: 300px;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            height: 400px;
        }
        
        .buffet-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .buffet-image:hover img {
            transform: scale(1.05);
        }
        
        .menu-list {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .menu-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-5px);
        }
        
        .menu-item i {
            color: var(--color-gold);
            font-size: 1.5rem;
            margin-top: 5px;
        }
        
        /* Sección de hamacas */
        .hammocks-section {
            padding: 100px 0;
            background: var(--color-sand);
            color: var(--color-dark);
        }
        
        .hammocks-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .hammocks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .hammock-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .hammock-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .hammock-image {
            height: 250px;
            overflow: hidden;
        }
        
        .hammock-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .hammock-card:hover .hammock-image img {
            transform: scale(1.1);
        }
        
        .hammock-info {
            padding: 25px;
        }
        
        .hammock-info h3 {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: var(--color-ocean);
        }
        
        /* Sección de alberca */
        .pool-section {
            padding: 100px 0;
            background: linear-gradient(rgba(10, 26, 47, 0.9), rgba(10, 26, 47, 0.9)), 
                        url('https://images.unsplash.com/photo-1560279969-cc411a7987f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-attachment: fixed;
            position: relative;
            text-align: center;
        }
        
        .pool-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 50px auto 0;
            padding: 0 20px;
        }
        
        .pool-feature {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: var(--border-radius);
            border: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .pool-feature i {
            font-size: 2.5rem;
            color: var(--color-gold);
            margin-bottom: 20px;
        }
        
        /* Footer */
        footer {
            background: rgba(10, 26, 47, 0.95);
            padding: 80px 20px 40px;
            text-align: center;
            position: relative;
            border-top: 1px solid rgba(212, 175, 55, 0.2);
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 50px;
            text-align: left;
            position: relative;
            z-index: 2;
        }
        
        .footer-section h3 {
            color: var(--color-gold);
            margin-bottom: 25px;
            font-size: 1.5rem;
            font-family: var(--font-heading);
            position: relative;
            padding-bottom: 10px;
            font-weight: 500;
        }
        
        .footer-section h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 1px;
            background: var(--color-gold);
        }
        
        .footer-section p, .footer-section a {
            color: #c4c3bf;
            margin-bottom: 15px;
            display: block;
            text-decoration: none;
            transition: var(--transition);
            font-weight: 300;
        }
        
        .footer-section a:hover {
            color: var(--color-gold);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: transparent;
            color: var(--color-light);
            transition: var(--transition);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }
        
        .social-links a:hover {
            background: var(--color-gold);
            color: var(--color-dark);
            transform: translateY(-5px);
        }
        
        .copyright {
            margin-top: 60px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            color: #8a8986;
            font-size: 0.9rem;
            position: relative;
            z-index: 2;
            letter-spacing: 0.5px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero-content h1 {
                font-size: 2.8rem;
            }
            
            .hero-content h1 span {
                font-size: 2.5rem;
            }
            
            .buffet-content {
                flex-direction: column;
            }
        }
        
        @media (max-width: 480px) {
            .hero {
                height: 85vh;
            }
            
            .hero-content h1 {
                font-size: 2.2rem;
            }
            
            .cta-button {
                padding: 14px 30px;
            }
        }
        
        /* Decoraciones */
        .palm-icon {
            position: absolute;
            font-size: 10rem;
            color: rgba(212, 175, 55, 0.1);
            z-index: 1;
        }
        
        .palm-1 {
            top: 20%;
            left: 5%;
            transform: rotate(-20deg);
        }
        
        .palm-2 {
            bottom: 20%;
            right: 5%;
            transform: rotate(20deg);
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header>
      <?php include('../includes/header.php'); ?>
    </header>
        
        <main>
            <!-- Hero Section -->
            <section class="hero">
                <div class="hero-content">
                    <div class="premium-badge">EXPERIENCIA EXCLUSIVA</div>
                    <h1>CLUB DE PLAYA <span>Premium</span></h1>
                    <p>Descubre el lujo frente al mar con nuestro exclusivo club de playa. Relájate en nuestras hamacas, disfruta de nuestra alberca infinita y saborea nuestro buffet gourmet.</p>
                    <a href="Reserva.php" class="cta-button">Reservar ahora <i class="fas fa-arrow-right"></i></a>
                </div>
            </section>
            
            <!-- Sección de características -->
            <section class="features-section">
                <h2 class="section-title">Nuestras Exclusividades</h2>
                
                <div class="features">
                    <div class="feature-card">
                        <i class="fas fa-swimming-pool"></i>
                        <h3>Alberca Infinity</h3>
                        <p>Disfruta de nuestra espectacular alberca infinity con vista al mar. Con áreas de descanso exclusivas y servicio de bar en la piscina.</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-utensils"></i>
                        <h3>Buffet Gourmet</h3>
                        <p>Saborea nuestra selección premium de platos internacionales y mariscos frescos preparados por chefs de renombre.</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-bed"></i>
                        <h3>Hamacas Premium</h3>
                        <p>Relájate en nuestras exclusivas hamacas de lujo con vista al mar, con servicio de atención personalizada.</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-cocktail"></i>
                        <h3>Bar en la Playa</h3>
                        <p>Disfruta de cócteles artesanales y bebidas premium en nuestro bar frente al mar con atención exclusiva.</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-spa"></i>
                        <h3>Área de Spa</h3>
                        <p>Experimenta tratamientos rejuvenecedores en nuestro spa de playa con productos naturales y terapeutas expertos.</p>
                    </div>
                    
                    <div class="feature-card">
                        <i class="fas fa-music"></i>
                        <h3>Ambiente Premium</h3>
                        <p>Disfruta de música en vivo, ambiente exclusivo y servicio de primera categoría en un entorno paradisíaco.</p>
                    </div>
                </div>
            </section>
            
            <!-- Sección de buffet -->
            <section class="buffet-section">
                <i class="fas fa-palm-tree palm-icon palm-1"></i>
                <i class="fas fa-palm-tree palm-icon palm-2"></i>
                
                <div class="buffet-content">
                    <div class="buffet-text">
                        <h2 class="section-title">Buffet Gourmet</h2>
                        <p style="color: #d1d0cd; font-size: 1.1rem; margin-bottom: 20px;">
                            Nuestro buffet premium ofrece una experiencia culinaria inigualable con ingredientes frescos y preparaciones exclusivas. Desde mariscos recién capturados hasta delicias internacionales, cada plato es una obra maestra.
                        </p>
                        
                        <div class="menu-list">
                            <div class="menu-item">
                                <i class="fas fa-fish"></i>
                                <div>
                                    <h3 style="color: var(--color-gold); font-family: var(--font-heading);">Mariscos Frescos</h3>
                                    <p>Ceviches, ostiones y langosta preparados al momento</p>
                                </div>
                            </div>
                            
                            <div class="menu-item">
                                <i class="fas fa-utensil-spoon"></i>
                                <div>
                                    <h3 style="color: var(--color-gold); font-family: var(--font-heading);">Cocina Internacional</h3>
                                    <p>Platos mediterráneos, asiáticos y locales de alta cocina</p>
                                </div>
                            </div>
                            
                            <div class="menu-item">
                                <i class="fas fa-glass-martini-alt"></i>
                                <div>
                                    <h3 style="color: var(--color-gold); font-family: var(--font-heading);">Barra Libre Premium</h3>
                                    <p>Vinos selectos, cervezas artesanales y cócteles exclusivos</p>
                                </div>
                            </div>
                            
                            <div class="menu-item">
                                <i class="fas fa-ice-cream"></i>
                                <div>
                                    <h3 style="color: var(--color-gold); font-family: var(--font-heading);">Postres Gourmet</h3>
                                    <p>Selección de postres finos preparados por nuestro pastelero</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="buffet-image">
                        <img src="https://images.unsplash.com/photo-1578474846511-04ba529f0b88?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" alt="Buffet gourmet">
                    </div>
                </div>
            </section>
            
            <!-- Sección de hamacas -->
            <section class="hammocks-section">
                <div class="hammocks-content">
                    <h2 class="section-title" style="color: var(--color-ocean);">Zona de Hamacas Premium</h2>
                    <p style="max-width: 700px; margin: 0 auto 30px; font-size: 1.1rem; color: var(--color-dark);">
                        Nuestras exclusivas hamacas de diseño ofrecen el máximo conforto frente al mar. Con materiales de primera calidad y servicio de atención personalizada, es el lugar perfecto para relajarse.
                    </p>
                    
                    <div class="hammocks-grid">
                        <div class="hammock-card">
                            <div class="hammock-image">
                                <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Hamaca frente al mar">
                            </div>
                            <div class="hammock-info">
                                <h3>Hamacas Deluxe</h3>
                                <p>Hamacas individuales con vista privilegiada al mar, incluyen mesa de servicio y sombrilla ajustable.</p>
                            </div>
                        </div>
                        
                        <div class="hammock-card">
                            <div class="hammock-image">
                                <img src="https://images.unsplash.com/photo-1505691938895-1758d7feb511?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Hamaca doble">
                            </div>
                            <div class="hammock-info">
                                <h3>Hamacas Dobles</h3>
                                <p>Amplias hamacas para parejas con servicio premium y atención personalizada durante todo el día.</p>
                            </div>
                        </div>
                        
                        <div class="hammock-card">
                            <div class="hammock-image">
                                <img src="https://images.unsplash.com/photo-1508804185872-d7badad00f7d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Zona VIP">
                            </div>
                            <div class="hammock-info">
                                <h3>Zona VIP</h3>
                                <p>Área exclusiva con hamacas de lujo, servicio de botones y atención prioritaria en restaurante y bar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Sección de alberca -->
            <section class="pool-section">
                <h2 class="section-title">Alberca Infinity</h2>
                <p style="max-width: 800px; margin: 0 auto 40px; font-size: 1.1rem; color: var(--color-sand);">
                    Nuestra espectacular alberca infinity parece fusionarse con el océano. Con áreas de descanso exclusivas, jacuzzis integrados y servicio de bar en la piscina, es el lugar perfecto para disfrutar del sol caribeño.
                </p>
                
                <div class="pool-features">
                    <div class="pool-feature">
                        <i class="fas fa-water"></i>
                        <h3>Vistas Panorámicas</h3>
                        <p>Disfruta de vistas ininterrumpidas al mar Caribe desde cualquier punto de la piscina.</p>
                    </div>
                    
                    <div class="pool-feature">
                        <i class="fas fa-couch"></i>
                        <h3>Camas Flotantes</h3>
                        <p>Relájate en nuestras exclusivas camas flotantes con servicio de bebidas premium.</p>
                    </div>
                    
                    <div class="pool-feature">
                        <i class="fas fa-hot-tub"></i>
                        <h3>Jacuzzis Integrados</h3>
                        <p>Áreas de hidromasaje estratégicamente ubicadas para máximo confort.</p>
                    </div>
                    
                    <div class="pool-feature">
                        <i class="fas fa-concierge-bell"></i>
                        <h3>Servicio Premium</h3>
                        <p>Atención personalizada con botones dedicados para cada zona de la piscina.</p>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
   <footer>
    <?php include('../includes/footer.php'); ?>
  </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animación para los elementos
            const featureCards = document.querySelectorAll('.feature-card');
            
            featureCards.forEach((card, index) => {
                // Retraso escalonado para la animación
                card.style.transitionDelay = `${index * 0.1}s`;
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                
                setTimeout(() => {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 500 + index * 100);
            });
            
            // Animación para el hero
            const heroContent = document.querySelector('.hero-content');
            heroContent.style.opacity = "0";
            heroContent.style.transform = "translateY(30px)";
            
            setTimeout(() => {
                heroContent.style.transition = "all 1s ease-out";
                heroContent.style.opacity = "1";
                heroContent.style.transform = "translateY(0)";
            }, 300);
        });
    </script>
</body>
</html>