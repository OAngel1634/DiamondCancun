<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

$isProduction = ($_ENV['RAILWAY_ENVIRONMENT'] ?? $_ENV['NODE_ENV'] ?? 'development') === 'production';
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || 
           ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1' ||
           str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

if ($isProduction && !$isLocal && 
    (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') &&
    ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') !== 'https') {
    header("Location: https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

session_start([
    'cookie_path' => '/',
    'cookie_secure' => $isProduction,
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio – DiamondPrueba</title>
  
  <link rel="stylesheet" href="/css/styles.css">
  <style>
   
    nav[aria-label="Navegación principal"] {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background: rgba(10, 26, 47, 0.95);
      backdrop-filter: blur(10px);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }
    
    nav[aria-label="Navegación principal"] ul {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 30px;
    }
    
    nav[aria-label="Navegación principal"] ul li a {
      color: #f1faee;
      text-decoration: none;
      font-weight: 500;
      font-size: 1.1rem;
      padding: 8px 15px;
      border-radius: 6px;
      transition: all 0.3s ease;
      position: relative;
    }
    
    nav[aria-label="Navegación principal"] ul li a:hover {
      background: rgba(212, 175, 55, 0.15);
      color: #d4af37;
    }
    
    nav[aria-label="Navegación principal"] ul li a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 50%;
      width: 0;
      height: 2px;
      background: #d4af37;
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }
    
    nav[aria-label="Navegación principal"] ul li a:hover::after {
      width: 80%;
    }
    
    .user-icon-container {
      position: relative;
      margin-left: auto;
    }
    
    .user-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
      border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .user-icon i {
      font-size: 18px;
      color: #f1faee;
    }
    
    .user-icon:hover {
      background: rgba(212, 175, 55, 0.2);
      transform: scale(1.05);
    }
    
    .user-icon.authenticated {
      background: rgba(212, 175, 55, 0.3);
      color: #0a1a2f;
    }
    
    .user-icon.authenticated i {
      color: #0a1a2f;
    }
    
    .user-dropdown {
      position: absolute;
      top: 50px;
      right: 0;
      background: rgba(10, 26, 47, 0.98);
      border-radius: 8px;
      width: 250px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(212, 175, 55, 0.2);
      overflow: hidden;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
      backdrop-filter: blur(10px);
      z-index: 1001;
    }
    
    .user-dropdown.active {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    
    .user-dropdown a {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      color: #f1faee;
      text-decoration: none;
      gap: 12px;
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background: none;
      border: none;
      width: 100%;
      text-align: left;
      font-family: inherit;
      font-size: 1rem;
      cursor: pointer;
    }
    
    .user-dropdown a:hover {
      background: rgba(212, 175, 55, 0.15);
      padding-left: 25px;
    }
    
    .user-dropdown a i {
      width: 20px;
      text-align: center;
      color: #d4af37;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
   
    .redirect-btn {
      display: inline-block;
      padding: 12px 25px;
      background: #d4af37;
      color: #0a1a2f;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      text-align: center;
      margin: 10px;
      min-width: 180px;
    }
    
    .redirect-btn:hover {
      background: #c5a030;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }
    
    .redirect-container {
      display: flex;
      justify-content: center;
      margin-top: 30px;
      flex-wrap: wrap;
    }
    
    .hero {
      position: relative;
      height: 80vh;
      overflow: hidden;
    }
    
    .hero-video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .hero-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
      color: white;
      z-index: 1;
    }
    
    .section-title {
      text-align: center;
      margin: 2rem 0;
      color: #0a1a2f;
    }
    
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
      padding: 1rem;
    }
    
    .gallery-item img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }
    
   
    @media (max-width: 768px) {
      nav[aria-label="Navegación principal"] {
        flex-direction: column;
        padding: 15px;
        gap: 15px;
      }
      
      nav[aria-label="Navegación principal"] ul {
        flex-direction: column;
        align-items: center;
        gap: 15px;
        width: 100%;
      }
      
      nav[aria-label="Navegación principal"] ul li {
        width: 100%;
        text-align: center;
      }
      
      .user-icon-container {
        margin: 0;
      }
      
      .user-dropdown {
        position: fixed;
        top: auto;
        bottom: 0;
        right: 0;
        left: 0;
        width: 100%;
        border-radius: 16px 16px 0 0;
      }
      
      .redirect-container {
        flex-direction: column;
        align-items: center;
      }
      
      .redirect-btn {
        width: 100%;
        max-width: 300px;
      }
    }
  </style>
  
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-p1CmWvQg2cL0+9J1Nc9MvdSEZHt+6iweMn5LhI5UUl/FUWFuRFu8r9ZtOtjmCl8pq23THPCAAUeHz6D3Ym0hA==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />
</head>

<body>
  <div id="content-wrapper">
    
   
    <nav aria-label="Navegación principal">
      <ul>
        <li><a href="/html/islamujeres.php">Isla Mujeres</a></li>
        <li><a href="/html/snorkel.php">Snorkeling</a></li>
        <li><a href="/html/club.php">Club playa</a></li>
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
           
            <a href="/html/inicio-sesion.php" id="loginLink" role="menuitem">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
            </a>
            <a href="/html/registro.php" id="registerLink" role="menuitem">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
            </a>
          <?php else: ?>
           
            <div role="presentation" style="padding: 15px 20px; color: #f1faee; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <strong><?php echo $nombreUsuario ?: 'Usuario'; ?></strong><br>
              <small><?php echo $emailUsuario ?: ''; ?></small>
            </div>
            <a href="/dashboard.php" role="menuitem"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi perfil</a>
            <a href="/mis-reservas.php" role="menuitem"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mis reservas</a>
            <a href="/configuracion.php" role="menuitem"><i class="fas fa-cog" aria-hidden="true"></i> Configuración</a>
            <a href="/logout.php" id="logoutLink" role="menuitem"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <section class="hero" role="banner">
      <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán">
        <source src="/Imagenes/Catamaran.mp4" type="video/mp4">
        Tu navegador no soporta el elemento de video.
      </video>
      <div class="hero-content">
        <h1>
          Catamaran<br>
          <span class="title-line">Diamond Bright</span>
        </h1>
        <p class="subtitle">Somos más que un Tour</p>
        <p class="tagline">Somos una experiencia de por vida</p>
        
      
        <?php if ($isAuthenticated): ?>
          <div class="redirect-container">
            <a href="/dashboard.php" class="redirect-btn">
              <i class="fas fa-user-circle"></i> Ir a mi perfil
            </a>
            <a href="/html/Reserva.php" class="redirect-btn">
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
        <img src="/Imagenes/Poster.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
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
        <a href="/html/Reserva.php" class="btn-pill" id="saberMasBtn" role="button">
          <span class="btn-text">Saber más</span>
          <span class="btn-icon">➔</span>
        </a>
      </div>
      <div class="image">
        <img src="/Imagenes/Hand.jpg" alt="Vela en Isla Mujeres con Diamond Bright">
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

window.authData = {
    isAuthenticated: <?php echo $isAuthenticated ? 'true' : 'false'; ?>,
    userName: '<?php echo addslashes($nombreUsuario ?? ''); ?>',
    userEmail: '<?php echo addslashes($emailUsuario ?? ''); ?>'
};


document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userIcon && userDropdown) {
        // Toggle del dropdown
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = userIcon.getAttribute('aria-expanded') === 'true';
            userIcon.setAttribute('aria-expanded', !isExpanded);
            userDropdown.setAttribute('aria-hidden', isExpanded);
            userDropdown.classList.toggle('active', !isExpanded);
            
            
            if (!isExpanded) {
                const firstLink = userDropdown.querySelector('a, button');
                if (firstLink) firstLink.focus();
            }
        });
        
        document.addEventListener('click', function(e) {
            if (!userIcon.contains(e.target) && !userDropdown.contains(e.target)) {
                userIcon.setAttribute('aria-expanded', 'false');
                userDropdown.setAttribute('aria-hidden', 'true');
                userDropdown.classList.remove('active');
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && userIcon.getAttribute('aria-expanded') === 'true') {
                userIcon.setAttribute('aria-expanded', 'false');
                userDropdown.setAttribute('aria-hidden', 'true');
                userDropdown.classList.remove('active');
                userIcon.focus();
            }
        });
        
       
        const dropdownItems = userDropdown.querySelectorAll('a, button');
        dropdownItems.forEach((item, index) => {
            item.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = Math.min(index + 1, dropdownItems.length - 1);
                    dropdownItems[nextIndex].focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = Math.max(index - 1, 0);
                    dropdownItems[prevIndex].focus();
                }
            });
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

<script src="/Script/index.js" defer></script>
 
</body>
</html>