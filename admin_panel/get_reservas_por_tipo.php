<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$days = $_GET['days'] ?? 30;

$database = new AdminDB();
$conn = $database->connect();

try {
    $fechaInicio = date('Y-m-d', strtotime("-$days days"));
    
    $stmt = $conn->prepare("
        SELECT t.tipo, COUNT(r.id) as cantidad
        FROM reservas r
        JOIN tours t ON r.tour_id = t.id
        WHERE r.fecha_reserva >= :fecha_inicio
        GROUP BY t.tipo
    ");
    $stmt->bindParam(':fecha_inicio', $fechaInicio);
    $stmt->execute();
    
    header('Content-Type: application/json');
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
?>