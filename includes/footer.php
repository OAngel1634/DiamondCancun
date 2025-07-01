<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Optimizado - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ============= VARIABLES CSS ============= */
        :root {
            --overlay-dark: rgba(0, 0, 0, 0.8);
            --overlay-light: rgba(255, 255, 255, 0.1);
            --accent: #4CAF50;
            --accent-alt: #45a049;
            --text-secondary: #bdbdbd;
            --light: #ffffff;
            --dark: #333333;
            --border-radius-small: 4px;
            --font-size-xs: 0.75rem;
            --font-size-md: 1.25rem;
            --transition-speed: 0.3s;
        }

        /* ================= RESET Y ESTILOS GENERALES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .main-content {
            flex: 1;
            max-width: 1200px;
            margin: 100px auto 50px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .main-content h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .main-content p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto 30px;
        }

        /* ================= FOOTER ================= */
        footer {
            background: var(--overlay-dark);
            padding: 30px 20px 20px;
            text-align: center;
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
            z-index: 10;
            margin-top: 50px;
        }

        footer a {
            color: var(--accent);
            text-decoration: none;
            transition: color var(--transition-speed) ease;
        }

        footer a:hover {
            color: var(--accent-alt);
            text-decoration: underline;
        }

        .customer-service {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .customer-service h2 {
            margin-bottom: 15px;
            font-weight: 700;
            font-size: var(--font-size-md);
            color: var(--light);
        }

        .contact-numbers {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .contact-numbers li {
            background: var(--overlay-light);
            padding: 12px 20px;
            border-radius: var(--border-radius-small);
            transition: background var(--transition-speed) ease;
            min-width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contact-numbers li i {
            margin-bottom: 8px;
            font-size: 1.2rem;
        }

        .contact-numbers li:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .contact-numbers select {
            background-color: rgba(68, 68, 68, 0.7);
            color: var(--light);
            border: none;
            padding: 8px 12px;
            border-radius: var(--border-radius-small);
            margin-top: 8px;
            width: 100%;
            cursor: pointer;
        }

        .contact-buttons {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin: 25px 0;
        }

        .contact-buttons button {
            background-color: var(--accent);
            color: var(--dark);
            border: none;
            border-radius: var(--border-radius-small);
            padding: 12px 25px;
            display: flex;
            align-items: center;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
        }

        .contact-buttons button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(119, 221, 119, 0.3);
            background-color: var(--accent-alt);
        }

        .contact-buttons button i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 20px;
            padding: 10px;
        }

        .social-button a {
            color: var(--accent);
            font-size: 2rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform var(--transition-speed) ease, color var(--transition-speed) ease;
        }

        .social-button a:hover {
            transform: translateY(-5px) scale(1.1);
            color: var(--light);
        }

        .copyright {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .legal-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        /* Footer especial para páginas legales */
        .footer-legal {
            padding: 20px;
            text-align: center;
            background: rgba(0, 0, 0, 0.9);
        }

        .footer-legal .copyright {
            border-top: none;
            margin-top: 0;
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .contact-numbers li {
                min-width: 100%;
                padding: 15px;
            }
            
            .contact-buttons button {
                width: 100%;
                justify-content: center;
            }
            
            .social-buttons {
                gap: 15px;
            }
            
            .legal-links {
                gap: 10px;
            }
        }
    </style>
</head>
<body>

    <!-- Footer optimizado -->
    <footer>
        <div class="customer-service">
            <h2>Servicio al cliente / Ventas</h2>
            <p>Lunes a Viernes de 7:30 a.m. a 7:30 p.m. / Sábado y Domingo: 7:30 a.m. a 6:00 p.m. / Horario Local.</p>

            <ul class="contact-numbers" aria-label="Números de contacto">
                <li>
                    <i class="fas fa-phone"></i>
                    <span>México: 295‑883‑3142</span>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <span>USA / CAN: 1‑855‑295‑2950</span>
                </li>
                <li>
                    <i class="fas fa-globe"></i>
                    <span>Otro país o región</span>
                    <select aria-label="Seleccionar país">
                        <option>Seleccione un país</option>
                        <option>Argentina</option>
                        <option>Brasil</option>
                        <option>Chile</option>
                        <option>Colombia</option>
                        <option>España</option>
                    </select>
                </li>
            </ul>

            <div class="contact-buttons">
                <button aria-label="Contactar por WhatsApp">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button aria-label="Chatear en la Web">
                    <i class="fas fa-comments"></i> Chatea en la Web
                </button>
                <button aria-label="Contactar por Messenger">
                    <i class="fab fa-facebook-messenger"></i> Messenger
                </button>
            </div>

            <div class="social-buttons">
                <div class="social-button">
                    <a href="#" aria-label="Facebook" target="_blank" rel="noopener">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </div>
                <div class="social-button">
                    <a href="#" aria-label="TikTok" target="_blank" rel="noopener">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
                <div class="social-button">
                    <a href="#" aria-label="Instagram" target="_blank" rel="noopener">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                <div class="social-button">
                    <a href="#" aria-label="Twitter" target="_blank" rel="noopener">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2023 Catamaran Diamond Bright - Todos los derechos reservados</p>
                <div class="legal-links">
                    <a href="#">Política de privacidad</a>
                    <a href="#">Términos y condiciones</a>
                    <a href="#">Política de cookies</a>
                    <a href="#">Aviso legal</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>