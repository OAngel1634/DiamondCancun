<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tours Premium a Isla Mujeres | Diamond Bright Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS */
        :root {
            /* Paleta premium */
            --color-primary: #0a1a2f;
            --color-secondary: #1a3a5f;
            --color-accent: #d4af37;
            --color-light: #f8f7f3;
            --color-dark: #0c1625;
            --color-mid: #3a506b;
            --color-light-accent: #f5e7c1;
            --color-transparent: rgba(10, 26, 47, 0.85);
            --color-silver: #c0c0c0;
            --color-gold: #ffd700;
            --color-diamond: #b9f2ff;
            
            /* Transiciones */
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            
            /* Tipografía */
            --font-heading: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Montserrat', 'Segoe UI', Tahoma, sans-serif;
            
            /* Bordes y sombras */
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            --box-shadow-hover: 0 20px 50px rgba(0, 0, 0, 0.2);
            --box-shadow-card: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        /* Reset y estilos base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: var(--font-body);
            background: linear-gradient(135deg, var(--color-primary), var(--color-secondary));
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
        
        section {
            padding: 80px 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        
        
        /* Sección Hero */
        .hero {
            position: relative;
            height: 90vh;
            min-height: 700px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
        }
        
        .hero:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(10, 26, 47, 0.9), rgba(10, 26, 47, 0.6));
            z-index: 1;
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
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .hero-content span {
            color: var(--color-accent);
            display: block;
        }
        
        .hero-content p {
            font-size: 1.4rem;
            max-width: 700px;
            margin: 0 auto 40px;
            font-weight: 300;
            color: var(--color-light-accent);
        }
        
        /* Botones */
        .cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: var(--color-light);
            padding: 16px 45px;
            border-radius: 0;
            font-weight: 500;
            font-size: 1.1rem;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid var(--color-accent);
            position: relative;
            overflow: hidden;
            z-index: 1;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: var(--font-heading);
            cursor: pointer;
        }
        
        .cta-button:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--color-accent);
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
        
        /* Títulos de sección */
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
            background: var(--color-accent);
            margin: 20px auto;
        }
        
        .section-subtitle {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
            font-size: 1.2rem;
            color: var(--color-light-accent);
            font-weight: 300;
        }
        
        /* Tarjetas de Tours */
        .tours-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .tour-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow-card);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        
        .tour-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--box-shadow-hover);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .card-header {
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }
        
        .card-header.silver {
            background: linear-gradient(135deg, rgba(192, 192, 192, 0.15), rgba(128, 128, 128, 0.1));
        }
        
        .card-header.gold {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15), rgba(218, 165, 32, 0.1));
        }
        
        .card-header.diamond {
            background: linear-gradient(135deg, rgba(185, 242, 255, 0.15), rgba(176, 224, 230, 0.1));
        }
        
        .tour-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: var(--color-accent);
        }
        
        .tour-card h3 {
            font-size: 2.2rem;
            font-family: var(--font-heading);
            margin-bottom: 15px;
            color: var(--color-light);
        }
        
        .tour-card .card-header p {
            font-size: 1.1rem;
            color: var(--color-light-accent);
            margin-bottom: 20px;
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 600;
            font-family: var(--font-heading);
            margin: 15px 0;
        }
        
        .price.silver {
            color: var(--color-silver);
        }
        
        .price.gold {
            color: var(--color-gold);
        }
        
        .price.diamond {
            color: var(--color-diamond);
        }
        
        .price span {
            font-size: 1.2rem;
            font-weight: 400;
        }
        
        .card-body {
            padding: 30px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .features {
            list-style: none;
            margin-bottom: 30px;
            flex-grow: 1;
        }
        
        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 1.05rem;
        }
        
        .features li i {
            color: var(--color-accent);
            margin-top: 4px;
            min-width: 20px;
        }
        
        .features li .fa-check-circle {
            color: #4CAF50;
        }
        
        .features li .fa-times-circle {
            color: #f44336;
        }
        
        .btn-tour {
            display: block;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            font-size: 1.1rem;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: auto;
        }
        
        .btn-silver {
            background: rgba(192, 192, 192, 0.2);
            color: var(--color-light);
            border: 1px solid var(--color-silver);
        }
        
        .btn-silver:hover {
            background: rgba(192, 192, 192, 0.4);
        }
        
        .btn-gold {
            background: rgba(255, 215, 0, 0.2);
            color: var(--color-light);
            border: 1px solid var(--color-gold);
        }
        
        .btn-gold:hover {
            background: rgba(255, 215, 0, 0.4);
        }
        
        .btn-diamond {
            background: rgba(185, 242, 255, 0.2);
            color: var(--color-light);
            border: 1px solid var(--color-diamond);
        }
        
        .btn-diamond:hover {
            background: rgba(185, 242, 255, 0.4);
        }
        
        /* Timeline Itinerario */
        .timeline {
            position: relative;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .timeline:before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 100%;
            background: var(--color-accent);
        }
        
        .timeline-item {
            display: flex;
            justify-content: flex-end;
            padding-right: 30px;
            position: relative;
            margin: 40px 0;
            width: 50%;
        }
        
        .timeline-item:nth-child(odd) {
            align-self: flex-end;
            justify-content: flex-start;
            padding-left: 30px;
            padding-right: 0;
            left: 50%;
        }
        
        .timeline-content {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(212, 175, 55, 0.1);
            padding: 30px;
            border-radius: var(--border-radius);
            width: 100%;
            box-shadow: var(--box-shadow-card);
            transition: var(--transition);
            position: relative;
        }
        
        .timeline-item:hover .timeline-content {
            transform: translateY(-5px);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .timeline-content:before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--color-accent);
            border-radius: 50%;
            top: 30px;
            right: -10px;
            z-index: 1;
        }
        
        .timeline-item:nth-child(odd) .timeline-content:before {
            left: -10px;
            right: auto;
        }
        
        .timeline-content h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--color-accent);
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: var(--font-heading);
        }
        
        .timeline-content h3 i {
            font-size: 1.8rem;
        }
        
        /* Testimonios */
        .testimonials-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }
        
        .testimonial-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius);
            padding: 40px 35px 35px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            transition: var(--transition);
            box-shadow: var(--box-shadow-card);
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .testimonial-card:before {
            content: '"';
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 6rem;
            color: var(--color-accent);
            opacity: 0.15;
            font-family: Georgia, serif;
            line-height: 1;
            font-weight: 700;
        }
        
        .testimonial-content {
            margin-bottom: 30px;
            font-style: italic;
            padding-left: 40px;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
            color: #e6e5e2;
            line-height: 1.8;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            padding-left: 40px;
            position: relative;
            z-index: 1;
        }
        
        .author-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
            background: var(--color-mid);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-accent);
            font-size: 1.5rem;
            font-weight: bold;
            border: 2px solid var(--color-accent);
        }
        
        .author-info h4 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: var(--color-light);
        }
        
        .author-info p {
            color: var(--color-light-accent);
            font-size: 0.9rem;
        }
        
        /* FAQ */
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .faq-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-sm);
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: var(--transition);
        }
        
        .faq-item.active {
            border-color: rgba(212, 175, 55, 0.3);
        }
        
        .faq-question {
            padding: 25px;
            font-size: 1.2rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: var(--transition);
        }
        
        .faq-item.active .faq-question {
            color: var(--color-accent);
        }
        
        .faq-question i {
            transition: var(--transition);
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: var(--transition);
            color: var(--color-light-accent);
        }
        
        .faq-item.active .faq-answer {
            padding: 0 25px 25px;
            max-height: 500px;
        }
        
        /* CTA */
        .cta-section {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.1), rgba(10, 26, 47, 0.7));
            border: 1px solid rgba(212, 175, 55, 0.2);
            border-radius: var(--border-radius);
            padding: 60px 40px;
            text-align: center;
            margin: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section:before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
        }
        
        .cta-section h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: var(--color-light);
            font-family: var(--font-heading);
        }
        
        .cta-section p {
            max-width: 700px;
            margin: 0 auto 30px;
            font-size: 1.2rem;
            color: var(--color-light-accent);
        }
        
        .cta-btn {
            background: var(--color-accent);
            color: var(--color-dark);
            font-weight: 600;
            padding: 16px 50px;
            font-size: 1.1rem;
            border: none;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            font-family: var(--font-heading);
            letter-spacing: 1px;
            display: inline-block;
            text-decoration: none;
        }
        
        .cta-btn:hover {
            background: var(--color-light);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
        }
        
        
        /* Botón flotante */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--color-accent);
            color: var(--color-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 999;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            background: var(--color-light);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero-content h1 { font-size: 3.5rem; }
            .section-title { font-size: 2.5rem; }
            .timeline:before { left: 30px; }
            .timeline-item { width: 100%; padding-right: 0; padding-left: 70px; }
            .timeline-item:nth-child(odd) { left: 0; padding-left: 70px; }
            .timeline-content:before { left: -10px; right: auto; }
        }
        
        @media (max-width: 768px) {
            nav ul { display: none; }
            .mobile-menu-btn { display: block; }
            .hero-content h1 { font-size: 2.8rem; }
            .hero-content p { font-size: 1.2rem; }
            .section-title { font-size: 2.2rem; }
            .tours-container { grid-template-columns: 1fr; }
            .testimonials-container { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
        }
        
        @media (max-width: 480px) {
            .hero { height: 85vh; min-height: 600px; }
            .hero-content h1 { font-size: 2.2rem; }
            .cta-button { padding: 14px 30px; font-size: 1rem; }
            .section-title { font-size: 2rem; }
            .tour-card { margin-bottom: 30px; }
            .timeline-item { padding-left: 50px; }
            .timeline-item:nth-child(odd) { padding-left: 50px; }
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
   <header>
      <?php include('../includes/header.php'); ?>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Descubre la Magia de <span>Isla Mujeres</span></h1>
            <p>Experimenta la belleza del Caribe con nuestros exclusivos tours. Elige entre nuestras experiencias Plata, Oro o Diamante y crea recuerdos inolvidables.</p>
            <a href="#tours" class="cta-button">Ver Tours Disponibles <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    <!-- Tours Section -->
    <section id="tours">
        <div class="container">
            <div class="section-title">
                <h2>Nuestros Tours Premium</h2>
            </div>
            <div class="section-subtitle">
                <p>Selecciona la experiencia que mejor se adapte a tus preferencias. Todas incluyen transporte en catamarán de lujo y acceso a nuestras instalaciones exclusivas.</p>
            </div>
            
            <div class="tours-container">
                <!-- Silver Tour Card -->
                <div class="tour-card">
                    <div class="card-header silver">
                        <i class="fas fa-anchor tour-icon"></i>
                        <h3>Tour Plata</h3>
                        <p>Ideal para disfrutar de la esencia de Isla Mujeres</p>
                        <div class="price silver">$1,199 <span>MXN</span></div>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li><i class="fas fa-check-circle"></i> Transporte en catamarán de lujo</li>
                            <li><i class="fas fa-check-circle"></i> Visita a Playa Norte (top 10 playas del mundo)</li>
                            <li><i class="fas fa-check-circle"></i> Tiempo libre para explorar la isla</li>
                            <li><i class="fas fa-check-circle"></i> Almuerzo buffet con opciones mexicanas</li>
                            <li><i class="fas fa-check-circle"></i> Agua purificada durante el recorrido</li>
                            <li><i class="fas fa-times-circle"></i> Bebidas ilimitadas</li>
                            <li><i class="fas fa-times-circle"></i> Acceso a zona VIP</li>
                        </ul>
                        <a href="#reserva" class="btn-tour btn-silver">Reservar Ahora</a>
                    </div>
                </div>
                
                <!-- Gold Tour Card -->
                <div class="tour-card">
                    <div class="card-header gold">
                        <i class="fas fa-crown tour-icon"></i>
                        <h3>Tour Oro</h3>
                        <p>Experiencia premium con beneficios exclusivos</p>
                        <div class="price gold">$1,799 <span>MXN</span></div>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li><i class="fas fa-check-circle"></i> Todo lo del Tour Plata +</li>
                            <li><i class="fas fa-check-circle"></i> Bebidas no alcohólicas ilimitadas</li>
                            <li><i class="fas fa-check-circle"></i> Snorkel en el arrecife de coral</li>
                            <li><i class="fas fa-check-circle"></i> Acceso a zona exclusiva en la playa</li>
                            <li><i class="fas fa-check-circle"></i> Camastros premium y sombrillas</li>
                            <li><i class="fas fa-check-circle"></i> Recuerdo fotográfico digital</li>
                            <li><i class="fas fa-times-circle"></i> Bebidas alcohólicas premium</li>
                        </ul>
                        <a href="#reserva" class="btn-tour btn-gold">Reservar Ahora</a>
                    </div>
                </div>
                
                <!-- Diamond Tour Card -->
                <div class="tour-card">
                    <div class="card-header diamond">
                        <i class="fas fa-gem tour-icon"></i>
                        <h3>Tour Diamante</h3>
                        <p>La máxima experiencia en lujo y comodidad</p>
                        <div class="price diamond">$2,499 <span>MXN</span></div>
                    </div>
                    <div class="card-body">
                        <ul class="features">
                            <li><i class="fas fa-check-circle"></i> Todo lo del Tour Oro +</li>
                            <li><i class="fas fa-check-circle"></i> Barra libre ilimitada (bebidas premium)</li>
                            <li><i class="fas fa-check-circle"></i> Transporte en catamarán VIP</li>
                            <li><i class="fas fa-check-circle"></i> Masaje de 15 minutos en la playa</li>
                            <li><i class="fas fa-check-circle"></i> Tour privado en carrito de golf por la isla</li>
                            <li><i class="fas fa-check-circle"></i> Cena en restaurante exclusivo</li>
                            <li><i class="fas fa-check-circle"></i> Fotografía profesional del recuerdo</li>
                        </ul>
                        <a href="#reserva" class="btn-tour btn-diamond">Reservar Ahora</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Itinerary Section -->
    <section id="itinerario" class="itinerary">
        <div class="container">
            <div class="section-title">
                <h2>Itinerario del Tour</h2>
            </div>
            <div class="section-subtitle">
                <p>Un día inolvidable lleno de experiencias en el paraíso caribeño</p>
            </div>
            
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-ship"></i> Salida desde Cancún</h3>
                        <p>8:00 AM - Salida en catamarán desde el muelle con bebidas de bienvenida. Breve introducción sobre la historia de Isla Mujeres.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-water"></i> Snorkel en el arrecife</h3>
                        <p>9:30 AM - Parada para snorkel en el segundo arrecife de coral más grande del mundo (Tour Oro y Diamante). Equipo incluido.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-umbrella-beach"></i> Playa Norte</h3>
                        <p>11:00 AM - Llegada a Playa Norte, famosa por sus aguas turquesas y arena blanca. Tiempo libre para nadar y relajarse.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-utensils"></i> Almuerzo buffet</h3>
                        <p>1:30 PM - Almuerzo buffet con especialidades mexicanas y mariscos frescos. Opciones vegetarianas disponibles.</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-map-marked-alt"></i> Exploración de la isla</h3>
                        <p>3:00 PM - Recorrido por los principales atractivos de Isla Mujeres: Punta Sur, el templo de Ixchel y el centro histórico (Tour Diamante en carrito de golf privado).</p>
                    </div>
                </div>
                
                <div class="timeline-item">
                    <div class="timeline-content">
                        <h3><i class="fas fa-sun"></i> Regreso al atardecer</h3>
                        <p>5:30 PM - Regreso a Cancún disfrutando de una espectacular puesta de sol sobre el Caribe con música y bebidas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonios">
        <div class="container">
            <div class="section-title">
                <h2>Lo Que Dicen Nuestros Viajeros</h2>
            </div>
            <div class="section-subtitle">
                <p>Experiencias reales de quienes han vivido la magia de Isla Mujeres con nosotros</p>
            </div>
            
            <div class="testimonials-container">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "El Tour Diamante superó todas mis expectativas. El servicio VIP, las bebidas premium y el tour privado en carrito de golf hicieron de este día algo realmente especial."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">C</div>
                        <div class="author-info">
                            <h4>Carlos Martínez</h4>
                            <p>Ciudad de México</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "Optamos por el Tour Oro y fue la mejor decisión. El snorkel fue increíble y tener bebidas ilimitadas todo el día hizo la experiencia aún mejor. ¡Volveremos!"
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">A</div>
                        <div class="author-info">
                            <h4>Ana Rodríguez</h4>
                            <p>Monterrey</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        "El Tour Plata es perfecto para quienes quieren conocer Isla Mujeres sin gastar demasiado. El catamarán es cómodo, la comida excelente y la playa es simplemente maravillosa."
                    </div>
                    <div class="testimonial-author">
                        <div class="author-image">J</div>
                        <div class="author-info">
                            <h4>Javier López</h4>
                            <p>Guadalajara</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq">
        <div class="container">
            <div class="section-title">
                <h2>Preguntas Frecuentes</h2>
            </div>
            <div class="section-subtitle">
                <p>Resolvemos tus dudas sobre nuestros tours a Isla Mujeres</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué incluye el precio del tour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        El precio incluye transporte ida y vuelta en catamarán, acceso a nuestras instalaciones en Isla Mujeres, almuerzo buffet, y los servicios específicos de cada nivel (Plata, Oro o Diamante). No incluye propinas, compras personales ni actividades adicionales no mencionadas.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Necesito saber nadar para el tour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Para disfrutar plenamente del tour, especialmente la experiencia de snorkel en el Tour Oro y Diamante, recomendamos saber nadar. Sin embargo, contamos con chalecos salvavidas para todos los pasajeros y actividades que no requieren entrar al agua.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Puedo cambiar de nivel durante el tour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Sí, es posible mejorar a un nivel superior durante el tour pagando la diferencia en nuestro centro de atención en Isla Mujeres. Sin embargo, recomendamos reservar el nivel deseado con anticipación para garantizar disponibilidad.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Qué debo llevar al tour? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Recomendamos traer protector solar biodegradable, traje de baño, toalla, cámara fotográfica, dinero en efectivo para compras adicionales, y una identificación oficial. Para el Tour Diamante, traer ropa cómoda para el paseo en carrito de golf.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        ¿Hay descuentos para niños? <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Sí, niños de 5 a 11 años tienen 30% de descuento en todos los niveles. Niños menores de 5 años viajan gratis (máximo 1 niño gratis por adulto pagado). El Tour Diamante incluye actividades especiales para niños.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <div class="container">
        <div class="cta-section">
            <h2>¿Listo para vivir una experiencia inolvidable?</h2>
            <p>Reserva hoy mismo tu tour a Isla Mujeres y asegura tu lugar en el paraíso. Oferta especial por tiempo limitado.</p>
            <a href="#reserva" class="cta-btn">Reservar Ahora <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>

    <footer>
    <?php include('../includes/footer.php'); ?>
  </footer>

    <!-- Botón flotante -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        // FAQ Toggle Functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('active');
            });
        });
        
        // Smooth Scrolling for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Animation for Tour Cards on Scroll
        const tourCards = document.querySelectorAll('.tour-card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });
        
        tourCards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(50px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
        
        // Back to top button
        const backToTopButton = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navMenu = document.querySelector('nav ul');
        
        mobileMenuBtn.addEventListener('click', () => {
            navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('nav a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navMenu.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>