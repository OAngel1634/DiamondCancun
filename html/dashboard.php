<?php
session_start();

// Verificar sesión PHP
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

// Obtener información del usuario
require_once 'Usuario.php';
require_once 'conexion.php';

$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

// Conectar a la base de datos
$database = new Database();
$pdo = $database->connect();

// Obtener estadísticas del usuario
$reservasActivas = 0;
$valoracionPromedio = 0;
$viajesRealizados = 0;
$cuponesDisponibles = 0; // Inicializado en 0

// Consulta para reservas activas
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                      WHERE usuario_id = ? AND estado IN ('pendiente', 'confirmada')");
$stmt->execute([$_SESSION['usuario_id']]);
$reservasActivas = $stmt->fetchColumn();

// Consulta para valoración promedio
$stmt = $pdo->prepare("SELECT AVG(calificacion) FROM opiniones WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$valoracionPromedio = $stmt->fetchColumn();
$valoracionPromedio = $valoracionPromedio ? number_format($valoracionPromedio, 1) : '0.0';

// Consulta para viajes realizados
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                      WHERE usuario_id = ? AND estado = 'completada'");
$stmt->execute([$_SESSION['usuario_id']]);
$viajesRealizados = $stmt->fetchColumn();

// Consulta para cupones disponibles (asumiendo que existe tabla 'cupones')
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cupones 
                          WHERE usuario_id = ? AND usado = 0");
    $stmt->execute([$_SESSION['usuario_id']]);
    $cuponesDisponibles = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Error en cupones: " . $e->getMessage());
    $cuponesDisponibles = 0;
}

