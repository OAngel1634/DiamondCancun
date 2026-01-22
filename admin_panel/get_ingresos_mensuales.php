<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$months = (int)($_GET['months'] ?? 6);

$database = new AdminDB();
$conn = $database->connect();

try {
    $resultados = [];
    
    for ($i = $months - 1; $i >= 0; $i--) {
        $mes = date('Y-m', strtotime("-$i months"));
        $stmt = $conn->prepare("
            SELECT COALESCE(SUM(monto), 0) as total 
            FROM pagos 
            WHERE estado = 'completado' 
            AND DATE_FORMAT(fecha, '%Y-%m') = ?
        ");
        $stmt->execute([$mes]);
        $total = $stmt->fetchColumn();
        
        $resultados[] = [
            'mes' => $mes,
            'total' => $total
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($resultados);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
?>