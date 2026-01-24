<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Isla Mujeres - Paraíso Caribeño | Tours y Experiencias</title>
  
  <!-- Estilos -->
  <link rel="stylesheet" href="../css/islamu.css">
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
        integrity="sha512-p1CmWvQg2cL0+9J1N0c9MvdSEZHt+6iweMn5LhI5UUl/FUWFuRFu8r9ZtOtjmCl8pq23THPCAAUeHz6D3Ym0hA==" 
        crossorigin="anonymous" 
        referrerpolicy="no-referrer" />
  <style>
    /* Correcciones para eliminar el espacio innecesario */
    .page-container {
      min-height: calc(100vh - var(--footer-height));
      padding-bottom: 0 !important;
    }
    
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    
    
    /* Ajustes para el contenido principal */
    .main-content {
      flex: 1;
    }
    
  </style>
</head>
<body>
  <div class="page-container">
    <header>
      <?php include('../includes/header.php'); ?>
    </header>

    <!-- Contenido principal -->
    <main class="main-content">
      <div class="sidebar">
        <div class="sidebar-content">
          <h2>Disfruta de:</h2>
          <h1>Isla Mujeres</h1>
          <p>Una joya en el Caribe mexicano, Isla Mujeres es famosa por sus playas de arena blanca, aguas
            turquesas y ambiente relajado. Con solo 7 km de largo, esta isla paradisíaca ofrece una experiencia
            única lejos del bullicio de la ciudad.</p>

          <div class="features">
            <div class="feature"><i class="fas fa-sun"></i> Clima tropical</div>
            <div class="feature"><i class="fas fa-water"></i> Snorkel con tortugas</div>
            <div class="feature"><i class="fas fa-utensils"></i> Gastronomía local</div>
            <div class="feature"><i class="fas fa-ship"></i> Paseos en catamarán</div>
          </div>
        </div>
        <a href="../index.php" class="home-link" aria-label="Volver a la página principal">
          <i class="fas fa-home"></i>
          Página principal
        </a>
      </div>

      <div class="btn-container">
        <a href="Reserva.php" class="cta-btn" aria-label="Reservar tour">
          <span class="icon"><i class="fas fa-calendar-check"></i></span>
          <span class="text">¡Reserva aquí!</span>
        </a>
      </div>
    </main>
  </div> <!-- Fin de .page-container -->
  
  <footer>
    <?php include('../includes/footer.php'); ?>
  </footer>
</body>
</html>