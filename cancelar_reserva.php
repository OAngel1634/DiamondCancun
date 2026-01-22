<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

require_once 'Usuario.php';
require_once 'conexion.php';

// Obtener usuario
$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

// Conectar a base de datos
$database = new Database();
$pdo = $database->connect();

// Verificar si es una solicitud POST para cancelar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserva_id'])) {
    $reservaId = $_POST['reserva_id'];
    
    // Validar que la reserva pertenece al usuario
    $stmt = $pdo->prepare("SELECT id FROM reservas WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$reservaId, $usuario->id]);
    $reserva = $stmt->fetch();
    
    if (!$reserva) {
        echo json_encode([
            'success' => false, 
            'message' => 'Reserva no encontrada o no autorizada'
        ]);
        exit();
    }
    
    try {
        // Actualizar estado a 'cancelada'
        $stmt = $pdo->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = ?");
        $stmt->execute([$reservaId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No se pudo actualizar la reserva'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error de base de datos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Solicitud inválida'
    ]);
}