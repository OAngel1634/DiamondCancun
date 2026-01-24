<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Política de Privacidad - Diamond Bright Catamarán</title>
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
      line-height: 1.8;
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

    /* Contenedor principal */
    .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
    }

    /* Componentes reutilizables */
    .section-title {
      font-size: 1.8rem;
      margin-bottom: 20px;
      color: var(--color-accent-alt);
      position: relative;
      padding-bottom: 10px;
    }

    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 3px;
      background: var(--color-accent);
      border-radius: 2px;
    }

    .subsection-title {
      font-size: 1.3rem;
      margin: 25px 0 15px;
      color: var(--color-accent);
    }

    .highlight-box {
      background: rgba(38, 208, 206, 0.1);
      border-left: 3px solid var(--color-accent);
      padding: 15px;
      border-radius: 0 var(--border-radius-small) var(--border-radius-small) 0;
      margin: 20px 0;
    }

    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      background: rgba(0, 0, 0, 0.2);
      border-radius: var(--border-radius-small);
      overflow: hidden;
    }

    .data-table th, .data-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .data-table th {
      background: rgba(40, 193, 147, 0.2);
      color: var(--color-accent-alt);
      font-weight: 600;
    }

    .data-table tr:last-child td {
      border-bottom: none;
    }

    .data-table tr:hover {
      background: rgba(255, 255, 255, 0.05);
    }

    .btn {
      display: inline-block;
      background: linear-gradient(to right, var(--color-accent-alt), var(--color-accent));
      color: var(--color-dark);
      padding: 12px 35px;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 700;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: var(--transition);
      box-shadow: 0 5px 15px rgba(146, 246, 209, 0.4);
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(146, 246, 209, 0.6);
    }

    .btn-outline {
      background: transparent;
      border: 2px solid var(--color-accent-alt);
      color: var(--color-accent-alt);
      box-shadow: none;
    }

    .btn-outline:hover {
      background: rgba(146, 246, 209, 0.1);
    }

    .action-buttons {
      text-align: center;
      margin: 40px 0;
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    /* Navegación */
    nav {
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
    }

    .logo i {
      color: var(--color-secondary);
      font-size: 1.8rem;
    }

    .nav-menu {
      display: flex;
      list-style: none;
      gap: 20px;
    }

    .nav-link {
      color: var(--color-light);
      text-decoration: none;
      font-weight: 500;
      padding: 8px 12px;
      border-radius: var(--border-radius-small);
      transition: var(--transition);
      position: relative;
      font-size: 0.95rem;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--color-accent-alt);
      transition: var(--transition);
    }

    .nav-link:hover {
      color: var(--color-accent-alt);
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .nav-link.active {
      color: var(--color-accent-alt);
      font-weight: bold;
    }

    .nav-link.active::after {
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

    /* Cabecera */
    .page-header {
      text-align: center;
      padding: 120px 20px 60px;
      max-width: 900px;
      margin: 0 auto;
    }

    .page-header h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      font-weight: 800;
      background: linear-gradient(to right, var(--color-accent), var(--color-accent-alt));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
    }

    .page-header h1::after {
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

    /* Contenido principal */
    .content-container {
      background: var(--color-overlay);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--box-shadow);
      margin-bottom: 60px;
    }

    .content-section {
      margin-bottom: 40px;
    }

    .content-section p {
      margin-bottom: 15px;
    }

    .content-section ul {
      margin: 15px 0 25px 30px;
    }

    .content-section li {
      margin-bottom: 10px;
      position: relative;
    }

    .content-section li::before {
      content: '•';
      color: var(--color-accent);
      font-weight: bold;
      display: inline-block;
      width: 1em;
      margin-left: -1em;
    }

    /* Footer */
    footer {
      background: var(--color-overlay);
      padding: 40px 20px;
      text-align: center;
      border-radius: var(--border-radius);
      margin-top: 20px;
    }

    .customer-service {
      max-width: 800px;
      margin: 0 auto;
    }

    .customer-service h2 {
      font-size: 1.5rem;
      margin-bottom: 15px;
      color: var(--color-accent-alt);
    }

    .customer-service p {
      margin-bottom: 20px;
      opacity: 0.9;
    }

    .contact-list {
      list-style: none;
      padding: 0;
      margin: 25px 0;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
    }

    .contact-item {
      background: rgba(255, 255, 255, 0.1);
      padding: 12px 20px;
      border-radius: var(--border-radius-small);
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .contact-item:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .country-select {
      background-color: rgba(68, 68, 68, 0.7);
      color: var(--color-light);
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

    .contact-btn {
      background-color: var(--color-accent);
      color: var(--color-dark);
      border: none;
      border-radius: var(--border-radius-small);
      padding: 12px 25px;
      display: flex;
      align-items: center;
      font-weight: 600;
      cursor: pointer;
      transition: var(--transition);
    }

    .contact-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(146, 246, 209, 0.3);
    }

    .contact-btn i {
      margin-right: 10px;
    }

    .social-links {
      display: flex;
      justify-content: center;
      gap: 25px;
      margin: 25px 0;
    }

    .social-link {
      color: var(--color-accent);
      font-size: 1.8rem;
      text-decoration: none;
      transition: var(--transition);
    }

    .social-link:hover {
      color: var(--color-accent-alt);
      transform: translateY(-5px);
    }

    .copyright {
      margin-top: 30px;
      font-size: 0.9rem;
      opacity: 0.8;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .page-header {
        padding: 100px 20px 40px;
      }
      
      .page-header h1 {
        font-size: 2rem;
      }
      
      .nav-menu {
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
      
      .nav-menu.active {
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
      }
      
      .menu-toggle {
        display: block;
      }
      
      .content-container {
        padding: 25px;
      }
      
      .section-title {
        font-size: 1.5rem;
      }
      
      .subsection-title {
        font-size: 1.15rem;
      }
      
      .contact-list {
        flex-direction: column;
        align-items: center;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 15px;
      }
      
      .data-table {
        display: block;
        overflow-x: auto;
      }
    }

    @media (max-width: 480px) {
      .page-header h1 {
        font-size: 1.7rem;
      }
      
      .content-container {
        padding: 20px 15px;
      }
      
      .btn {
        padding: 12px 25px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Navegación -->
  <nav>
    <a href="index.html" class="logo" aria-label="Diamond Bright - Inicio">
      <i class="fas fa-umbrella-beach"></i>
      <span>Diamond Bright</span>
    </a>
    
    <button class="menu-toggle" aria-label="Menú">
      <i class="fas fa-bars"></i>
    </button>
    
    <ul class="nav-menu" aria-label="Navegación principal">
      <li><a href="index.html" class="nav-link">Inicio</a></li>
      <li><a href="islamujeres.html" class="nav-link">Isla Mujeres</a></li>
      <li><a href="parqueacuatico.html" class="nav-link">Museo Subacuático</a></li>
      <li><a href="Terminoscondiciones.html" class="nav-link">Términos</a></li>
      <li><a href="politica.html" class="nav-link active">Privacidad</a></li>
    </ul>
  </nav>

  <!-- Cabecera -->
  <header class="page-header">
    <h1>Política de Privacidad</h1>
    <p>Protegiendo tus datos personales con los más altos estándares de seguridad</p>
  </header>

  <main class="container">
    <article class="content-container">
      <section class="content-section">
        <h2 class="section-title">Introducción</h2>
        <p>En Diamond Bright Catamarán ("nosotros", "nuestro" o "la empresa"), respetamos su privacidad y nos comprometemos a proteger sus datos personales. Esta política explica cómo recopilamos, usamos, compartimos y protegemos su información cuando utiliza nuestros servicios o visita nuestro sitio web.</p>
        
        <div class="highlight-box">
          <p>Al utilizar nuestros servicios, usted acepta las prácticas descritas en esta Política de Privacidad. Si no está de acuerdo con estos términos, por favor no utilice nuestros servicios.</p>
        </div>
      </section>

      <section class="content-section">
        <h2 class="section-title">Información que Recopilamos</h2>
        
        <h3 class="subsection-title">Información que usted nos proporciona</h3>
        <p>Recopilamos información que usted nos proporciona directamente cuando:</p>
        <ul>
          <li>Realiza una reserva de tour o servicio</li>
          <li>Se suscribe a nuestro boletín informativo</li>
          <li>Participa en encuestas o promociones</li>
          <li>Contacta con nuestro servicio de atención al cliente</li>
          <li>Interactúa con nosotros a través de redes sociales</li>
        </ul>
        
        <h3 class="subsection-title">Tipos de datos personales recopilados</h3>
        <p>Podemos recopilar los siguientes tipos de información:</p>
        <table class="data-table">
          <thead>
            <tr>
              <th>Categoría de datos</th>
              <th>Ejemplos</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Datos de identificación</td>
              <td>Nombre completo, fecha de nacimiento, nacionalidad, documento de identidad</td>
            </tr>
            <tr>
              <td>Datos de contacto</td>
              <td>Dirección de correo electrónico, número de teléfono, dirección postal</td>
            </tr>
            <tr>
              <td>Datos de reserva</td>
              <td>Detalles de reservas, preferencias de tour, historial de servicios</td>
            </tr>
            <tr>
              <td>Datos de pago</td>
              <td>Información de tarjeta de crédito/débito, detalles de facturación</td>
            </tr>
            <tr>
              <td>Datos técnicos</td>
              <td>Dirección IP, tipo de navegador, ubicación aproximada, datos de cookies</td>
            </tr>
            <tr>
              <td>Datos de salud</td>
              <td>Información médica relevante para la seguridad durante los tours</td>
            </tr>
          </tbody>
        </table>
      </section>

      <section class="content-section">
        <h2 class="section-title">Cómo Utilizamos su Información</h2>
        
        <p>Utilizamos su información personal para los siguientes fines:</p>
        <ul>
          <li>Procesar sus reservas y pagos</li>
          <li>Gestionar su cuenta y relación con nosotros</li>
          <li>Personalizar su experiencia y ofrecer servicios relevantes</li>
          <li>Comunicarnos con usted sobre reservas, servicios y promociones</li>
          <li>Mejorar nuestros servicios, sitio web y experiencia del cliente</li>
          <li>Cumplir con obligaciones legales y regulatorias</li>
        </ul>
        
        <div class="highlight-box">
          <p>Nunca vendemos ni alquilamos su información personal a terceros con fines comerciales. Solo compartimos datos con proveedores de servicios esenciales para la prestación de nuestros servicios.</p>
        </div>
      </section>

      <section class="content-section">
        <h2 class="section-title">Protección de Datos</h2>
        
        <h3 class="subsection-title">Medidas de seguridad</h3>
        <p>Implementamos medidas técnicas y organizativas para proteger sus datos personales:</p>
        <ul>
          <li>Encriptación SSL en todas las transacciones</li>
          <li>Almacenamiento seguro de datos en servidores protegidos</li>
          <li>Acceso restringido a datos personales</li>
          <li>Actualizaciones periódicas de sistemas de seguridad</li>
          <li>Protocolos de respuesta ante incidentes de seguridad</li>
        </ul>
        
        <h3 class="subsection-title">Conservación de datos</h3>
        <p>Conservamos sus datos personales solo durante el tiempo necesario para los fines para los que fueron recopilados, o según lo requiera la ley aplicable.</p>
      </section>

      <section class="content-section">
        <h2 class="section-title">Sus Derechos de Privacidad</h2>
        <p>Dependiendo de su ubicación y de las leyes aplicables, usted puede tener los siguientes derechos sobre sus datos personales:</p>
        
        <h3 class="subsection-title">Derecho de acceso</h3>
        <p>Solicitar copias de sus datos personales que poseemos.</p>
        
        <h3 class="subsection-title">Derecho de rectificación</h3>
        <p>Solicitar la corrección de datos incompletos o inexactos.</p>
        
        <h3 class="subsection-title">Derecho de eliminación</h3>
        <p>Solicitar la eliminación de sus datos personales en determinadas circunstancias.</p>
        
        <h3 class="subsection-title">Derecho a la portabilidad</h3>
        <p>Recibir sus datos en un formato estructurado y comúnmente utilizado.</p>
        
        <div class="action-buttons">
          <button class="btn" onclick="downloadData()">
            <i class="fas fa-download"></i> Solicitar mis datos
          </button>
          <button class="btn btn-outline" onclick="updatePreferences()">
            <i class="fas fa-cog"></i> Actualizar preferencias
          </button>
        </div>
      </section>
    </article>
  </main>
  
  <!-- Footer -->
  <footer>
    <section class="customer-service">
      <h2>Servicio al cliente / Ventas</h2>
      <p>Lunes a Viernes de 7:30 a.m. a 7:30 p.m. / Sábado y Domingo: 7:30 a.m. a 6:00 p.m. / Horario Local.</p>
      
      <ul class="contact-list" aria-label="Números de contacto">
        <li class="contact-item">
          <i class="fas fa-phone"></i> México: 295-883-3142
        </li>
        <li class="contact-item">
          <i class="fas fa-phone"></i> USA / CAN: 1-855-295-2950
        </li>
        <li class="contact-item">
          <i class="fas fa-globe"></i> Otro país o región
          <select class="country-select" aria-label="Seleccionar país">
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
        <button class="contact-btn" aria-label="Contactar por WhatsApp">
          <i class="fab fa-whatsapp"></i> WhatsApp
        </button>
        <button class="contact-btn" aria-label="Chatear en la Web">
          <i class="fas fa-comments"></i> Chatea en la Web
        </button>
        <button class="contact-btn" aria-label="Contactar por Messenger">
          <i class="fab fa-facebook-messenger"></i> Messenger
        </button>
      </div>
      
      <div class="social-links">
        <a href="https://www.facebook.com/tu-pagina" class="social-link" aria-label="Facebook" target="_blank" rel="noopener">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="https://www.tiktok.com/@tu-cuenta" class="social-link" aria-label="TikTok" target="_blank" rel="noopener">
          <i class="fab fa-tiktok"></i>
        </a>
        <a href="https://www.instagram.com/tu-cuenta" class="social-link" aria-label="Instagram" target="_blank" rel="noopener">
          <i class="fab fa-instagram"></i>
        </a>
      </div>
      
      <p class="copyright">&copy; 2025 Catamaran Diamond Bright - Todos los derechos reservados</p>
    </section>
  </footer>

  <script>
    // Cambiar color de fondo del nav al hacer scroll
    let lastScrollY = window.scrollY;
    
    function handleScroll() {
      const nav = document.querySelector('nav');
      if (window.scrollY > 50) {
        nav.style.background = 'rgba(10, 30, 60, 0.95)';
      } else {
        nav.style.background = 'rgba(0, 0, 0, 0.4)';
      }
      
      lastScrollY = window.scrollY;
    }
    
    // Menú móvil
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.nav-menu').classList.toggle('active');
    });
    
    // Cerrar menú al hacer clic en un enlace (móvil)
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function() {
        document.querySelector('.nav-menu').classList.remove('active');
      });
    });
    
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
    
    // Funciones para los botones de acción
    function downloadData() {
      // Simulación de solicitud de datos
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
      
      content.innerHTML = `
        <h3 style="color: var(--color-accent-alt); margin-bottom: 20px;">Solicitud de Datos Personales</h3>
        <p style="margin-bottom: 25px;">Hemos enviado un enlace de verificación a su correo electrónico. Por favor revise su bandeja de entrada y siga las instrucciones para descargar sus datos personales.</p>
        <button class="btn" style="margin-top: 15px;" onclick="this.parentElement.parentElement.remove()">Entendido</button>
      `;
      
      modal.appendChild(content);
      document.body.appendChild(modal);
    }
    
    function updatePreferences() {
      // Simulación de actualización de preferencias
      alert('Será redirigido a su panel de preferencias donde podrá actualizar sus opciones de comunicación y privacidad.');
    }
    
    // Inicializar
    handleScroll();
  </script>
</body>
</html>