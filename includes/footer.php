<?php

$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
$cssUrl    = URL_BASE . 'assets/css/footer.css';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Optimizado - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $cssUrl; ?>?v=<?php echo time(); ?>">
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