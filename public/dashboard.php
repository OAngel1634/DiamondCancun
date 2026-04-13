<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth_functions.php';
require_once __DIR__ . '/app/Security/database.php';
session_start();

// Protección de ruta
if (!isset($_SESSION['AUTH_USER'])) {
    header("Location: /../public/inicio-sesion.php");
    exit();
}

$user = $_SESSION['AUTH_USER'];
$rol = $user['rol'];

// Mapeo de rol a archivo de panel
$panelMap = [
    'super_admin'    => 'superadmin.php',
    'admin'          => 'admin.php',
    'customer'       => 'customer.php',
    'captain'        => 'staff.php',
    'marine'         => 'staff.php',
    'agency_admin'   => 'agency.php',
    'agency_agent'   => 'agency.php',
    'informal_agent' => 'agency.php',
];

$panelFile = __DIR__ . '/panels/' . ($panelMap[$rol] ?? 'generic.php');
if (!file_exists($panelFile)) {
    $panelFile = __DIR__ . '/panels/generic.php';
}

// Título de la página según rol
$pageTitle = match ($rol) {
    'super_admin', 'admin' => 'Panel de Administración',
    'customer' => 'Mis Reservas',
    'captain', 'marine' => 'Panel de Tripulación',
    default => 'Dashboard'
};

// Datos para estadísticas (super_admin)
$stats = [];
if ($rol === 'super_admin') {
    try {
        $pdo = getConnection();
        $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM system_users")->fetchColumn();
        $stats['total_clients'] = $pdo->query("SELECT COUNT(*) FROM client")->fetchColumn();
        $stats['active_tours'] = $pdo->query("SELECT COUNT(*) FROM maritime_staff WHERE employed_status = 'active'")->fetchColumn();
        $stats['total_agencies'] = $pdo->query("SELECT COUNT(*) FROM agency")->fetchColumn();
    } catch (Exception $e) {
        $stats = ['error' => $e->getMessage()];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · Diamond Bright</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-layout">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
        
        <main class="dashboard-main">
            <?php include __DIR__ . '/includes/header.php'; ?>
            
            <div class="dashboard-content">
                <?php include $panelFile; ?>
            </div>
            
            <?php include __DIR__ . '/includes/footer.php'; ?>
        </main>
    </div>
    
    <script>
        // Toggle para menú colapsable (opcional)
        document.querySelectorAll('.sidebar-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelector('.sidebar').classList.toggle('collapsed');
            });
        });
    </script>
</body>
</html>