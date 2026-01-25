<?php
declare(strict_types=1);

// ============================================
// CONFIGURACIÓN Y BOOTSTRAP
// ============================================

// Configuración de errores
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

// Determinar entorno
$isProduction = ($_ENV['RAILWAY_ENVIRONMENT'] ?? $_ENV['NODE_ENV'] ?? 'development') === 'production';
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || 
           ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1' ||
           str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

// Configuración de sesión
session_start([
    'cookie_path' => '/',
    'cookie_secure' => $isProduction,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cache_limiter' => 'nocache'
]);

// ============================================
// CONSTANTES Y CONFIGURACIÓN DE RUTAS
// ============================================

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEWS_PATH', APP_PATH . '/Views');
define('CONTROLLERS_PATH', APP_PATH . '/Controllers');
define('MODELS_PATH', APP_PATH . '/Models');

// Configurar rutas base para assets
$baseUrl = ($isProduction ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$assetsBase = $baseUrl . '/assets/';

// ============================================
// AUTOCARGADOR SIMPLE
// ============================================

spl_autoload_register(function ($className) {
    $directories = [
        MODELS_PATH . '/',
        CONTROLLERS_PATH . '/',
        APP_PATH . '/Core/',
        APP_PATH . '/Services/'
    ];
    
    $className = str_replace('\\', '/', $className);
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ============================================
// AUTENTICACIÓN DEL USUARIO
// ============================================

$isAuthenticated = false;
$nombreUsuario = '';
$emailUsuario = '';

if (isset($_SESSION['usuario_id']) && is_numeric($_SESSION['usuario_id'])) {
    $isAuthenticated = true;
    $nombreUsuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
    $emailUsuario = htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8');
}

// ============================================
// ROUTER SIMPLE
// ============================================

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

// Rutas públicas que no requieren autenticación
$publicRoutes = [
    '/',
    '/login',
    '/registro',
    '/tours',
    '/snorkel',
    '/club',
    '/islamujeres',
    '/parquescuatico'
];

// Si no es ruta pública y no está autenticado, redirigir a login
if (!$isAuthenticated && !in_array($requestUri, $publicRoutes) && $requestUri !== '/') {
    header('Location: /login');
    exit;
}

// ============================================
// ENCABEZADOS HTTP
// ============================================

header("Content-Type: text/html; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ============================================
// VISTA - INICIO
// ============================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio – DiamondPrueba</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="<?php echo $assetsBase; ?>css/styles.css?v=<?php echo time(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $assetsBase; ?>imagenes/favicon.ico">
</head>
<body>
    <!-- WRAPPER -->
    <div id="content-wrapper">
        
        <!-- NAVEGACIÓN PRINCIPAL -->
        <nav aria-label="Navegación principal">
            <div class="nav-container">
                <div class="nav-logo">
                    <a href="/">
                        <img src="<?php echo $assetsBase; ?>imagenes/logo.png" alt="Diamond Bright Logo" height="40">
                    </a>
                </div>
                
                <ul class="nav-menu">
                    <li><a href="/islamujeres">Isla Mujeres</a></li>
                    <li><a href="/snorkel">Snorkeling</a></li>
                    <li><a href="/club">Club playa</a></li>
                    <li><a href="/parquescuatico">Parque acuático</a></li>
                    <li><a href="/tours">Tours</a></li>
                </ul>
                
                <!-- ICONO DE USUARIO -->
                <div class="user-icon-container" aria-label="Acceso de usuario">
                    <div class="user-icon <?php echo $isAuthenticated ? 'authenticated' : ''; ?>" 
                         id="userIcon" 
                         tabindex="0" 
                         role="button" 
                         aria-expanded="false">
                        <i class="fas fa-user" aria-hidden="true"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown" aria-hidden="true">
                        <?php if (!$isAuthenticated): ?>
                            <a href="/login" id="loginLink" role="button">
                                <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
                            </a>
                            <a href="/registro" id="registerLink" role="button">
                                <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
                            </a>
                        <?php else: ?>
                            <div class="user-info">
                                <strong><?php echo $nombreUsuario ?: 'Usuario'; ?></strong><br>
                                <small><?php echo $emailUsuario ?: ''; ?></small>
                            </div>
                            <a href="/dashboard"><i class="fas fa-tachometer-alt" aria-hidden="true"></i> Dashboard</a>
                            <a href="/perfil"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi perfil</a>
                            <a href="/reservas"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mis reservas</a>
                            <a href="/configuracion"><i class="fas fa-cog" aria-hidden="true"></i> Configuración</a>
                            <a href="/logout" id="logoutLink"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- NOTIFICACIONES -->
        <div class="notification" id="notification" style="display: none;">
            <i class="fas fa-info-circle"></i>
            <span id="notificationText">Mensaje de notificación</span>
        </div>

        <!-- HERO SECTION -->
        <section class="hero" role="banner">
            <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán">
                <source src="<?php echo $assetsBase; ?>imagenes/Catamaran.mp4" type="video/mp4">
                Tu navegador no soporta el elemento de video.
            </video>
            <div class="hero-content">
                <h1>
                    Catamaran<br>
                    <span class="title-line">Diamond Bright</span>
                </h1>
                <p class="subtitle">Somos más que un Tour</p>
                <p class="tagline">Somos una experiencia de por vida</p>
                
                <?php if (!$isAuthenticated): ?>
                    <div class="redirect-container">
                        <a href="/login" class="redirect-btn">
                            <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                        </a>
                        <a href="/registro" class="redirect-btn">
                            <i class="fas fa-user-plus"></i> Registrarse
                        </a>
                    </div>
                <?php else: ?>
                    <div class="redirect-container">
                        <a href="/reserva" class="redirect-btn primary">
                            <i class="fas fa-calendar-plus"></i> Reservar ahora
                        </a>
                        <a href="/tours" class="redirect-btn secondary">
                            <i class="fas fa-binoculars"></i> Ver tours
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- GALERÍA -->
        <section class="gallery-section" aria-labelledby="gallery-heading">
            <h2 id="gallery-heading" class="section-title">Maravíllate - Inspírate - Vive el mar</h2>
            <div class="gallery">
                <figure class="gallery-item">
                    <img src="<?php echo $assetsBase; ?>imagenes/grupo-catamaran.jpg" 
                         alt="Grupo disfrutando en el catamarán Diamond Bright">
                    <figcaption class="img-caption">Grupo en el catamarán</figcaption>
                </figure>
                <figure class="gallery-item">
                    <img src="<?php echo $assetsBase; ?>imagenes/muelle-tropical.jpg" 
                         alt="Muelle tropical en Cancún">
                    <figcaption class="img-caption">Muelle tropical</figcaption>
                </figure>
                <figure class="gallery-item">
                    <img src="<?php echo $assetsBase; ?>imagenes/vista-mar.jpg" 
                         alt="Vista al mar Caribe desde el catamarán">
                    <figcaption class="img-caption">Vista al mar</figcaption>
                </figure>
                <figure class="gallery-item">
                    <img src="<?php echo $assetsBase; ?>imagenes/snorkel-experiencia.jpg" 
                         alt="Experiencia de snorkel con Diamond Bright">
                    <figcaption class="img-caption">Snorkel bajo el agua</figcaption>
                </figure>
            </div>
        </section>

        <!-- MAPA -->
        <section class="map-section" aria-labelledby="map-heading">
            <h2 class="section-title map-title" id="map-heading">
                <span class="first-line">Cómo llegar a</span>
                <span class="second-line">Nuestra sucursal</span>
            </h2>
            
            <div class="map-container" aria-labelledby="map-heading">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.8924849176187!2d-86.80201992473988!3d21.156676580524767!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f4c293e862f2c95%3A0xc03a2d75f3d562e2!2sDiamond%20Bright%20Catamaran%20Canc%C3%BAn!5e0!3m2!1ses-419!2smx!4v1750223700133!5m2!1ses-419!2smx" 
                        allowfullscreen 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade" 
                        title="Ubicación de Diamond Bright Catamarán"></iframe>
            </div>
        </section>

        <!-- ACERCA DE -->
        <section class="about" aria-labelledby="about-heading">
            <div class="about-image">
                <img src="<?php echo $assetsBase; ?>imagenes/Poster.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
            </div>
            <div class="about-text">
                <h2 id="about-heading">Acerca de nosotros</h2>
                <p>Vive la aventura en uno de nuestros lujosos catamaranes de 45 pies sobre el hermoso Mar Caribe disfrutando de una bebida de nuestro extenso menú de barra libre, donde nuestra increíble tripulación te hará pasar un gran momento. Haz Snorkel en el segundo arrecife de coral más grande del mundo con una biodiversidad incomparable, visita el encantador pueblo de Isla Mujeres catalogado como uno de los pueblos mágicos de México, también puedes nadar en una de las playas más hermosas del mundo, Playa Norte.</p>
                <p>Después de tanta diversión, disfruta del delicioso buffet que hemos preparado para ti, relájate en nuestro club de playa o disfruta de los diversos servicios que tiene, como sillones, columpios, camastros y hamacas. Para terminar el día, atrévete a realizar la divertida actividad del spinnaker bajo la puesta de sol de Cancún si las condiciones climáticas lo permiten.</p>
            </div>
        </section>

        <!-- PROMO -->
        <section class="promo" aria-labelledby="promo-heading">
            <div class="cta">
                <h2 id="promo-heading">Snorkel en el museo acuático</h2>
                <a href="/reserva" class="btn-pill" id="saberMasBtn" role="button">
                    <span class="btn-text">Reservar ahora</span>
                    <span class="btn-icon">➔</span>
                </a>
            </div>
            <div class="image">
                <img src="<?php echo $assetsBase; ?>imagenes/Hand.jpg" alt="Vela en Isla Mujeres con Diamond Bright">
            </div>
        </section>

        <!-- CLIMA -->
        <div class="weather" aria-label="Información del clima">
            <div class="weather-top">
                <div class="weather-left">
                    <i class="weather-icon fas fa-sun" aria-hidden="true"></i>
                    <div class="weather-info">
                        <div class="weather-temp">28°C</div>
                        <div class="weather-desc">Soleado</div>
                        <div class="weather-detail">Prob. de precipitaciones: 10%</div>
                        <div class="weather-detail">Humedad: 65%</div>
                        <div class="weather-detail">Viento: 12 km/h</div>
                    </div>
                </div>
                <div class="weather-time">
                    <div>Clima actual</div>
                    <div id="current-time"><?php 
                        date_default_timezone_set('America/Cancun');
                        echo date('l H:i');
                    ?></div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <footer class="main-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Diamond Bright</h3>
                    <p>Experiencias inolvidables en el Caribe Mexicano</p>
                </div>
                <div class="footer-section">
                    <h4>Enlaces rápidos</h4>
                    <ul>
                        <li><a href="/tours">Tours</a></li>
                        <li><a href="/reserva">Reservar</a></li>
                        <li><a href="/contacto">Contacto</a></li>
                        <li><a href="/terminos">Términos y condiciones</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contacto</h4>
                    <p><i class="fas fa-phone"></i> +52 998 123 4567</p>
                    <p><i class="fas fa-envelope"></i> info@diamondbright.com</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Diamond Bright Catamarán. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- SCRIPTS -->
    <script src="<?php echo $assetsBase; ?>js/index.js?v=<?php echo time(); ?>"></script>
    <script>
        // Configuración global
        window.appConfig = {
            baseUrl: '<?php echo $baseUrl; ?>',
            assetsBase: '<?php echo $assetsBase; ?>',
            isAuthenticated: <?php echo $isAuthenticated ? 'true' : 'false'; ?>,
            user: {
                name: '<?php echo addslashes($nombreUsuario); ?>',
                email: '<?php echo addslashes($emailUsuario); ?>'
            }
        };

        // Inicialización cuando el DOM está listo
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown de usuario
            const userIcon = document.getElementById('userIcon');
            const userDropdown = document.getElementById('userDropdown');
            
            if (userIcon && userDropdown) {
                userIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isExpanded = userIcon.getAttribute('aria-expanded') === 'true';
                    userIcon.setAttribute('aria-expanded', !isExpanded);
                    userDropdown.setAttribute('aria-hidden', isExpanded);
                    userDropdown.classList.toggle('active', !isExpanded);
                });
                
                document.addEventListener('click', function(e) {
                    if (!userIcon.contains(e.target) && !userDropdown.contains(e.target)) {
                        userIcon.setAttribute('aria-expanded', 'false');
                        userDropdown.setAttribute('aria-hidden', 'true');
                        userDropdown.classList.remove('active');
                    }
                });
            }
            
            // Actualizar hora cada minuto
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const updateTime = () => {
                    const now = new Date();
                    const options = { 
                        weekday: 'long', 
                        hour: '2-digit', 
                        minute: '2-digit',
                        timeZone: 'America/Cancun'
                    };
                    timeElement.textContent = now.toLocaleDateString('es-MX', options);
                };
                updateTime();
                setInterval(updateTime, 60000);
            }
            
            // Notificaciones
            const notification = document.getElementById('notification');
            if (notification) {
                window.showNotification = function(message, type = 'info') {
                    const text = document.getElementById('notificationText');
                    text.textContent = message;
                    notification.className = 'notification ' + type;
                    notification.style.display = 'block';
                    
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 5000);
                };
            }
        });
    </script>
</body>
</html>