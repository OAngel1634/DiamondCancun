<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $tour_id = $_POST['tour_id'];
    $fecha_tour = $_POST['fecha_tour'];
    $hora_tour = $_POST['hora_tour'];
    $numero_adultos = $_POST['numero_adultos'];
    $numero_ninos = $_POST['numero_ninos'];
    $comentarios = $_POST['comentarios'];
    
    // Obtener precio base del tour
    $tour_query = $conn->prepare("SELECT precio_base FROM tours WHERE id = ?");
    $tour_query->bind_param("i", $tour_id);
    $tour_query->execute();
    $tour_result = $tour_query->get_result();
    $tour_data = $tour_result->fetch_assoc();
    
    if ($tour_data) {
        $precio_base = $tour_data['precio_base'];
        $precio_total = ($numero_adultos * $precio_base) + ($numero_ninos * ($precio_base * 0.5));
        
        // Insertar reserva en la base de datos
        $stmt = $conn->prepare("INSERT INTO reservas (
            usuario_id, 
            tour_id, 
            fecha_tour, 
            hora_tour, 
            numero_adultos, 
            numero_ninos, 
            precio_total, 
            comentarios
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "isssiids",
            $usuario_id,
            $tour_id,
            $fecha_tour,
            $hora_tour,
            $numero_adultos,
            $numero_ninos,
            $precio_total,
            $comentarios
        );
        
        if ($stmt->execute()) {
            $reserva_id = $stmt->insert_id;
            header("Location: confirmacion_reserva.php?id=".$reserva_id);
            exit();
        } else {
            $error = "Error al procesar la reserva: ".$conn->error;
        }
    } else {
        $error = "Tour seleccionado no válido";
    }
}

include('../includes/header.php');
?>

<div class="container">
    <?php if (isset($error)): ?>
        <div class="error-message">
            <p><?= $error ?></p>
            <a href="reservas.php" class="btn">Volver a intentar</a>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>