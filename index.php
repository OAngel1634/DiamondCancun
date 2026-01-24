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
<html lang="es" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Catamarán Diamond Bright - Experiencias únicas en Isla Mujeres, Cancún">
  <meta name="keywords" content="catamarán, snorkel, Isla Mujeres, tour, Cancún, Diamond Bright">
  <meta name="author" content="Diamond Bright">
  <title>Inicio – DiamondPrueba</title>
  
  <link rel="preload" href="/css/styles.css" as="style">
  <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
  +
  <link rel="stylesheet" href="/css/styles.css">
  
  <style>
    
    :root {
      --primary-gold: #d4af37;
      --dark-blue: #0a1a2f;
      --light-bg: #f8f9fa;
      --white: #ffffff;
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    
    .user-icon-container {
      position: relative;
      z-index: 1000;
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
      transition: var(--transition);
      position: relative;
      border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .user-icon:hover,
    .user-icon:focus-visible {
      background: rgba(212, 175, 55, 0.2);
      transform: scale(1.05);
      outline: 2px solid var(--primary-gold);
    }
    
    .user-icon.authenticated {
      background: rgba(212, 175, 55, 0.3);
      color: var(--dark-blue);
    }
    
    .user-dropdown {
      position: absolute;
      top: calc(100% + 10px);
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
    
    .user-dropdown a,
    .user-dropdown button {
      display: flex;
      align-items: center;
      padding: 15px 20px;
      color: #f1faee;
      text-decoration: none;
      gap: 12px;
      transition: var(--transition);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      background: none;
      border: none;
      width: 100%;
      text-align: left;
      font-family: inherit;
      font-size: 1rem;
      cursor: pointer;
    }
    
    .user-dropdown a:hover,
    .user-dropdown button:hover {
      background: rgba(212, 175, 55, 0.15);
      padding-left: 25px;
    }
    
    .user-dropdown a i,
    .user-dropdown button i {
      width: 20px;
      text-align: center;
      color: var(--primary-gold);
    }
    
    .redirect-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 28px;
      background: var(--primary-gold);
      color: var(--dark-blue);
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      font-size: 1rem;
      transition: var(--transition);
      border: 2px solid transparent;
      cursor: pointer;
      min-width: 180px;
      text-align: center;
    }
    
    .redirect-btn:hover,
    .redirect-btn:focus-visible {
      background: #c5a030;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
      border-color: var(--primary-gold);
    }
    
    .redirect-container {
      display: flex;
      justify-content: center;
      margin-top: 30px;
      flex-wrap: wrap;
      gap: 15px;
    }
    
    /* Optimización de imágenes */
    .gallery-item img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      loading: lazy;
    }
    
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }
    
    @media (max-width: 768px) {
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
 
  <a href="#main-content" class="sr-only">Saltar al contenido principal</a>
  
  <div id="content-wrapper">
    <nav aria-label="Navegación principal">
      <ul>
        <li><a href="/html/islamujeres.php">Isla Mujeres</a></li>
        <li><a href="/html/snorkel.php">Snorkeling</a></li>
        <li><a href="/html/club.php">Club playa</a></li>
      </ul>
      
      <div class="user-icon-container" aria-label="Menú de usuario">
        <button class="user-icon <?php echo $isAuthenticated ? 'authenticated' : ''; ?>" 
                id="userIcon" 
                aria-expanded="false"
                aria-controls="userDropdown"
                aria-label="Abrir menú de usuario">
          <i class="fas fa-user" aria-hidden="true"></i>
        </button>
        <div class="user-dropdown" id="userDropdown" role="menu" aria-label="Opciones de usuario">
          <?php if (!$isAuthenticated): ?>
            <a href="/html/inicio-sesion.php" role="menuitem">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
            </a>
            <a href="/html/registro.php" role="menuitem">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
            </a>
          <?php else: ?>
            <div role="presentation" style="padding: 15px 20px; color: #f1faee; border-bottom: 1px solid rgba(255,255,255,0.1);">
              <strong><?php echo $nombreUsuario ?: 'Usuario'; ?></strong>
              <br>
              <small><?php echo $emailUsuario ?: ''; ?></small>
            </div>
            <a href="/dashboard.php" role="menuitem"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi perfil</a>
            <a href="/mis-reservas.php" role="menuitem"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mis reservas</a>
            <a href="/configuracion.php" role="menuitem"><i class="fas fa-cog" aria-hidden="true"></i> Configuración</a>
            <form action="/logout.php" method="POST" style="margin: 0;">
              <button type="submit" role="menuitem" style="padding: 15px 20px;">
                <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión
              </button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <main id="main-content">
      <section class="hero" role="banner">
        <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán Diamond Bright">
          <source src="/Imagenes/Catamaran.mp4" type="video/mp4">
          <source src="/Imagenes/Catamaran.webm" type="video/webm">
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
              <a href="/html/inicio-sesion.php" class="redirect-btn">
                <i class="fas fa-sign-in-alt"></i> Iniciar sesión
              </a>
              <a href="/html/registro.php" class="redirect-btn">
                <i class="fas fa-user-plus"></i> Registrarse
              </a>
            </div>
          <?php else: ?>
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
            <img src="/Imagenes/catamaran-grupo.jpg" 
                 alt="Grupo disfrutando en el catamarán Diamond Bright"
                 width="400" height="200">
            <figcaption class="img-caption">Grupo en el catamarán</figcaption>
          </figure>
          <figure class="gallery-item">
            <img src="/Imagenes/muelle.jpg" 
                 alt="Muelle tropical en Cancún"
                 width="400" height="200">
            <figcaption class="img-caption">Muelle tropical</figcaption>
          </figure>
          <figure class="gallery-item">
            <img src="/Imagenes/vista-mar.jpg" 
                 alt="Vista al mar Caribe desde el catamarán"
                 width="400" height="200">
            <figcaption class="img-caption">Vista al mar</figcaption>
          </figure>
          <figure class="gallery-item">
            <img src="/Imagenes/snorkel.jpg" 
                 alt="Experiencia de snorkel con Diamond Bright"
                 width="400" height="200">
            <figcaption class="img-caption">Snorkel bajo el agua</figcaption>
          </figure>
        </div>
        
        <h2 class="section-title map-title" id="map-heading">
          <span class="first-line">Cómo llegar a</span>
          <span class="second-line">Nuestra sucursal</span>
        </h2>
        
        <div class="map-container" aria-labelledby="map-heading">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3720.8924849176187!2d-86.80201992473988!3d21.156676580524767!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f4c293e862f2c95%3A0xc03a2d75f3d562e2!2sDiamond%20Bright%20Catamaran%20Canc%C3%BAn!5e0!3m2!1ses-419!2smx!4v1750223700133!5m2!1ses-419!2smx" 
                  allowfullscreen 
                  loading="lazy" 
                  referrerpolicy="no-referrer-when-downgrade" 
                  title="Ubicación de Diamond Bright Catamarán en Cancún"
                  aria-label="Mapa interactivo de la ubicación"></iframe>
        </div>
      </section>

      <section class="about" aria-labelledby="about-heading">
        <div class="about-image">
          <img src="/Imagenes/Poster.jpg" 
               alt="Tour en Isla Mujeres con Diamond Bright" 
               loading="lazy"
               width="600" height="400">
        </div>
        <div class="about-text">
          <h2 id="about-heading">Acerca de nosotros</h2>
          <p>Vive la aventura en uno de nuestros lujosos catamaranes de 45 pies sobre el hermoso Mar Caribe disfrutando de una bebida de nuestro extenso menú de barra libre, donde nuestra increíble tripulación te hará pasar un gran momento.</p>
          <p>Haz Snorkel en el segundo arrecife de coral más grande del mundo con una biodiversidad incomparable, visita el encantador pueblo de Isla Mujeres catalogado como uno de los pueblos mágicos de México.</p>
        </div>
      </section>

      <section class="promo" aria-labelledby="promo-heading">
        <div class="cta">
          <h2 id="promo-heading">Snorkel en el museo acuático</h2>
          <a href="/html/Reserva.php" class="btn-pill" role="button" aria-label="Más información sobre Snorkel">
            <span class="btn-text">Saber más</span>
            <span class="btn-icon" aria-hidden="true">➔</span>
          </a>
        </div>
        <div class="image">
          <img src="/Imagenes/Hand.jpg" 
               alt="Vela en Isla Mujeres con Diamond Bright" 
               loading="lazy"
               width="600" height="400">
        </div>
      </section>

      <section class="weather" aria-label="Información del clima">
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
      </section>
    </main>
    
    <?php 
    
    $footerPath = __DIR__ . '/includes/footer.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>
  </div>

  <script>
    
    const authData = {
        isAuthenticated: <?php echo $isAuthenticated ? 'true' : 'false'; ?>,
        userName: <?php echo json_encode($nombreUsuario, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
        userEmail: <?php echo json_encode($emailUsuario, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
    };

    // Dropdown del usuario con mejor accesibilidad
    document.addEventListener('DOMContentLoaded', () => {
        const userIcon = document.getElementById('userIcon');
        const userDropdown = document.getElementById('userDropdown');
        
        if (userIcon && userDropdown) {
            // Toggle dropdown
            const toggleDropdown = (show) => {
                const isExpanded = show ?? (userIcon.getAttribute('aria-expanded') === 'false');
                userIcon.setAttribute('aria-expanded', isExpanded);
                userDropdown.classList.toggle('active', isExpanded);
                
                if (isExpanded) {
                    userDropdown.focus();
                }
            };
            
            userIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleDropdown();
            });
            
            userIcon.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleDropdown();
                } else if (e.key === 'Escape' && userIcon.getAttribute('aria-expanded') === 'true') {
                    toggleDropdown(false);
                }
            });
            
            document.addEventListener('click', (e) => {
                if (!userIcon.contains(e.target) && !userDropdown.contains(e.target)) {
                    toggleDropdown(false);
                }
            });
            
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && userIcon.getAttribute('aria-expanded') === 'true') {
                    toggleDropdown(false);
                    userIcon.focus();
                }
            });
            
            const dropdownItems = userDropdown.querySelectorAll('a, button');
            dropdownItems.forEach((item, index) => {
                item.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        dropdownItems[Math.min(index + 1, dropdownItems.length - 1)].focus();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        dropdownItems[Math.max(index - 1, 0)].focus();
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