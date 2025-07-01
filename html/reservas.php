<?php
session_start(); // Iniciar sesión PHP

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

// ... EL RESTO DE TU CÓDIGO ACTUAL DE RESERVAS.PHP ...

// Crear instancia de Database y conectar
$database = new Database();
$pdo = $database->connect();

// Obtener reservas del usuario
$stmt = $pdo->prepare("
    SELECT r.*, t.nombre AS tour_nombre, t.imagen_principal, t.duracion
    FROM reservas r
    JOIN tours t ON r.tour_id = t.id
    WHERE r.usuario_id = ?
    ORDER BY r.fecha_tour DESC, r.hora_tour DESC
");
$stmt->execute([$usuario->id]);
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para formatear fecha
function formatearFecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

// Función para formatear hora
function formatearHora($hora) {
    return date('H:i', strtotime($hora));
}

// Función para obtener el estado con clase CSS
function obtenerClaseEstado($estado) {
    switch ($estado) {
        case 'confirmada': return 'estado-confirmada';
        case 'pendiente': return 'estado-pendiente';
        case 'cancelada': return 'estado-cancelada';
        case 'completada': return 'estado-completada';
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
            max-width: 1200px;
            margin: 0 auto;
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

        /* Reservas Container */
        .reservas-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Lista de Reservas */
        .reservas-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .reservas-list {
                grid-template-columns: 1fr 1fr;
            }
        }

        .reserva-card {
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .reserva-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .reserva-imagen {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .reserva-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .reserva-title {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .reserva-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: var(--text-light);
        }

        .reserva-details {
            margin-bottom: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .detail-label {
            font-weight: 500;
        }

        .reserva-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid var(--light-gray);
        }

        .reserva-estado {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .estado-confirmada {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .estado-pendiente {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .estado-cancelada {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .estado-completada {
            background-color: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }

        .reserva-acciones .btn {
            padding: 8px 15px;
            font-size: 14px;
        }

        .no-reservas {
            text-align: center;
            padding: 40px 20px;
            grid-column: 1 / -1;
        }

        .no-reservas i {
            font-size: 50px;
            color: var(--light-gray);
            margin-bottom: 15px;
        }

        .no-reservas p {
            font-size: 18px;
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .btn-explorar {
            background: var(--primary);
            color: white;
            display: inline-flex;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Mis Reservas - Diamond Bright</h1>
                <p>Administra tus experiencias reservadas</p>
            </div>
            <button class="btn btn-primary" onclick="location.href='perfil.php'">
                <i class="fas fa-arrow-left"></i> Volver al perfil
            </button>
        </div>

        <!-- Contenedor de Reservas -->
        <div class="reservas-container">
            <h2 class="section-title">
                <i class="fas fa-calendar-check"></i> Experiencias Reservadas
            </h2>

            <div class="reservas-list">
                <?php if (count($reservas) > 0): ?>
                    <?php foreach ($reservas as $reserva): ?>
                        <div class="reserva-card">
                            <div class="reserva-imagen" style="background-image: url('<?php echo $reserva['imagen_principal'] ?: 'https://via.placeholder.com/400x200?text=Tour+Image'; ?>');"></div>
                            
                            <div class="reserva-content">
                                <h3 class="reserva-title"><?php echo htmlspecialchars($reserva['tour_nombre']); ?></h3>
                                
                                <div class="reserva-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <?php echo htmlspecialchars($reserva['duracion']); ?>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-users"></i>
                                        <?php echo ($reserva['numero_adultos'] + $reserva['numero_ninos']); ?> personas
                                    </div>
                                </div>
                                
                                <div class="reserva-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Fecha:</span>
                                        <span><?php echo formatearFecha($reserva['fecha_tour']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Hora:</span>
                                        <span><?php echo formatearHora($reserva['hora_tour']); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Adultos:</span>
                                        <span><?php echo $reserva['numero_adultos']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Niños:</span>
                                        <span><?php echo $reserva['numero_ninos']; ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Total:</span>
                                        <span style="font-weight: bold;">$<?php echo number_format($reserva['precio_total'], 2); ?></span>
                                    </div>
                                </div>
                                
                                <div class="reserva-footer">
                                    <span class="reserva-estado <?php echo obtenerClaseEstado($reserva['estado']); ?>">
                                        <?php echo ucfirst($reserva['estado']); ?>
                                    </span>
                                    
                                    <div class="reserva-acciones">
                                        <?php if ($reserva['estado'] === 'pendiente'): ?>
                                            <button class="btn btn-cancel" onclick="cancelarReserva(<?php echo $reserva['id']; ?>)">
                                                <i class="fas fa-times"></i> Cancelar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reservas">
                        <i class="fas fa-calendar-times"></i>
                        <p>No tienes reservas activas</p>
                        <a href="tours.php" class="btn-explorar">
                            <i class="fas fa-ship"></i> Explorar Tours
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function cancelarReserva(reservaId) {
            if (confirm('¿Estás seguro de que deseas cancelar esta reserva?')) {
                // En un sistema real, aquí haríamos una petición AJAX o redirigiríamos
                // a un script de procesamiento de cancelación
                alert(`Reserva #${reservaId} cancelada (simulación)`);
                // Esto sería una petición real:
                // fetch(`cancelar_reserva.php?id=${reservaId}`)
                //   .then(response => response.json())
                //   .then(data => {
                //       if (data.success) {
                //           location.reload();
                //       } else {
                //           alert('Error: ' + data.message);
                //       }
                //   });
            }
        }
    </script>
</body>
</html>