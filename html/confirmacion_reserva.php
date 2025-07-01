<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$reserva_id = $_GET['id'];
$usuario_id = $_SESSION['usuario_id'];

// Obtener detalles de la reserva
$query = $conn->prepare("
    SELECT r.*, t.nombre AS tour_nombre
    FROM reservas r
    JOIN tours t ON r.tour_id = t.id
    WHERE r.id = ? AND r.usuario_id = ?
");
$query->bind_param("ii", $reserva_id, $usuario_id);
$query->execute();
$reserva = $query->get_result()->fetch_assoc();

include('../includes/header.php');
?>

<div class="container">
    <?php if ($reserva): ?>
        <div class="confirmation-card">
            <div class="confirmation-header">
                <i class="fas fa-check-circle"></i>
                <h2>¡Reserva Confirmada!</h2>
                <p>Tu reserva #<?= $reserva['id'] ?> ha sido procesada exitosamente</p>
            </div>
            
            <div class="confirmation-details">
                <div class="detail-item">
                    <span>Tour:</span>
                    <span><?= $reserva['tour_nombre'] ?></span>
                </div>
                <div class="detail-item">
                    <span>Fecha:</span>
                    <span><?= date('d/m/Y', strtotime($reserva['fecha_tour'])) ?></span>
                </div>
                <div class="detail-item">
                    <span>Hora:</span>
                    <span><?= date('h:i A', strtotime($reserva['hora_tour'])) ?></span>
                </div>
                <div class="detail-item">
                    <span>Personas:</span>
                    <span><?= $reserva['numero_adultos'] ?> adultos, <?= $reserva['numero_ninos'] ?> niños</span>
                </div>
                <div class="detail-item total">
                    <span>Total pagado:</span>
                    <span>$<?= number_format($reserva['precio_total'], 2) ?></span>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <a href="tours.php" class="btn">Ver más tours</a>
                <a href="reservas.php" class="btn primary">Nueva reserva</a>
            </div>
        </div>
    <?php else: ?>
        <div class="error-message">
            <p>No se encontró la reserva solicitada</p>
            <a href="reservas.php" class="btn">Volver a reservas</a>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>