// Obtener datos del usuario
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: #003366;
            --secondary: #0099cc;
            --accent: #D4AF37;
            --light-bg: #f0f8ff;
            --white: #ffffff;
            --light-gray: #e0e0e0;
            --text-dark: #333333;
            --text-light: #666666;
            --shadow: 0 8px 20px rgba(0, 51, 102, 0.15);
            --transition: all 0.3s ease;
            --border-radius: 10px;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0,153,204,0.1) 100%);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
            flex-direction: column;
        }

        /* Header Styles */
        .profile-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            gap: 15px;
        }

        .welcome-section h1 {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .welcome-section p {
            color: var(--text-light);
            font-size: 16px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #002244;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
        }

        /* Main Content Layout */
        .content-wrapper {
            display: flex;
            gap: 20px;
            flex-direction: column;
        }

        @media (min-width: 900px) {
            .content-wrapper {
                flex-direction: row;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            width: 100%;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        @media (min-width: 900px) {
            .sidebar {
                width: 280px;
                flex-shrink: 0;
            }
        }

        .user-card {
            text-align: center;
            padding: 20px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--light-gray);
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            font-weight: bold;
        }

        .user-card h2 {
            font-size: 20px;
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-card .email {
            font-size: 14px;
            color: var(--text-light);
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navigation {
            list-style: none;
            margin-bottom: auto;
            padding: 0;
        }

        .navigation li {
            margin-bottom: 5px;
        }

        .navigation li a {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text-dark);
        }

        .navigation li a:hover,
        .navigation li a.active {
            background: var(--light-bg);
        }

        .navigation li i {
            margin-right: 12px;
            font-size: 18px;
            color: var(--secondary);
            width: 24px;
            text-align: center;
        }

        .navigation li .text {
            flex: 1;
            font-size: 16px;
            font-weight: 500;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-warning {
            background-color: var(--warning);
            color: #333;
        }

        .logout-container {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            color: var(--text-light);
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.05);
            color: var(--danger);
            border-color: rgba(220, 53, 69, 0.3);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            min-height: 500px;
        }

        .dashboard-welcome {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .dashboard-welcome i {
            font-size: 64px;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        .dashboard-welcome h2 {
            font-size: 28px;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .dashboard-welcome p {
            font-size: 17px;
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: var(--light-bg);
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--light-gray);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 16px;
            color: var(--text-light);
        }

        /* Footer */
        .profile-footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            color: var(--text-light);
            font-size: 14px;
            border-top: 1px solid var(--light-gray);
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 15px;
            background: var(--light-bg);
            border-radius: var(--border-radius);
            width: 120px;
            height: 120px;
            text-align: center;
            transition: var(--transition);
            border: 1px solid var(--light-gray);
            cursor: pointer;
        }

        .action-btn:hover {
            background: white;
            box-shadow: var(--shadow);
            transform: translateY(-3px);
        }

        .action-btn i {
            font-size: 28px;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .action-label {
            font-size: 14px;
            font-weight: 500;
        }
    </style>
</head>
<body>
  <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Panel de Usuario - Diamond Bright</h1>
                <p>Bienvenido, <?php echo $nombreUsuario; ?></p>
            </div>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Volver al inicio
            </a>
        </div>

        <div class="content-wrapper">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="user-card">
                    <div class="avatar">
                        <?php echo $inicialAvatar; ?>
                    </div>
                    <h2><?php echo $nombreUsuario; ?></h2>
                    <p class="email"><?php echo $emailUsuario; ?></p>
                </div>

                <ul class="navigation">
                    <li>
                        <a href="dashboard.php" class="active">
                            <i class="fas fa-tachometer-alt"></i>
                            <div class="text">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <a href="perfil.php">
                            <i class="fas fa-user"></i>
                            <div class="text">Perfil</div>
                        </a>
                    </li>
                    <li>
                        <a href="reservas.php">
                            <i class="fas fa-calendar-check"></i>
                            <div class="text">Reservas</div>
                            <span class="badge badge-warning"><?php echo $reservasActivas; ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="agregar-pago.php">
                            <i class="fas fa-credit-card"></i>
                            <div class="text">Agregar Pago</div>
                        </a>
                    </li>
                    <li>
                        <a href="opiniones.php">
                            <i class="fas fa-star"></i>
                            <div class="text">Opiniones dadas</div>
                        </a>
                    </li>
                    <li>
                        <a href="seguridad.php">
                            <i class="fas fa-shield-alt"></i>
                            <div class="text">Seguridad</div>
                        </a>
                    </li>
                    <li>
                        <a href="ayuda.php">
                            <i class="fas fa-question-circle"></i>
                            <div class="text">Ayuda</div>
                        </a>
                    </li>
                </ul>

                <div class="logout-container">
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <div class="dashboard-welcome">
                    <i class="fas fa-user-circle"></i>
                    <h2>Bienvenido a tu panel de usuario, <?php echo $nombreUsuario; ?></h2>
                    <p>Desde aquí puedes gestionar todas las actividades de tu cuenta, ver tus próximas reservas, configurar notificaciones y personalizar tu experiencia.</p>
                    
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-number"><?php echo $reservasActivas; ?></div>
                            <div class="stat-label">Reservas activas</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-number"><?php echo $valoracionPromedio; ?></div>
                            <div class="stat-label">Valoración promedio</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div class="stat-number"><?php echo $cuponesDisponibles; ?></div>
                            <div class="stat-label">Cupones disponibles</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-ship"></i>
                            </div>
                            <div class="stat-number"><?php echo $viajesRealizados; ?></div>
                            <div class="stat-label">Viajes realizados</div>
                        </div>
                    </div>
                    
                    <div class="quick-actions">
                        <a href="tours.php" class="action-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span class="action-label">Nueva reserva</span>
                        </a>
                        
                        <a href="editar-perfil.php" class="action-btn">
                            <i class="fas fa-user-edit"></i>
                            <span class="action-label">Editar perfil</span>
                        </a>
                        
                        <a href="agregar-pago.php" class="action-btn">
                            <i class="fas fa-credit-card"></i>
                            <span class="action-label">Añadir pago</span>
                        </a>
                        
                        <a href="favoritos.php" class="action-btn">
                            <i class="fas fa-heart"></i>
                            <span class="action-label">Favoritos</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-footer">
            <p>© 2025 Diamond Bright Catamarans. Todos los derechos reservados.</p>
            <p>Tu cuenta está segura con nosotros. <a href="politica.php" style="color: var(--secondary); text-decoration: none;">Política de privacidad</a></p>
        </div>
    </div>

    <script>
        // Este script maneja la clase 'active' en la navegación
        document.addEventListener('DOMContentLoaded', function() {
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.navigation li a');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (currentPage === href) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>