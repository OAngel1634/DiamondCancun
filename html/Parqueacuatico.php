<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Museo Subacuático de Arte (MUSA) - Diamond Bright</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Variables CSS */
    :root {
      --color-primary: #1a2980;
      --color-secondary: #26d0ce;
      --color-accent: #28C193;
      --color-accent-alt: #92F6D1;
      --color-light: #ffffff;
      --color-dark: #003366;
      --color-text: #f8f8f8;
      --color-overlay: rgba(0, 30, 60, 0.7);
      --border-radius: 20px;
      --border-radius-small: 15px;
      --box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
      --box-shadow-light: 0 5px 15px rgba(0, 0, 0, 0.2);
      --transition: all 0.3s ease;
    }

    /* Reset y estilos base */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(rgba(10, 30, 60, 0.85), rgba(15, 40, 80, 0.9));
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: var(--color-text);
      line-height: 1.6;
      min-height: 100vh;
      position: relative;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 10% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 20%),
                  radial-gradient(circle at 90% 80%, rgba(38, 208, 206, 0.1) 0%, transparent 20%);
      z-index: -1;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Navegación */
    .main-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      background: var(--color-overlay);
      backdrop-filter: blur(5px);
      transition: var(--transition);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      font-size: 1.3rem;
      color: var(--color-light);
      text-decoration: none;
      transition: var(--transition);
    }

    .logo:hover {
      transform: scale(1.05);
      color: var(--color-accent-alt);
    }

    .logo i {
      color: var(--color-secondary);
      font-size: 1.8rem;
    }

    .main-nav ul {
      display: flex;
      list-style: none;
      gap: 20px;
    }

    .main-nav a {
      color: var(--color-light);
      text-decoration: none;
      font-weight: 500;
      padding: 8px 12px;
      border-radius: var(--border-radius-small);
      transition: var(--transition);
      position: relative;
      font-size: 0.95rem;
    }

    .main-nav a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--color-accent-alt);
      transition: var(--transition);
    }

    .main-nav a:hover {
      color: var(--color-accent-alt);
    }

    .main-nav a:hover::after {
      width: 100%;
    }

    .menu-toggle {
      display: none;
      background: none;
      border: none;
      color: var(--color-accent-alt);
      font-size: 1.5rem;
      cursor: pointer;
    }

    /* Hero Section */
    .hero {
      position: relative;
      height: 85vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      margin-top: 70px;
      overflow: hidden;
      isolation: isolate;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to top, rgba(10, 30, 60, 0.9), transparent 60%);
      z-index: 1;
    }

    .hero-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 900px;
      padding: 20px;
    }

    .hero h1 {
      font-size: 3rem;
      margin-bottom: 20px;
      font-weight: 800;
      background: linear-gradient(to right, var(--color-accent), var(--color-accent-alt));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    .hero h1::after {
      content: '';
      position: absolute;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      width: 150px;
      height: 4px;
      background: linear-gradient(to right, var(--color-accent-alt), var(--color-accent));
      border-radius: 2px;
    }

    .hero p {
      font-size: 1.2rem;
      max-width: 700px;
      margin: 0 auto 30px;
      line-height: 1.8;
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    }

    .btn {
      display: inline-block;
      background: linear-gradient(to right, var(--color-accent-alt), var(--color-accent));
      color: var(--color-dark);
      padding: 15px 45px;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 700;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 5px 20px rgba(146, 246, 209, 0.4);
      margin-top: 20px;
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right, var(--color-accent), var(--color-accent-alt));
      opacity: 0;
      transition: var(--transition);
      z-index: -1;
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(146, 246, 209, 0.6);
    }

    .btn:hover::before {
      opacity: 1;
    }

    /* Secciones */
    section {
      padding: 60px 0;
    }

    .section-title {
      text-align: center;
      margin-bottom: 40px;
      position: relative;
    }

    .section-title h2 {
      font-size: 2.3rem;
      margin-bottom: 10px;
      background: linear-gradient(to right, var(--color-accent), var(--color-accent-alt));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .section-title p {
      font-size: 1.2rem;
      opacity: 0.9;
    }

    .section-title::after {
      content: '';
      display: block;
      width: 100px;
      height: 4px;
      background: linear-gradient(to right, var(--color-accent-alt), var(--color-accent));
      margin: 15px auto;
      border-radius: 2px;
    }

    /* Tarjetas de información */
    .info-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin-bottom: 50px;
    }

    .card {
      background: var(--color-overlay);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      padding: 30px;
      box-shadow: var(--box-shadow-light);
      border: 1px solid rgba(255, 255, 255, 0.1);
      transition: var(--transition);
      display: flex;
      flex-direction: column;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: var(--box-shadow);
      border-color: var(--color-accent);
    }

    .card i {
      font-size: 2.5rem;
      color: var(--color-accent);
      margin-bottom: 20px;
      align-self: center;
    }

    .card h3 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: var(--color-accent-alt);
      text-align: center;
    }

    .card p {
      margin-bottom: 15px;
      flex-grow: 1;
    }

    .card ul {
      padding-left: 20px;
      margin-bottom: 20px;
    }

    .card ul li {
      margin-bottom: 8px;
      position: relative;
      padding-left: 15px;
    }

    .card ul li::before {
      content: '•';
      color: var(--color-accent);
      position: absolute;
      left: 0;
    }

    /* Galería */
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 60px;
    }

    .gallery-item {
      border-radius: var(--border-radius);
      overflow: hidden;
      position: relative;
      height: 300px;
      box-shadow: var(--box-shadow-light);
      transition: var(--transition);
    }

    .gallery-item:hover {
      transform: scale(1.03);
      box-shadow: var(--box-shadow);
    }

    .gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .gallery-item:hover img {
      transform: scale(1.1);
    }

    .gallery-caption {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
      padding: 15px;
      font-size: 1.1rem;
      text-align: center;
      opacity: 0;
      transition: var(--transition);
    }

    .gallery-item:hover .gallery-caption {
      opacity: 1;
    }

    /* Mapa */
    .map-container {
      position: relative;
      overflow: hidden;
      padding-top: 56.25%;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      margin: 40px 0;
      border: 3px solid var(--color-accent);
    }

    .map-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Formulario de reservas */
    .reservation-form {
      background: var(--color-overlay);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--box-shadow);
      max-width: 700px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px 15px;
      border-radius: var(--border-radius-small);
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(0, 0, 0, 0.3);
      color: var(--color-light);
      font-size: 1rem;
      transition: var(--transition);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--color-accent);
      outline: none;
      background: rgba(0, 0, 0, 0.4);
    }


    /* Responsive */
    @media (max-width: 768px) {
      .hero {
        height: 70vh;
      }
      
      .hero h1 {
        font-size: 2.3rem;
      }
      
      .main-nav ul {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        background: var(--color-overlay);
        backdrop-filter: blur(10px);
        clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
        transition: clip-path 0.4s ease;
        flex-direction: column;
        padding: 20px;
        gap: 10px;
        text-align: center;
      }
      
      .main-nav ul.active {
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
      }
      
      .menu-toggle {
        display: block;
      }
      
      .section-title h2 {
        font-size: 1.9rem;
      }
      
      .reservation-form {
        padding: 25px;
      }
    }

    @media (max-width: 480px) {
      .hero {
        height: 60vh;
        margin-top: 60px;
      }
      
      .hero h1 {
        font-size: 1.9rem;
      }
      
      .hero p {
        font-size: 1rem;
      }
      
      .btn {
        padding: 12px 30px;
        font-size: 1rem;
      }
      
      .gallery {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
      <?php include('../includes/header.php'); ?>
    </header>

  <!-- Hero Section -->
  <section class="hero">
    <video class="hero-video" autoplay muted loop playsinline>
      <source src="https://player.vimeo.com/external/572275180.sd.mp4?s=ec0e1f2b0a1c7f2a0f7c4a3d6f4b4e4a1a5d5d6e&profile_id=164" type="video/mp4">
      <!-- Fallback image -->
      <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Museo Subacuático">
    </video>
    <div class="hero-content">
      <h1>Museo Subacuático de Arte (MUSA)</h1>
      <p>Sumérgete en el mundo submarino más extraordinario de Isla Mujeres. Más de 500 esculturas submarinas crean un arrecife artificial único que combina arte y conservación marina.</p>
      <a href="#reservar" class="btn">Reservar Tour</a>
    </div>
  </section>

  <div class="container">
    <!-- Información sobre MUSA -->
    <section>
      <div class="section-title">
        <h2>El Museo Subacuático</h2>
        <p>Una experiencia única en el mundo</p>
      </div>
      
      <div class="info-cards">
        <div class="card">
          <i class="fas fa-water"></i>
          <h3>¿Qué es MUSA?</h3>
          <p>El Museo Subacuático de Arte (MUSA) es un proyecto de conservación que combina arte y ecología. Con más de 500 esculturas sumergidas a diferentes profundidades, se ha convertido en uno de los destinos de buceo y snorkel más fascinantes del mundo.</p>
          <p>Creado por el artista Jason deCaires Taylor, las esculturas están diseñadas para promover el crecimiento de corales y la vida marina, creando un arrecife artificial único.</p>
        </div>
        
        <div class="card">
          <i class="fas fa-fish"></i>
          <h3>Vida Marina</h3>
          <p>Las esculturas han sido colonizadas por una sorprendente variedad de vida marina:</p>
          <ul>
            <li>Más de 2,000 ejemplares de coral juvenil</li>
            <li>36 especies de peces tropicales</li>
            <li>Diversas especies de crustáceos y moluscos</li>
            <li>Tortugas marinas y mantarrayas</li>
          </ul>
          <p>El museo ayuda a reducir la presión sobre los arrecifes naturales, permitiendo su recuperación.</p>
        </div>
        
        <div class="card">
          <i class="fas fa-map-marked-alt"></i>
          <h3>Ubicación y Acceso</h3>
          <p>El museo se encuentra entre las costas de Cancún e Isla Mujeres, en el Parque Nacional Costa Occidental de Isla Mujeres, Punta Cancún y Punta Nizuc.</p>
          <p>Existen tres formas de visitar el museo:</p>
          <ul>
            <li>Buceo con tanque</li>
            <li>Snorkeling</li>
            <li>Embarcaciones con fondo de cristal</li>
          </ul>
        </div>
      </div>
    </section>
    
    <!-- Galería -->
    <section>
      <div class="section-title">
        <h2>Galería Submarina</h2>
        <p>Descubre las increíbles esculturas bajo el mar</p>
      </div>
      
      <div class="gallery">
        <div class="gallery-item">
          <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Escultura 'La Evolución Silenciosa'">
          <div class="gallery-caption">La Evolución Silenciosa - Más de 400 figuras humanas</div>
        </div>
        
        <div class="gallery-item">
          <img src="https://images.unsplash.com/photo-1579546929662-711aa81148cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="El Hombre en Llamas">
          <div class="gallery-caption">El Hombre en Llamas - Cubierto de corales de fuego</div>
        </div>
        
        <div class="gallery-item">
          <img src="https://images.unsplash.com/photo-1530541930197-ff16ac917b0e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="El Coleccionista de Sueños">
          <div class="gallery-caption">El Coleccionista de Sueños - Cientos de mensajes en botellas</div>
        </div>
        
        <div class="gallery-item">
          <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Jardín de la Esperanza">
          <div class="gallery-caption">Jardín de la Esperanza - Niños observando el futuro del océano</div>
        </div>
      </div>
    </section>
    
    <!-- Mapa -->
    <section>
      <div class="section-title">
        <h2>Ubicación del Museo</h2>
        <p>Encuentra cómo llegar a este increíble lugar</p>
      </div>
      
      <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1464.8265978125373!2d-86.72703098748265!3d21.19925273172303!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f4c259e979267b7%3A0x184811e5d56afbc5!2sMuseo%20Subacu%C3%A1tico%20de%20Arte!5e0!3m2!1ses-419!2smx!4v1750825032802!5m2!1ses-419!2smx" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div>
    </section>
    
    <!-- Reservas -->
    <section id="reservar">
      <div class="section-title">
        <h2>Reserva tu Tour</h2>
        <p>Vive la experiencia MUSA con Diamond Bright</p>
      </div>
      
      <div class="reservation-form">
        <form id="reservationForm">
          <div class="form-group">
            <label for="name">Nombre completo</label>
            <input type="text" id="name" required>
          </div>
          
          <div class="form-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" required>
          </div>
          
          <div class="form-group">
            <label for="phone">Teléfono</label>
            <input type="tel" id="phone" required>
          </div>
          
          <div class="form-group">
            <label for="date">Fecha del tour</label>
            <input type="date" id="date" required>
          </div>
          
          <div class="form-group">
            <label for="tour">Tipo de tour</label>
            <select id="tour" required>
              <option value="">Selecciona una opción</option>
              <option value="snorkel">Tour de Snorkel (2 horas) - $40 USD</option>
              <option value="buceo">Tour de Buceo (3 horas) - $75 USD</option>
              <option value="premium">Tour Premium (5 horas con comida) - $120 USD</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="participants">Número de participantes</label>
            <input type="number" id="participants" min="1" max="10" required>
          </div>
          
          <div class="form-group">
            <label for="message">Mensaje adicional</label>
            <textarea id="message" rows="4"></textarea>
          </div>
          
          <button type="submit" class="btn">Reservar Ahora</button>
        </form>
      </div>
    </section>
  </div>
  
  <!-- Footer -->
 <footer>
    <?php include('../includes/footer.php'); ?>
  </footer>

  <script>
    // Menú móvil
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.main-nav ul').classList.toggle('active');
    });

    // Cerrar menú al hacer clic en un enlace (móvil)
    document.querySelectorAll('.main-nav a').forEach(link => {
      link.addEventListener('click', function() {
        document.querySelector('.main-nav ul').classList.remove('active');
      });
    });

    // Cambiar color de fondo del nav al hacer scroll
    let lastScrollY = window.scrollY;
    
    function handleScroll() {
      const nav = document.querySelector('.main-header');
      if (window.scrollY > 50) {
        nav.style.background = 'rgba(10, 30, 60, 0.95)';
      } else {
        nav.style.background = 'rgba(0, 0, 0, 0.4)';
      }
      
      lastScrollY = window.scrollY;
    }
    
    // Optimización: Throttling para el evento scroll
    let isThrottled = false;
    const throttleDelay = 100;
    
    window.addEventListener('scroll', function() {
      if (!isThrottled) {
        handleScroll();
        isThrottled = true;
        setTimeout(() => {
          isThrottled = false;
        }, throttleDelay);
      }
    });
    
    // Formulario de reserva
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Simulación de reserva exitosa
      const modal = document.createElement('div');
      modal.style.position = 'fixed';
      modal.style.top = '0';
      modal.style.left = '0';
      modal.style.width = '100%';
      modal.style.height = '100%';
      modal.style.backgroundColor = 'rgba(0,0,0,0.7)';
      modal.style.display = 'flex';
      modal.style.justifyContent = 'center';
      modal.style.alignItems = 'center';
      modal.style.zIndex = '2000';
      
      const content = document.createElement('div');
      content.style.backgroundColor = 'rgba(10,30,60,0.95)';
      content.style.padding = '30px';
      content.style.borderRadius = 'var(--border-radius)';
      content.style.textAlign = 'center';
      content.style.maxWidth = '500px';
      content.style.width = '90%';
      content.style.boxShadow = '0 15px 50px rgba(0,0,0,0.5)';
      content.style.border = '2px solid var(--color-accent)';
      
      content.innerHTML = `
        <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--color-accent-alt); margin-bottom: 20px;"></i>
        <h3 style="color: var(--color-accent-alt); margin-bottom: 20px;">¡Reserva Confirmada!</h3>
        <p style="margin-bottom: 25px;">Hemos enviado los detalles de tu reserva al correo electrónico proporcionado. ¡Prepárate para vivir una experiencia inolvidable en el MUSA!</p>
        <button class="btn" style="margin-top: 15px;" onclick="this.parentElement.parentElement.remove()">Continuar</button>
      `;
      
      modal.appendChild(content);
      document.body.appendChild(modal);
      
      // Resetear formulario
      this.reset();
    });

    // Inicializar
    handleScroll();
    
    // Configurar la fecha mínima en el formulario (hoy)
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date').min = today;
  </script>
</body>
</html>