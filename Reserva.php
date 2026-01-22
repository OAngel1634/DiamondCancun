<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Guardar URL actual PARA REDIRIGIR DESPUÉS DEL LOGIN
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: inicio-sesion.php');
    exit;
}

require_once('conexion.php');

// Crear instancia de la base de datos y obtener conexión
$database = new Database();
$conn = $database->connect();

// Verificar la conexión a la base de datos
// Nota: PDO no tiene propiedad connect_error, usamos un método diferente
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reservas - Tours Isla Mujeres</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/reserva.css" />
</head>

<body>
    <header>
        <?php include('../includes/header.php'); ?>
    </header>

    <div class="container">
        <header class="hero-header">
            <h1>Reserva tu Tour a Isla Mujeres</h1>
            <p>Completa el formulario para reservar tu experiencia inolvidable. ¡Aprovecha nuestras promociones!</p>
        </header>

        <section class="carousel-container">
            <div class="carousel" id="carousel"></div>

            <div class="timer-bar">
                <div class="progress" id="progressBar"></div>
            </div>

            <div class="carousel-controls">
                <button class="carousel-btn prev" id="prevBtn" aria-label="Anterior">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn next" id="nextBtn" aria-label="Siguiente">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <div class="carousel-indicators" id="indicators"></div>
        </section>

        <div class="features">
            <div class="feature">
                <i class="fas fa-ship"></i>
                <h3>Transporte Incluido</h3>
                <p>Recogida en tu hotel de Cancún o Playa del Carmen</p>
            </div>
            <div class="feature">
                <i class="fas fa-utensils"></i>
                <h3>Comida y Bebidas</h3>
                <p>Buffet y barra libre durante el tour</p>
            </div>
            <div class="feature">
                <i class="fas fa-life-ring"></i>
                <h3>Equipo de Snorkel</h3>
                <p>Todo el equipo necesario para tu aventura</p>
            </div>
            <div class="feature">
                <i class="fas fa-camera"></i>
                <h3>Fotos Profesionales</h3>
                <p>Recuerdos de tu experiencia en alta calidad</p>
            </div>
        </div>

        <footer>
            <?php include('../includes/footer.php'); ?>
        </footer>
    </div>

    <!-- Plantilla oculta para duplicar -->
    <template id="promo-template">
        <div class="carousel-item">
            <div class="promo-tag"></div>
            <img class="promo-image" />
            <div class="promo-text">
                <p class="offer"></p>
                <p class="occasion"></p>
                <p class="discount"></p>
                <div class="rating">
                    <span class="rating-value"></span>
                    <div class="stars"></div>
                </div>
                <div class="price-container">
                    <span class="price"></span>
                    <span class="original-price"></span>
                    <p class="tax"></p>
                </div>
                <button class="book-btn">Reservar Ahora</button>
            </div>
        </div>
    </template>

    <script src="../Script/script.js"></script>
</body>
</html>