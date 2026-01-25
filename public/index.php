<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '1');

$isProduction = ($_ENV['RAILWAY_ENVIRONMENT'] ?? $_ENV['NODE_ENV'] ?? 'development') === 'production';
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || 
           ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1' ||
           str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

session_start([
    'cookie_path' => '/',
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cache_limiter' => 'nocache'
]);

$isAuthenticated = false;
$nombreUsuario = '';
$emailUsuario = '';

if (isset($_SESSION['usuario_id']) && is_numeric($_SESSION['usuario_id'])) {
    $isAuthenticated = true;
    $nombreUsuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
    $emailUsuario = htmlspecialchars($_SESSION['usuario_email'] ?? '', ENT_QUOTES, 'UTF-8');
}

echo "<!-- DEBUG: HTTP_HOST = " . ($_SERVER['HTTP_HOST'] ?? 'NO HOST') . " -->\n";
echo "<!-- DEBUG: REQUEST_URI = " . ($_SERVER['REQUEST_URI'] ?? 'NO URI') . " -->\n";

header("Content-Type: text/html; charset=UTF-8");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio – DiamondPrueba</title>
  
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="/public/assets/css/styles.css?v=<?php echo time(); ?>">

</head>
<body>
  
  <div id="content-wrapper">
    
    <nav aria-label="Navegación principal">
      <ul>
        <li><a href="./html/islamujeres.php">Isla Mujeres</a></li>
        <li><a href="./html/snorkel.php">Snorkeling</a></li>
        <li><a href="./html/club.php">Club playa</a></li>
      </ul>
      
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
            <a href="./html/inicio-sesion.php" id="loginLink" role="button">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
            </a>
            <a href="./html/registro.php" id="registerLink" role="button">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
            </a>
          <?php else: ?>
            <div>
              <strong><?php echo $nombreUsuario ?: 'Usuario'; ?></strong><br>
              <small><?php echo $emailUsuario ?: ''; ?></small>
            </div>
            <a href="./dashboard.php"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi perfil</a>
            <a href="./mis-reservas.php"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mis reservas</a>
            <a href="./configuracion.php"><i class="fas fa-cog" aria-hidden="true"></i> Configuración</a>
            <a href="./logout.php" id="logoutLink"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
  
    <div class="notification" id="notification">
      <i class="fas fa-info-circle"></i>
      <span id="notificationText">Mensaje de notificación</span>
    </div>

    <section class="hero" role="banner">
      <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán">
        <source src="./public/assets/Imagenes/Catamaran.mp4" type="video/mp4">
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
            <a href="./html/inicio-sesion.php" class="redirect-btn">
              <i class="fas fa-sign-in-alt"></i> Iniciar sesión
            </a>
            <a href="./html/registro.php" class="redirect-btn">
              <i class="fas fa-user-plus"></i> Registrarse
            </a>
          </div>
        <?php else: ?>
          <div class="redirect-container">
            <a href="./dashboard.php" class="redirect-btn">
              <i class="fas fa-user-circle"></i> Ir a mi perfil
            </a>
            <a href="./html/Reserva.php" class="redirect-btn">
              <i class="fas fa-calendar-plus"></i> Hacer reserva
            </a>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="gallery-section" aria-labelledby="gallery-heading">
      <h2 id="gallery-heading" class="section-title">Maravíllate - Inspírate - Vive el mar</h2>
      <div class="gallery">
        <figure class="gallery-item">
          <img src="https://images.unsplash.com/photo-1519046904884-53103b34b206" 
               alt="Grupo disfrutando en el catamarán Diamond Bright">
          <figcaption class="img-caption">Grupo en el catamarán</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21" 
               alt="Muelle tropical en Cancún">
          <figcaption class="img-caption">Muelle tropical</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="https://images.unsplash.com/photo-1505118380757-91f5f5632de0" 
               alt="Vista al mar Caribe desde el catamarán">
          <figcaption class="img-caption">Vista al mar</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e" 
               alt="Experiencia de snorkel con Diamond Bright">
          <figcaption class="img-caption">Snorkel bajo el agua</figcaption>
        </figure>
      </div>
      
      <h2 class="section-title map-title" id="map-heading">
        <span class="first-line">Como llegar a</span>
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

    <section class="about" aria-labelledby="about-heading">
      <div class="about-image">
        <img src="./public/assets/Imagenes/Poster.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
      </div>
      <div class="about-text">
        <h2 id="about-heading">Acerca de nosotros</h2>
        <p>Vive la aventura en uno de nuestros lujosos catamaranes de 45 pies sobre el hermoso Mar Caribe disfrutando de una bebida de nuestro extenso menú de barra libre, donde nuestra increíble tripulación te hará pasar un gran momento. Haz Snorkel en el segundo arrecife de coral más grande del mundo con una biodiversidad incomparable, visita el encantador pueblo de Isla Mujeres catalogado como uno de los pueblos mágicos de México, también puedes nadar en una de las playas más hermosas del mundo, Playa Norte.</p>
        <p>Después de tanta diversión, disfruta del delicioso buffet que hemos preparado para ti, relájate en nuestro club de playa o disfruta de los diversos servicios que tiene, como sillones, columpios, camastros y hamacas. Para terminar el día, atrévete a realizar la divertida actividad del spinnaker bajo la puesta de sol de Cancún si las condiciones climáticas lo permiten.</p>
      </div>
    </section>

    <section class="promo" aria-labelledby="promo-heading">
      <div class="cta">
        <h2 id="promo-heading">Snorkel en el museo acuático</h2>
        <a href="./html/Reserva.php" class="btn-pill" id="saberMasBtn" role="button">
          <span class="btn-text">Saber más</span>
          <span class="btn-icon">➔</span>
        </a>
      </div>
      <div class="image">
        <img src="./public/assets/Imagenes/Hand.jpg" alt="Vela en Isla Mujeres con Diamond Bright">
      </div>
    </section>

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
          <div>Clima</div>
          <div id="current-time"><?php echo date('l H:i'); ?></div>
        </div>
      </div>
    </div>
    
    <?php 
    $footerPath = __DIR__ . '/includes/footer.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>
  </div>

  <script>
    console.log('Página cargada - CSS:', document.querySelector('link[href*="styles.css"]') ? 'Encontrado' : 'No encontrado');
    
    window.authData = {
        isAuthenticated: <?php echo $isAuthenticated ? 'true' : 'false'; ?>,
        userName: '<?php echo addslashes($nombreUsuario ?? ''); ?>',
        userEmail: '<?php echo addslashes($emailUsuario ?? ''); ?>'
    };

    document.addEventListener('DOMContentLoaded', function() {
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
    });
  </script>

<script src="public/assets/js/index.js?v=<?php echo time(); ?>"></script>
 
</body>

<?php

$cssUrl = '/css/styles.css';
$fullUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . 
           $_SERVER['HTTP_HOST'] . $cssUrl;
?>

<script>
console.log('CSS URL:', '<?php echo $cssUrl; ?>');
console.log('CSS Full URL:', '<?php echo $fullUrl; ?>');

fetch('<?php echo $cssUrl; ?>')
    .then(response => {
        console.log('CSS Status:', response.status);
        return response.text();
    })
    .then(text => console.log('CSS Length:', text.length))
    .catch(error => console.error('CSS Error:', error));
</script>
</html>