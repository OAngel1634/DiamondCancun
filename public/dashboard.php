<?php
declare(strict_types=1);
session_start();

// ---------------------------------------------------------------------
// 1. Protección de ruta: solo usuarios autenticados
// ---------------------------------------------------------------------
if (!isset($_SESSION['AUTH_USER'])) {
    header("Location: /../public/inicio-sesion.php");
    exit();
}

$user = $_SESSION['AUTH_USER'];
$rol = $user['rol'] ?? 'cliente';

// ---------------------------------------------------------------------
// 2. Función de escape para evitar XSS
// ---------------------------------------------------------------------
function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------------
// 3. Definir mensaje de bienvenida y algunas acciones según rol
// ---------------------------------------------------------------------
$panelTitle = 'Panel de ';
$acciones = [];

switch ($rol) {
    case 'super_admin':
        $panelTitle .= 'Super Administrador';
        $acciones = [
            'Gestionar usuarios' => '#',
            'Ver reportes' => '#',
            'Configuración del sistema' => '#'
        ];
        break;
    case 'admin':
        $panelTitle .= 'Administrador';
        $acciones = [
            'Gestionar reservas' => '#',
            'Ver reportes' => '#',
            'Gestionar clientes' => '#'
        ];
        break;
    case 'customer':
        $panelTitle .= 'Cliente';
        $acciones = [
            'Mis reservas' => '#',
            'Nueva reserva' => '#',
            'Mi perfil' => '#'
        ];
        break;
    case 'captain':
    case 'marine':
        $panelTitle .= 'Tripulación';
        $acciones = [
            'Horarios' => '#',
            'Partes de trabajo' => '#',
            'Embarcaciones' => '#'
        ];
        break;
    case 'agency_admin':
    case 'agency_agent':
    case 'informal_agent':
        $panelTitle .= 'Agencia';
        $acciones = [
            'Clientes de agencia' => '#',
            'Reservas' => '#',
            'Comisiones' => '#'
        ];
        break;
    default:
        $panelTitle .= 'Usuario';
        $acciones = ['Perfil' => '#'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Diamond Bright</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f5f7fb;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar simple */
        .sidebar {
            width: 260px;
            background: #1e293b;
            color: #f1f5f9;
            padding: 1.5rem 1rem;
        }
        .sidebar h2 {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #334155;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar li {
            margin-bottom: 0.5rem;
        }
        .sidebar a {
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            transition: background 0.2s;
        }
        .sidebar a:hover {
            background: #334155;
            color: #fff;
        }
        .user-info {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #334155;
            font-size: 0.875rem;
            color: #94a3b8;
        }
        .logout-btn {
            background: #ef4444;
            color: white !important;
            text-align: center;
            margin-top: 1rem;
        }
        .logout-btn:hover {
            background: #dc2626 !important;
        }
        /* Contenido principal */
        .main {
            flex: 1;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #0f172a;
        }
        .role-badge {
            background: #3b82f6;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .card h3 {
            margin-bottom: 1rem;
            color: #1e293b;
        }
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .action-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            transition: all 0.2s;
        }
        .action-card:hover {
            background: #e0e7ff;
            border-color: #3b82f6;
        }
        .action-card a {
            text-decoration: none;
            color: #1e293b;
            font-weight: 500;
        }
        .footer-note {
            margin-top: 2rem;
            font-size: 0.875rem;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>⚓ Diamond Bright</h2>
        <ul>
            <li><a href="/dashboard.php">🏠 Dashboard</a></li>
            <?php foreach ($acciones as $label => $url): ?>
                <li><a href="<?= e($url) ?>">📌 <?= e($label) ?></a></li>
            <?php endforeach; ?>
            <li><a href="/logout.php" class="logout-btn">🚪 Cerrar sesión</a></li>
        </ul>
        <div class="user-info">
            <div>👤 <?= e($user['nombre'] ?? explode('@', $user['email'])[0]) ?></div>
            <div>📧 <?= e($user['email']) ?></div>
            <div>🔐 <?= e($rol) ?></div>
        </div>
    </div>

    <div class="main">
        <div class="header">
            <h1><?= e($panelTitle) ?></h1>
            <span class="role-badge">Rol: <?= e($rol) ?></span>
        </div>

        <div class="card">
            <h3>Bienvenido, <?= e($user['nombre'] ?? 'Usuario') ?></h3>
            <p>Has iniciado sesión correctamente. Este es un dashboard de pruebas.</p>
            <p>ID de usuario: <code><?= e($user['id']) ?></code></p>
            <p>Última actividad: <?= date('d/m/Y H:i:s', $_SESSION['LAST_ACTIVITY'] ?? time()) ?></p>
        </div>

        <div class="card">
            <h3>Acciones rápidas (simuladas)</h3>
            <div class="action-grid">
                <?php foreach ($acciones as $label => $url): ?>
                    <div class="action-card">
                        <a href="<?= e($url) ?>"><?= e($label) ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <p class="footer-note">🔧 Los enlaces apuntan a "#" porque son de prueba. Puedes cambiarlos por rutas reales.</p>
        </div>
    </div>
</body>
</html>