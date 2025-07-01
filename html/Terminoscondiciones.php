<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Términos y Condiciones - Diamond Bright Catamarán</title>
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

    .main-nav a.active {
      color: var(--color-accent-alt);
      font-weight: bold;
    }

    .main-nav a.active::after {
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
    .header {
      text-align: center;
      padding: 120px 20px 60px;
      max-width: 900px;
      margin: 0 auto;
    }

    .header h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      font-weight: 800;
      background: linear-gradient(to right, var(--color-accent), var(--color-accent-alt));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      position: relative;
      display: inline-block;
    }

    .header h1::after {
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

    /* Contenido de términos */
    .terms-content {
      background: var(--color-overlay);
      backdrop-filter: blur(10px);
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--box-shadow);
      margin-bottom: 60px;
    }

    .terms-section {
      margin-bottom: 40px;
    }

    .terms-section h2 {
      font-size: 1.7rem;
      margin-bottom: 20px;
      color: var(--color-accent-alt);
      position: relative;
      padding-bottom: 10px;
    }

    .terms-section h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 3px;
      background: var(--color-accent);
      border-radius: 2px;
    }

    .terms-section h3 {
      font-size: 1.25rem;
      margin: 25px 0 15px;
      color: var(--color-accent);
    }

    .terms-section p {
      margin-bottom: 15px;
    }

    .terms-section ul {
      margin: 15px 0 25px 30px;
    }

    .terms-section ul li {
      margin-bottom: 10px;
      position: relative;
    }

    .terms-section ul li::before {
      content: '•';
      color: var(--color-accent);
      font-weight: bold;
      display: inline-block;
      width: 1em;
      margin-left: -1em;
    }

    .highlight {
      background: rgba(38, 208, 206, 0.1);
      border-left: 3px solid var(--color-accent);
      padding: 15px;
      border-radius: 0 var(--border-radius-small) var(--border-radius-small) 0;
      margin: 20px 0;
    }

    /* Botón de aceptación */
    .acceptance {
      text-align: center;
      margin: 40px 0;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .acceptance p {
      margin-bottom: 20px;
      font-weight: 500;
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
    }

    .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(146, 246, 209, 0.6);
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
      background: rgba(255, 255, 255, 0.1);
      padding: 12px 20px;
      border-radius: var(--border-radius-small);
      transition: var(--transition);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .contact-numbers li:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .contact-numbers select {
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

    .contact-buttons button {
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

    .contact-buttons button:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(146, 246, 209, 0.3);
    }

    .contact-buttons i {
      margin-right: 10px;
    }

    .social-buttons {
      display: flex;
      justify-content: center;
      gap: 25px;
      margin: 25px 0;
    }

    .social-button a {
      color: var(--color-accent);
      font-size: 1.8rem;
      text-decoration: none;
      transition: var(--transition);
    }

    .social-button a:hover {
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
      .header {
        padding: 100px 20px 40px;
      }
      
      .header h1 {
        font-size: 2rem;
      }
      
      .main-nav {
        position: fixed;
        top: 70px;
        left: 0;
        width: 100%;
        background: var(--color-overlay);
        backdrop-filter: blur(10px);
        clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
        transition: clip-path 0.4s ease;
      }
      
      .main-nav.active {
        clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
      }
      
      .main-nav ul {
        flex-direction: column;
        padding: 20px;
        gap: 10px;
      }
      
      .menu-toggle {
        display: block;
      }
      
      .terms-content {
        padding: 25px;
      }
      
      .terms-section h2 {
        font-size: 1.5rem;
      }
      
      .terms-section h3 {
        font-size: 1.15rem;
      }
      
      .contact-numbers {
        flex-direction: column;
        align-items: center;
      }
      
      .contact-buttons button {
        width: 100%;
        justify-content: center;
      }
    }

    @media (max-width: 480px) {
      .header h1 {
        font-size: 1.7rem;
      }
      
      .terms-content {
        padding: 20px 15px;
      }
      
      .btn {
        padding: 12px 35px;
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
    
    <nav class="main-nav" aria-label="Navegación principal">
      <ul>
        <li><a href="index.html">Inicio</a></li>
        <li><a href="islamujeres.html">Isla Mujeres</a></li>
        <li><a href="parqueacuatico.html">Museo Subacuático</a></li>
        <li><a href="terminos.html" class="active">Términos</a></li>
      </ul>
    </nav>
  </nav>

  <!-- Cabecera -->
  <header class="header">
    <h1>Términos y Condiciones</h1>
    <p>Conoce nuestras políticas para disfrutar de la mejor experiencia</p>
  </header>

  <main class="container">
    <section class="terms-content">
      <article class="terms-section">
        <h2>Introducción</h2>
        <p>Al acceder y utilizar los servicios de Diamond Bright Catamarán ("nosotros", "nuestro" o "la empresa"), usted acepta estar legalmente obligado por estos Términos y Condiciones. Si no está de acuerdo con alguna parte de estos términos, no debe utilizar nuestros servicios.</p>
        
        <div class="highlight">
          <p>Estos términos rigen su relación con Diamond Bright Catamarán y el uso de todos los servicios ofrecidos a través de nuestro sitio web y reservas.</p>
        </div>
      </article>

      <article class="terms-section">
        <h2>Reservas y Pagos</h2>
        
        <h3>Proceso de Reserva</h3>
        <p>Para reservar cualquier tour o servicio ofrecido por Diamond Bright Catamarán, debe:</p>
        <ul>
          <li>Ser mayor de 18 años o contar con autorización de un tutor legal</li>
          <li>Proporcionar información completa y precisa</li>
          <li>Aceptar estos términos y condiciones</li>
          <li>Realizar el pago correspondiente según las opciones disponibles</li>
        </ul>
        
        <h3>Política de Pagos</h3>
        <p>Se requiere un depósito del 30% para confirmar la reserva. El saldo restante debe pagarse al menos 48 horas antes de la fecha del tour.</p>
        <p>Aceptamos los siguientes métodos de pago: tarjetas de crédito/débito (Visa, MasterCard, American Express), transferencia bancaria y PayPal.</p>
        
        <h3>Confirmación</h3>
        <p>Recibirá una confirmación por correo electrónico una vez completada su reserva. Esta confirmación incluye todos los detalles de su tour y sirve como su boleto electrónico.</p>
      </article>

      <article class="terms-section">
        <h2>Política de Cancelación</h2>
        
        <h3>Cancelaciones por el Cliente</h3>
        <ul>
          <li><strong>Más de 7 días antes:</strong> Reembolso completo del depósito</li>
          <li><strong>Entre 3-7 días antes:</strong> 50% de reembolso del depósito</li>
          <li><strong>Menos de 48 horas antes:</strong> Sin reembolso</li>
        </ul>
        
        <h3>Cancelaciones por Diamond Bright</h3>
        <p>Nos reservamos el derecho de cancelar cualquier tour debido a condiciones climáticas adversas, problemas técnicos o circunstancias imprevistas. En tales casos:</p>
        <ul>
          <li>Ofreceremos una fecha alternativa</li>
          <li>O un reembolso completo del importe pagado</li>
        </ul>
        
        <h3>Cambios de Reserva</h3>
        <p>Los cambios de fecha están sujetos a disponibilidad y deben solicitarse con al menos 72 horas de antelación. Se aplicará una tarifa administrativa de $150 MXN por cambio.</p>
      </article>

      <article class="terms-section">
        <h2>Responsabilidades y Requisitos</h2>
        
        <h3>Requisitos de los Participantes</h3>
        <ul>
          <li>Todos los participantes deben saber nadar</li>
          <li>Los menores de 18 años deben estar acompañados por un adulto</li>
          <li>Debe informarnos de cualquier condición médica relevante</li>
          <li>Está prohibido el consumo de alcohol antes de actividades acuáticas</li>
        </ul>
        
        <h3>Responsabilidades del Cliente</h3>
        <p>Usted acepta:</p>
        <ul>
          <li>Seguir todas las instrucciones del personal</li>
          <li>Utilizar el equipo de seguridad proporcionado</li>
          <li>Respetar el medio ambiente marino</li>
          <li>Ser puntual en el punto de encuentro</li>
        </ul>
        
        <h3>Responsabilidades de Diamond Bright</h3>
        <p>Nos comprometemos a:</p>
        <ul>
          <li>Proporcionar equipo de seguridad adecuado</li>
          <li>Contar con personal capacitado y certificado</li>
          <li>Seguir todos los protocolos de seguridad</li>
          <li>Proporcionar servicios según lo descrito</li>
        </ul>
      </article>

      <article class="terms-section">
        <h2>Privacidad y Protección de Datos</h2>
        <p>Respetamos su privacidad y protegemos sus datos personales de acuerdo con nuestra Política de Privacidad. Los datos que recopilamos se utilizan únicamente para procesar su reserva y mejorar nuestros servicios.</p>
        
        <h3>Datos Recopilados</h3>
        <p>Recopilamos la siguiente información:</p>
        <ul>
          <li>Nombre completo</li>
          <li>Información de contacto (email, teléfono)</li>
          <li>Detalles de pago</li>
          <li>Preferencias de tour</li>
          <li>Información médica relevante (opcional)</li>
        </ul>
        
        <h3>Uso de Imágenes</h3>
        <p>Durante nuestros tours, podemos tomar fotografías o videos que podrían incluir participantes. Al aceptar estos términos, nos autoriza a utilizar estas imágenes con fines promocionales, a menos que nos indique lo contrario por escrito antes del tour.</p>
      </article>

      <article class="terms-section">
        <h2>Limitación de Responsabilidad</h2>
        <p>Diamond Bright Catamarán no será responsable por:</p>
        <ul>
          <li>Lesiones o accidentes resultantes del incumplimiento de las normas de seguridad</li>
          <li>Pérdida o daño de objetos personales</li>
          <li>Retrasos o cancelaciones debido a circunstancias fuera de nuestro control</li>
          <li>Cambios en itinerarios debido a condiciones climáticas o de mar</li>
        </ul>
        
        <div class="highlight">
          <p>Recomendamos encarecidamente contratar un seguro de viaje que cubra cancelaciones, problemas médicos y pérdida de equipaje.</p>
        </div>
      </article>

      <article class="terms-section">
        <h2>Propiedad Intelectual</h2>
        <p>Todos los contenidos de nuestro sitio web, incluyendo textos, gráficos, logotipos, imágenes y software, son propiedad de Diamond Bright Catamarán o de sus licenciantes y están protegidos por leyes de propiedad intelectual.</p>
        <p>Queda estrictamente prohibido:</p>
        <ul>
          <li>Reproducir, distribuir o modificar cualquier contenido sin autorización</li>
          <li>Utilizar nuestros contenidos con fines comerciales</li>
          <li>Utilizar tecnología para extraer datos de nuestro sitio</li>
        </ul>
      </article>

      <article class="terms-section">
        <h2>Modificaciones de los Términos</h2>
        <p>Nos reservamos el derecho de modificar estos Términos y Condiciones en cualquier momento. Las versiones actualizadas se publicarán en nuestro sitio web con la fecha de última actualización. El uso continuado de nuestros servicios después de dichos cambios constituirá su consentimiento a los mismos.</p>
      </article>

      <article class="terms-section">
        <h2>Legislación Aplicable</h2>
        <p>Estos Términos y Condiciones se regirán e interpretarán de acuerdo con las leyes de México. Cualquier disputa relacionada con estos términos estará sujeta a la jurisdicción exclusiva de los tribunales de Cancún, Quintana Roo.</p>
      </article>

      <div class="acceptance">
        <p>Al utilizar nuestros servicios, usted reconoce que ha leído, comprendido y aceptado estos Términos y Condiciones en su totalidad.</p>
        <button class="btn" onclick="window.history.back()">Volver al Sitio</button>
      </div>
    </section>
  </main>
  
  <!-- Footer -->
  <footer>
    <div class="customer-service">
      <h2>Servicio al cliente / Ventas</h2>
      <p>Lunes a Viernes de 7:30 a.m. a 7:30 p.m. / Sábado y Domingo: 7:30 a.m. a 6:00 p.m. / Horario Local.</p>
      
      <ul class="contact-numbers">
        <li>
          <i class="fas fa-phone"></i> México: 295-883-3142
        </li>
        <li>
          <i class="fas fa-phone"></i> USA / CAN: 1-855-295-2950
        </li>
        <li>
          <i class="fas fa-globe"></i> Otro país o región
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
          <a href="https://www.facebook.com/tu-pagina" aria-label="Facebook" target="_blank" rel="noopener">
            <i class="fab fa-facebook-f"></i>
          </a>
        </div>
        <div class="social-button">
          <a href="https://www.tiktok.com/@tu-cuenta" aria-label="TikTok" target="_blank" rel="noopener">
            <i class="fab fa-tiktok"></i>
          </a>
        </div>
        <div class="social-button">
          <a href="https://www.instagram.com/tu-cuenta" aria-label="Instagram" target="_blank" rel="noopener">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>
      
      <div class="copyright">
        <p>&copy; 2025 Catamaran Diamond Bright - Todos los derechos reservados</p>
      </div>
    </div>
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
      document.querySelector('.main-nav').classList.toggle('active');
    });
    
    // Cerrar menú al hacer clic en un enlace (móvil)
    document.querySelectorAll('.main-nav a').forEach(link => {
      link.addEventListener('click', function() {
        document.querySelector('.main-nav').classList.remove('active');
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
    
    // Inicializar
    handleScroll();
  </script>
</body>
</html>