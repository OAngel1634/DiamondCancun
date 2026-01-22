<?php
session_start();
error_log("Accediendo a reservacliente.php. ID de sesión: " . session_id());
error_log("Usuario ID en sesión: " . ($_SESSION['usuario_id'] ?? 'No definido'));

if (!isset($_SESSION['usuario_id'])) {
    error_log("Redirigiendo a inicio-sesion.php desde reservacliente.php");
    header("Location: inicio-sesion.php");
    exit();
}

require_once 'conexion.php';
$database = new Database();
$pdo = $database->connect();

// Obtener datos del cliente
$stmt = $pdo->prepare("SELECT * FROM Cliente WHERE Id_Cliente = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

// Obtener reservas del cliente
$stmt = $pdo->prepare("SELECT * FROM Reserva WHERE Id_Cliente = ? ORDER BY Fecha_Reserva DESC");
$stmt->execute([$_SESSION['usuario_id']]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores para estadísticas
$reservasActivas = 0;
$viajesRealizados = 0;

foreach ($reservas as $reserva) {
    if ($reserva['Estado'] == 'pendiente' || $reserva['Estado'] == 'confirmado') {
        $reservasActivas++;
    } elseif ($reserva['Estado'] == 'completado') {
        $viajesRealizados++;
    }
}

$nombreUsuario = htmlspecialchars($cliente['Nombre']);
$emailUsuario = htmlspecialchars($cliente['Correo']);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));

// Función para formatear fecha
function formatearFecha($fecha) {
    if ($fecha) {
        return date('d/m/Y', strtotime($fecha));
    }
    return '--/--/----';
}

// Función para obtener clase CSS según estado
function getEstadoClass($estado) {
    switch ($estado) {
        case 'pendiente': return 'estado-pendiente';
        case 'confirmado': return 'estado-confirmado';
        case 'completado': return 'estado-completado';
        case 'cancelado': return 'estado-cancelado';
        default: return '';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Reservas - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .reservas-container {
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .reservas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .reservas-title {
            font-size: 1.5rem;
            color: var(--primary);
        }

        .reserva-card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
            transition: transform 0.3s ease;
        }

        .reserva-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .reserva-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .reserva-id {
            font-weight: bold;
            color: var(--dark);
        }

        .reserva-fecha {
            color: #666;
            font-size: 0.9rem;
        }

        .reserva-body {
            display: flex;
            justify-content: space-between;
        }

        .reserva-info {
            flex: 1;
        }

        .reserva-monto {
            font-weight: bold;
            font-size: 1.2rem;
            color: var(--secondary);
        }

        .reserva-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .estado-pendiente {
            background-color: #ffc107;
            color: #333;
        }

        .estado-confirmado {
            background-color: #28a745;
            color: white;
        }

        .estado-completado {
            background-color: #17a2b8;
            color: white;
        }

        .estado-cancelado {
            background-color: #dc3545;
            color: white;
        }

        .no-reservas {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .no-reservas i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .btn-nueva-reserva {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
        }

        .btn-nueva-reserva i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
  <div class="container">
    <div class="profile-header">
        <div class="welcome-section">
            <h1>Mis Reservas - Diamond Bright</h1>
            <p>Bienvenido, <?php echo $nombreUsuario; ?></p>
        </div>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-home"></i> Volver al inicio
        </a>
    </div>

    <div class="content-wrapper">
        <div class="sidebar">
            <div class="user-card">
                <div class="avatar"><?php echo $inicialAvatar; ?></div>
                <h2><?php echo $nombreUsuario; ?></h2>
                <p class="email"><?php echo $emailUsuario; ?></p>
            </div>

            <ul class="navigation">
                <li>
                    <a href="editar-perfil.php">
                        <i class="fas fa-user"></i>
                        <div class="text">Perfil</div>
                    </a>
                </li>
                <li>
                    <a href="reservacliente.php" class="active">
                        <i class="fas fa-calendar-check"></i>
                        <div class="text">Reservas</div>
                        <span class="badge badge-warning"><?php echo $reservasActivas; ?></span>
                    </a>
                </li>
                <li>
                    <a href="agendar-reserva.php" class="active">
                        <i class="fas fa-plus-circle"></i>
                        <div class="text">Nueva Reserva</div>
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

        <div class="main-content">
            <div class="reservas-container">
                <div class="reservas-header">
                    <h2 class="reservas-title">
                        <i class="fas fa-calendar-alt"></i> Historial de Reservas
                    </h2>
                    <a href="tours.php" class="btn-nueva-reserva">
                        <i class="fas fa-plus-circle"></i> Nueva reserva
                    </a>
                </div>

                <?php if (count($reservas) > 0): ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <div class="reserva-card">
                            <div class="reserva-header">
                                <div class="reserva-id">Reserva #<?php echo $reserva['Id_Reserva']; ?></div>
                                <div class="reserva-fecha">Fecha: <?php echo formatearFecha($reserva['Fecha_Reserva']); ?></div>
                            </div>
                            <div class="reserva-body">
                                <div class="reserva-info">
                                    <div>Estado: 
                                        <span class="reserva-estado <?php echo getEstadoClass($reserva['Estado']); ?>">
                                            <?php echo ucfirst($reserva['Estado']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="reserva-monto">$<?php echo number_format($reserva['Monto'], 2); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reservas">
                        <i class="fas fa-calendar-times"></i>
                        <h3>No tienes reservas aún</h3>
                        <p>Comienza explorando nuestros tours disponibles</p>
                        <a href="tours.php" class="btn-nueva-reserva" style="margin-top: 15px;">
                            <i class="fas fa-ship"></i> Ver tours disponibles
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="profile-footer">
        <p>© 2025 Diamond Bright Catamarans. Todos los derechos reservados.</p>
        <p>Tu cuenta está segura con nosotros. <a href="politica.php" style="color: var(--secondary); text-decoration: none;">Política de privacidad</a></p>
    </div>
  </div>

  <script>
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