<?php
session_start();
require_once 'Sesion.php';
require_once 'conexion.php';

// Verificar sesión y permisos...

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $reservaId = (int)$_POST['id'];
    
    // Actualizar estado a 'cancelada'
    $database = new Database();
    $pdo = $database->connect();
    
    try {
        $stmt = $pdo->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$reservaId, $_SESSION['usuario_id']]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reserva no encontrada o no autorizada']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error de base de datos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
?>