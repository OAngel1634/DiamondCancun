<?php
declare(strict_types=1);

$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);

define('URL_BASE', '');

$cssUrl    = URL_BASE . 'assets/css/styles.css';
$jsUrl     = URL_BASE . 'assets/js/index.js';
$imagesUrl = URL_BASE . 'assets/imagenes/'; 
$assetBase = URL_BASE . 'assets/';         

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true
]);

$isAuthenticated = isset($_SESSION['usuario_id']);
$nombreUsuario   = $_SESSION['usuario_nombre'] ?? '';
$emailUsuario    = $_SESSION['usuario_email'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio – DiamondPrueba</title>
  
  
  <meta name="asset-base" content="<?php echo htmlspecialchars($assetBase); ?>">
  <meta name="images-url" content="<?php echo htmlspecialchars($imagesUrl); ?>">
  
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <link rel="stylesheet" href="<?php echo $cssUrl; ?>?v=<?php echo time(); ?>">

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
            <a href="./inicio-sesion.php" id="loginLink" role="button">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
            </a>
            <a href="./registro.php" id="registerLink" role="button">
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
  
   
    <section class="hero" role="banner">
      <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán">
       
        <source src="<?php echo $imagesUrl; ?>Catamaran.mp4" type="video/mp4">
        <source src="public/assets/imagenes/Catamaran.mp4" type="video/mp4">
      </video>
      <div class="hero-content">
        <h1>
          Catamaran<br>
          <span class="title-line">Diamond Bright</span>
        </h1>
        <p class="subtitle">Somos más que un Tour</p>
        <p class="tagline">Somos una experiencia de por vida</p>
        
        
    </section>

    <section class="gallery-section" aria-labelledby="gallery-heading">
      <h2 id="gallery-heading" class="section-title">Maravíllate - Inspírate - Vive el mar</h2>
      <div class="gallery">
        <figure class="gallery-item">
          <img src="<?php echo $imagesUrl; ?>grupo-catamaran.jpg" 
               alt="Grupo disfrutando en el catamarán Diamond Bright"
               onerror="this.src='https://images.unsplash.com/photo-1519046904884-53103b34b206'">
          <figcaption class="img-caption">Grupo en el catamarán</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="<?php echo $imagesUrl; ?>muelle-tropical.jpg" 
               alt="Muelle tropical en Cancún"
               onerror="this.src='https://images.unsplash.com/photo-1506929562872-bb421503ef21'">
          <figcaption class="img-caption">Muelle tropical</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="<?php echo $imagesUrl; ?>vista-mar.jpg" 
               alt="Vista al mar Caribe desde el catamarán"
               onerror="this.src='https://images.unsplash.com/photo-1505118380757-91f5f5632de0'">
          <figcaption class="img-caption">Vista al mar</figcaption>
        </figure>
        <figure class="gallery-item">
          <img src="<?php echo $imagesUrl; ?>snorkel-experiencia.jpg" 
               alt="Experiencia de snorkel con Diamond Bright"
               onerror="this.src='https://images.unsplash.com/photo-1530541930197-ff16ac917b0e'">
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
        <img src="<?php echo $imagesUrl; ?>Poster.jpg" 
             alt="Tour en Isla Mujeres con Diamond Bright">
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
        <img src="<?php echo $imagesUrl; ?>Hand.jpg" 
             alt="Vela en Isla Mujeres con Diamond Bright"
             onerror="console.error('Error cargando Hand.jpg')">
      </div>
    </section>
    
    <?php 
    $footerPath = __DIR__ . '/../includes/footer.php';
    if (file_exists($footerPath)) {
        require_once $footerPath;
    } 
    ?>
  </div>
 
  <script src="<?php echo $jsUrl; ?>?v=<?php echo time(); ?>"></script>
 
</body>
</html>