<?php
session_start();

// Verificar sesión usando el sistema unificado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

require_once 'Usuario.php';
require_once 'conexion.php';

// Validar usuario real
$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

$database = new Database();
$conn = $database->connect();  // Asumiendo que tu clase Database devuelve conexión MySQLi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $tour_id = $_POST['tour_id'] ?? 0;
    $fecha_tour = $_POST['fecha_tour'] ?? '';
    $hora_tour = $_POST['hora_tour'] ?? '';
    $numero_adultos = $_POST['numero_adultos'] ?? 0;
    $numero_ninos = $_POST['numero_ninos'] ?? 0;
    $comentarios = $_POST['comentarios'] ?? '';
    
    // Validaciones básicas
    $errores = [];
    if (empty($tour_id) || !is_numeric($tour_id)) $errores[] = "Tour inválido";
    if (empty($fecha_tour)) $errores[] = "Fecha requerida";
    if (empty($hora_tour)) $errores[] = "Hora requerida";
    if ($numero_adultos < 1) $errores[] = "Debe haber al menos 1 adulto";
    
    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: reservas.php");
        exit();
    }
    
    try {
        // Obtener precio base del tour con validación
        $tour_query = $conn->prepare("SELECT precio_base FROM tours WHERE id = ?");
        $tour_query->bind_param("i", $tour_id);
        $tour_query->execute();
        $tour_result = $tour_query->get_result();
        
        if ($tour_result->num_rows === 0) {
            throw new Exception("Tour seleccionado no válido");
        }
        
        $tour_data = $tour_result->fetch_assoc();
        $precio_base = $tour_data['precio_base'];
        $precio_total = ($numero_adultos * $precio_base) + ($numero_ninos * ($precio_base * 0.5));
        
        // Insertar reserva con transacción
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("INSERT INTO reservas (
            usuario_id, 
            tour_id, 
            fecha_tour, 
            hora_tour, 
            numero_adultos, 
            numero_ninos, 
            precio_total, 
            comentarios,
            estado
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')");
        
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
        
        if (!$stmt->execute()) {
            throw new Exception("Error al procesar la reserva: ".$conn->error);
        }
        
        $reserva_id = $conn->insert_id;
        $conn->commit();
        
        header("Location: confirmacion_reserva.php?id=".$reserva_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

include('../includes/header.php');
?>

<div class="container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <p><?= htmlspecialchars($error) ?></p>
            <a href="reservas.php" class="btn btn-primary">Volver a intentar</a>
        </div>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>