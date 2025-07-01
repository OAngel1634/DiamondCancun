<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snorkel Premium | DiamondPrueba</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Nueva paleta premium */
            --color-primary: #0a1a2f;
            --color-secondary: #1a3a5f;
            --color-accent: #d4af37; /* dorado */
            --color-light: #f8f7f3;
            --color-dark: #0c1625;
            --color-mid: #3a506b;
            --color-light-accent: #f5e7c1;
            --color-transparent: rgba(10, 26, 47, 0.85);
            
            /* Transiciones */
            --transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            
            /* Tipografía premium */
            --font-heading: 'Cormorant Garamond', Georgia, serif;
            --font-body: 'Montserrat', 'Segoe UI', Tahoma, sans-serif;
            
            /* Dimensiones */
            --border-radius: 10px;
            --border-radius-sm: 6px;
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
            background: linear-gradient(135deg, var(--color-primary), #081120);
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
        
        
        .hero {
            position: relative;
            height: 90vh;
            overflow: hidden;
            margin-bottom: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
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
        
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
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
        
        .hero-content p {
            font-size: 1.4rem;
            max-width: 700px;
            margin: 0 auto 40px;
            font-weight: 300;
            color: var(--color-light-accent);
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
            border: 1px solid var(--color-accent);
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
        
        /* Sección de información */
        .info-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius);
            padding: 80px 60px;
            margin-bottom: 80px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .info-section:before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
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
            background: var(--color-accent);
            margin: 20px auto;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .feature {
            background: rgba(255, 255, 255, 0.03);
            padding: 40px 35px;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .feature:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--color-accent);
            transform: translateX(-100%);
            transition: var(--transition);
        }
        
        .feature:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(212, 175, 55, 0.2);
        }
        
        .feature:hover:after {
            transform: translateX(0);
        }
        
        .feature i {
            font-size: 3rem;
            color: var(--color-accent);
            margin-bottom: 25px;
            transition: var(--transition);
        }
        
        .feature h3 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            font-family: var(--font-heading);
            letter-spacing: 0.5px;
            font-weight: 500;
            color: var(--color-light);
        }
        
        .feature p {
            font-weight: 300;
            font-size: 1.05rem;
            color: #d1d0cd;
        }
        
        /* Galería */
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 60px;
        }
        
        .gallery-item {
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            position: relative;
            height: 350px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }
        
        .gallery-item:hover {
            transform: translateY(-15px);
            box-shadow: var(--box-shadow-hover);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .gallery-item:hover img {
            transform: scale(1.05);
        }
        
        .gallery-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background: rgba(10, 26, 47, 0.9);
            padding: 20px;
            text-align: center;
            border-top: 2px solid var(--color-accent);
            font-family: var(--font-heading);
            font-size: 1.3rem;
        }
        
        /* Testimonios */
        .testimonials {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        
        .testimonial {
            background: rgba(255, 255, 255, 0.03);
            padding: 40px 35px 35px;
            border-radius: var(--border-radius);
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }
        
        .testimonial:hover {
            border-color: rgba(212, 175, 55, 0.2);
        }
        
        .testimonial:before {
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
        
        .testimonial p {
            margin-bottom: 30px;
            font-style: italic;
            padding-left: 40px;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
            color: #e6e5e2;
        }
        
        .client {
            display: flex;
            align-items: center;
            padding-left: 40px;
            position: relative;
            z-index: 1;
        }
        
        .client img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
            object-fit: cover;
            border: 2px solid var(--color-accent);
        }
        
        .client-info strong {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        .client-info .rating {
            color: var(--color-accent);
            letter-spacing: 2px;
            font-size: 0.9rem;
        }
        
        /* FAQ */
        .faq-container {
            max-width: 900px;
            margin: 60px auto 0;
        }
        
        .faq-item {
            background: rgba(255, 255, 255, 0.03);
            margin-bottom: 20px;
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: var(--transition);
        }
        
        .faq-item:hover {
            border-color: rgba(212, 175, 55, 0.2);
        }
        
        .faq-question {
            padding: 25px;
            background: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 400;
            font-size: 1.2rem;
            transition: var(--transition);
            position: relative;
            font-family: var(--font-heading);
        }
        
        .faq-question i {
            color: var(--color-accent);
            transition: var(--transition);
        }
        
        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s ease;
            background: rgba(0, 0, 0, 0.15);
        }
        
        .faq-item.active .faq-answer {
            padding: 25px;
            max-height: 500px;
        }
        
        
        /* Elementos decorativos premium */
        .decorative-line {
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--color-accent), transparent);
            margin: 50px auto;
        }
        
        .premium-badge {
            display: inline-block;
            background: rgba(212, 175, 55, 0.1);
            color: var(--color-accent);
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            letter-spacing: 1.5px;
            border: 1px solid var(--color-accent);
            margin-bottom: 30px;
            font-family: var(--font-heading);
        }
        
        /* Lista de beneficios */
        .benefits-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            list-style: none;
            margin-top: 20px;
        }
        
        .benefits-list li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }
        
        .benefits-list i {
            color: var(--color-accent);
            margin-top: 5px;
            font-size: 1.2rem;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 3.5rem;
            }
            
            .info-section {
                padding: 60px 40px;
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
            
            .hero-content p {
                font-size: 1.2rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .feature, .testimonial {
                padding: 30px 25px;
            }
            
            .info-section {
                padding: 40px 30px;
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
            
            .info-section {
                padding: 30px 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
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
                <video class="hero-video" autoplay muted loop playsinline>
                    <source src="https://assets.mixkit.co/videos/preview/mixkit-school-of-fish-swimming-in-the-deep-sea-1588-large.mp4" type="video/mp4">
                    Tu navegador no soporta el elemento de video.
                </video>
                <div class="hero-content">
                    <div class="premium-badge">EXPERIENCIA EXCLUSIVA</div>
                    <h1>SNORKEL <span>Premium</span></h1>
                    <p>Sumérgete en una experiencia submarina de lujo donde la elegancia se encuentra con la aventura. Descubre el mundo acuático con un toque de sofisticación.</p>
                    <a href="Reserva.php" class="cta-button">Reservar ahora <i class="fas fa-arrow-right"></i></a>
                </div>
            </section>
            
            <!-- Sección de información -->
            <section class="info-section">
                <h2 class="section-title">Descubre el Mundo Submarino</h2>
                <p style="text-align: center; max-width: 800px; margin: 0 auto 50px; font-size: 1.2rem; font-weight: 300; color: #d1d0cd;">
                    Nuestro tour premium de snorkel te lleva a las aguas cristalinas del Caribe, donde podrás explorar el segundo arrecife de coral más grande del mundo con comodidades exclusivas y atención personalizada.
                </p>
                
                <div class="decorative-line"></div>
                
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-crown"></i>
                        <h3>Guías Expertos</h3>
                        <p>Nuestros guías certificados te acompañarán en todo momento, garantizando tu seguridad y brindando información exclusiva sobre el ecosistema marino.</p>
                    </div>
                    
                    <div class="feature">
                        <i class="fas fa-gem"></i>
                        <h3>Equipo Premium</h3>
                        <p>Proporcionamos equipo de snorkel de alta gama: máscaras de titanio, aletas profesionales, chalecos salvavidas premium y trajes de neopreno de calidad superior.</p>
                    </div>
                    
                    <div class="feature">
                        <i class="fas fa-concierge-bell"></i>
                        <h3>Biodiversidad Exclusiva</h3>
                        <p>Observa más de 500 especies de peces, tortugas marinas, rayas y corales vibrantes en ubicaciones exclusivas de acceso limitado.</p>
                    </div>
                </div>
                
                <div style="background: rgba(212, 175, 55, 0.05); border-radius: var(--border-radius); padding: 40px; margin-top: 50px; border: 1px solid rgba(212, 175, 55, 0.15);">
                    <h3 style="text-align: center; margin-bottom: 30px; color: var(--color-accent); font-family: var(--font-heading); font-weight: 500; font-size: 1.8rem;">Lo que incluye tu experiencia premium:</h3>
                    <ul class="benefits-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Equipo completo de snorkel premium</strong>
                                <p style="margin-top: 5px;">Incluye máscara de titanio y aletas profesionales</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Guía profesional bilingüe</strong>
                                <p style="margin-top: 5px;">Atención personalizada durante todo el recorrido</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Instrucción personalizada</strong>
                                <p style="margin-top: 5px;">Adaptada a tu nivel de experiencia</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Bebidas gourmet</strong>
                                <p style="margin-top: 5px;">Selección de bebidas artesanales y snacks premium</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Fotografías submarinas profesionales</strong>
                                <p style="margin-top: 5px;">Con entrega en álbum de lujo</p>
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            <div>
                                <strong>Seguro de viaje premium</strong>
                                <p style="margin-top: 5px;">Cobertura ampliada para tu tranquilidad</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
            
            <!-- Galería -->
            <section class="info-section">
                <h2 class="section-title">Galería Submarina</h2>
                <p style="text-align: center; max-width: 700px; margin: 0 auto 50px; font-size: 1.1rem; font-weight: 300; color: #d1d0cd;">
                    Momentos capturados en nuestras experiencias premium, donde la elegancia y la aventura submarina se fusionan.
                </p>
                
                <div class="gallery">
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e" alt="Arrecife de coral">
                        <div class="caption">Arrecife Virgen</div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1559494007-9f5847c49d94" alt="Tortuga marina">
                        <div class="caption">Encuentro Elegante</div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1587502536575-6dfba0a6e017" alt="Peces tropicales">
                        <div class="caption">Vida Colorida</div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5" alt="Rayas">
                        <div class="caption">Danza de Rayas</div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1560279969-cc411a7987f8" alt="Snorkeling en grupo">
                        <div class="caption">Experiencia Grupal</div>
                    </div>
                    <div class="gallery-item">
                        <img src="https://images.unsplash.com/photo-1505118380757-91f5f5632de0" alt="Bajo el agua">
                        <div class="caption">Mundo Submarino</div>
                    </div>
                </div>
            </section>
            
            <!-- Testimonios -->
            <section class="info-section">
                <h2 class="section-title">Lo que dicen nuestros clientes</h2>
                <p style="text-align: center; max-width: 700px; margin: 0 auto 50px; font-size: 1.1rem; font-weight: 300; color: #d1d0cd;">
                    Testimonios de nuestros distinguidos clientes sobre su experiencia premium.
                </p>
                
                <div class="testimonials">
                    <div class="testimonial">
                        <p>"La experiencia premium realmente vale cada centavo. El servicio personalizado y las ubicaciones exclusivas hicieron que valiera la pena. ¡Ver tortugas marinas tan de cerca fue mágico!"</p>
                        <div class="client">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="María González">
                            <div class="client-info">
                                <strong>María González</strong>
                                <div class="rating">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial">
                        <p>"Perfecto para principiantes. Me sentí seguro en todo momento y los instructivos fueron muy claros. Las aguas cristalinas y la vida marina son impresionantes."</p>
                        <div class="client">
                            <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Carlos Mendoza">
                            <div class="client-info">
                                <strong>Carlos Mendoza</strong>
                                <div class="rating">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial">
                        <p>"Celebré mi aniversario con este tour premium y fue inolvidable. El detalle del álbum fotográfico premium fue el toque perfecto. El personal excedió todas nuestras expectativas."</p>
                        <div class="client">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Laura Jiménez">
                            <div class="client-info">
                                <strong>Laura Jiménez</strong>
                                <div class="rating">★★★★★</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- FAQ -->
            <section class="info-section">
                <h2 class="section-title">Preguntas Frecuentes</h2>
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question">
                            ¿Necesito experiencia previa para hacer snorkel?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>No, no necesitas experiencia previa. Nuestros guías te darán una instrucción personalizada antes de comenzar y te acompañarán durante todo el recorrido para garantizar tu seguridad y comodidad. Adaptamos la experiencia a tu nivel.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            ¿Qué debo llevar al tour premium?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Recomendamos traer: traje de baño, protector solar biodegradable y una muda de ropa. Nosotros proveemos todo el equipo premium de snorkel, toallas de alta calidad, gorras y todo lo necesario para tu comodidad.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            ¿Es seguro para niños?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Sí, nuestra experiencia premium es segura para niños a partir de 8 años. Contamos con equipo especializado para menores y chalecos salvavidas de diseño ergonómico. Los menores deben estar acompañados por un adulto.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            ¿Qué pasa si no sé nadar?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>No es necesario saber nadar. Todos nuestros participantes usan chalecos salvavidas premium de flotación regulable. Además, nuestros guías están especialmente entrenados para asistir a personas con poca experiencia en el agua.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            ¿Cuánto dura el tour premium?
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>La experiencia premium completa tiene una duración de 5 horas, que incluye transporte en embarcación exclusiva, preparación con aperitivos gourmet, dos inmersiones de snorkel (aproximadamente 2 horas en total) y tiempo de relax.</p>
                        </div>
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
            // FAQ functionality
            const faqItems = document.querySelectorAll('.faq-item');
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question');
                
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    
                    // Close all items first
                    faqItems.forEach(otherItem => {
                        otherItem.classList.remove('active');
                    });
                    
                    // Toggle current item
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>
</html>