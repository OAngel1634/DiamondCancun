<?php
session_start();

$isAuthenticated = false;
$nombreUsuario = '';
$emailUsuario = '';


if (isset($_SESSION['usuario_id'])) {
    $isAuthenticated = true;
    
    
    if (isset($_SESSION['usuario_nombre'])) {
        $nombreUsuario = $_SESSION['usuario_nombre'];
    }
    
    if (isset($_SESSION['usuario_email'])) {
        $emailUsuario = $_SESSION['usuario_email'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio – DiamondPrueba</title>
  
  <link rel="stylesheet" href="../css/styles.css">
  <style>
   
    .login-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.85);
      z-index: 10000;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background: linear-gradient(135deg, #0a1a2f, #1a3a5f);
      padding: 35px;
      border-radius: 12px;
      width: 100%;
      max-width: 450px;
      position: relative;
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(212, 175, 55, 0.3);
    }

    .close-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      font-size: 28px;
      cursor: pointer;
      color: #f1faee;
      background: transparent;
      border: none;
      transition: all 0.3s ease;
    }

    .close-btn:hover {
      color: #d4af37;
      transform: scale(1.1);
    }

    .form-group {
      margin-bottom: 25px;
      position: relative;
    }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
      color: #f1faee;
      font-size: 1.1rem;
    }

    .form-group input {
      width: 100%;
      padding: 14px 15px;
      border-radius: 8px;
      border: 1px solid #457b9d;
      background: rgba(255, 255, 255, 0.1);
      color: #f1faee;
      font-size: 1.05rem;
      transition: all 0.3s ease;
    }

    .form-group input:focus {
      border-color: #d4af37;
      outline: none;
      box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .password-container {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #a8dadc;
      cursor: pointer;
      font-size: 1.2rem;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: #d4af37;
    }

    .btn-login {
      width: 100%;
      padding: 15px;
      background: #d4af37;
      color: #0a1a2f;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.1rem;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }

    .btn-login:hover {
      background: #c5a030;
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    .register-link {
      text-align: center;
      margin-top: 25px;
      color: #a8dadc;
      font-size: 1rem;
    }

    .register-link a {
      color: #d4af37;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .register-link a:hover {
      text-decoration: underline;
    }

    /* Notificaciones */
    .notification {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 25px;
      border-radius: 8px;
      color: white;
      z-index: 10000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      transform: translateX(120%);
      transition: transform 0.3s ease-in-out;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .notification.success {
      background: #28a745;
    }

    .notification.error {
      background: #dc3545;
    }

    .notification.info {
      background: #17a2b8;
    }

    .notification.active {
      transform: translateX(0);
    }
    
    /* Icono de usuario mejorado */
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
    
    .user-icon:hover {
      background: rgba(212, 175, 55, 0.2);
      transform: scale(1.05);
    }
    
    .user-icon.authenticated {
      background: rgba(212, 175, 55, 0.3);
      color: #0a1a2f;
    }
    
    .user-dropdown {
      position: absolute;
      top: 50px;
      right: 0;
      background: rgba(10, 26, 47, 0.95);
      border-radius: 8px;
      width: 250px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      border: 1px solid rgba(212, 175, 55, 0.2);
      overflow: hidden;
      display: none;
      z-index: 1000;
      backdrop-filter: blur(10px);
    }
    
    .user-dropdown.active {
      display: block;
      animation: fadeIn 0.3s ease;
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
    
    /* Botones de redirección */
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
  </style>
  
  
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-p1CmWvQg2cL0+9J1N0c9MvdSEZHt+6iweMn5LhI5UUl/FUWFuRFu8r9ZtOtjmCl8pq23THPCAAUeHz6D3Ym0hA==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />
</head>

<body>
  <div id="content-wrapper">
    
    <nav aria-label="Navegación principal">
      <ul>
        <li><a href="../html/islamujeres.php">Isla Mujeres</a></li>
        <li><a href="../html/snorkel.php">Snorkeling</a></li>
        <li><a href="../html/club.php">Club playa</a></li>
      </ul>
      
      <div class="user-icon-container" aria-label="Acceso de usuario">
        <div class="user-icon" id="userIcon" tabindex="0" role="button" aria-expanded="false">
          <i class="fas fa-user" aria-hidden="true"></i>
        </div>
        <div class="user-dropdown" id="userDropdown" aria-hidden="true">
          <div id="guestLinks">
            <a href="../html/inicio-sesion.php" id="loginLink" role="button">
              <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar sesión
            </a>
            <a href="../html/registro.php" id="registerLink" role="button">
              <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
            </a>
          </div>
          <div id="userLinks" style="display:none;">
            <a href="dashboard.php"><i class="fas fa-user-circle" aria-hidden="true"></i> Mi perfil</a>
            <a href="reservations.html"><i class="fas fa-calendar-check" aria-hidden="true"></i> Mis reservas</a>
            <a href="settings.html"><i class="fas fa-cog" aria-hidden="true"></i> Configuración</a>
            <a href="logout.php" id="logoutLink"><i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar sesión</a>
          </div>
        </div>
      </div>
    </nav>

    <div class="notification" id="notification">
      <i class="fas fa-info-circle"></i>
      <span id="notificationText">Mensaje de notificación</span>
    </div>

    <section class="hero" role="banner">
      <video class="hero-video" autoplay muted loop playsinline aria-label="Video de catamarán">
        <source src="../Imagenes/Catamaran.mp4" type="video/mp4">
        Tu navegador no soporta el elemento de video.
      </video>
      <div class="hero-content">
        <h1>
          Catamaran<br>
          <span class="title-line">Diamond Bright</span>
        </h1>
        <p class="subtitle">Somos más que un Tour</p>
        <p class="tagline">Somos una experiencia de por vida</p>
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
        <img src="../Imagenes/Poster.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
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
       <a href="../html/Reserva.php" class="btn-pill" id="saberMasBtn" role="button">
  <span class="btn-text">Saber más</span>
  <span class="btn-icon">➔</span>
</a>
      </div>
      <div class="image">
        <img src="../Imagenes/Hand.jpg" alt="Vela en Isla Mujeres con Diamond Bright">
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
          <div>martes 10:00 a.m.</div>
        </div>
      </div>
    </div>
    
    <?php include('../includes/footer.php'); ?>
  </div>

<script>
window.authData = {
    isAuthenticated: <?php echo $isAuthenticated ? 'true' : 'false'; ?>,
    userName: '<?php echo $nombreUsuario ?? ''; ?>',
    userEmail: '<?php echo $emailUsuario ?? ''; ?>'
};
</script>
 <script src="../Script/index.js"></script>
 
</body>
</html>