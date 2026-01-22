<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
error_log("Accediendo a agendar-reserva.php. ID de sesión: " . session_id());
error_log("Usuario ID en sesión: " . ($_SESSION['usuario_id'] ?? 'No definido'));

if (!isset($_SESSION['usuario_id'])) {
    error_log("Redirigiendo a inicio-sesion.php desde agendar-reserva.php");
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

// Obtener tours disponibles
$tours = [];
try {
    $stmt = $pdo->query("SELECT * FROM Tour");
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error al obtener tours: " . $e->getMessage());
}

$mensaje = '';
$error = '';

// Procesar formulario de reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idTour = $_POST['tour'] ?? null;
    $fecha = $_POST['fecha'] ?? null;
    $metodoPago = $_POST['metodo_pago'] ?? '';

    // Validaciones
    if (empty($idTour)) {
        $error = "Selecciona un tour válido";
    } elseif (empty($fecha)) {
        $error = "Selecciona una fecha";
    } elseif (strtotime($fecha) < strtotime('today')) {
        $error = "La fecha no puede ser en el pasado";
    } elseif (empty($metodoPago)) {
        $error = "Selecciona un método de pago";
    } else {
        // Obtener precio del tour
        $stmt = $pdo->prepare("SELECT Precio FROM Tour WHERE Id_Tour = ?");
        $stmt->execute([$idTour]);
        $tourPrecio = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($tourPrecio) {
            $monto = $tourPrecio['Precio'];
            
            // Insertar reserva - CORREGIDO: quitamos Metodo_Pago
            try {
                $stmt = $pdo->prepare("INSERT INTO Reserva (Id_Cliente, Id_Tour, Fecha_Reserva, Monto, Estado, Fecha_Viaje) 
                                      VALUES (?, ?, NOW(), ?, 'pendiente', ?)");
                $stmt->execute([
                    $_SESSION['usuario_id'],
                    $idTour,
                    $monto,
                    $fecha
                ]);
                
                $mensaje = "¡Reserva creada exitosamente!";
                error_log("Reserva creada para usuario ID: {$_SESSION['usuario_id']}");
                
                // Redirigir después de 3 segundos
                header("Refresh: 3; url=reservacliente.php");
            } catch (PDOException $e) {
                $error = "Error al crear la reserva: " . $e->getMessage();
                error_log("Error en reserva: " . $e->getMessage());
            }
        } else {
            $error = "Tour no encontrado";
        }
    }
}

$nombreUsuario = htmlspecialchars($cliente['Nombre']);
$emailUsuario = htmlspecialchars($cliente['Correo']);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Reserva - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .reserva-form-container {
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .btn-submit {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            display: block;
            width: 100%;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #3a4cca;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .tour-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tour-card:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(94, 114, 228, 0.2);
        }
        
        .tour-card.selected {
            border-color: var(--secondary);
            background-color: rgba(94, 114, 228, 0.05);
        }
        
        .tour-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .tour-title {
            font-weight: 600;
            color: var(--dark);
        }
        
        .tour-price {
            font-weight: bold;
            color: var(--secondary);
        }
        
        .tour-description {
            color: #666;
            margin-bottom: 10px;
        }
        
        .tour-details {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
        }
        
        .tour-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #555;
        }
        
        .preview-box {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .preview-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .preview-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .preview-label {
            color: #666;
        }
        
        .preview-value {
            font-weight: 500;
        }
        
        .preview-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--secondary);
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
  <div class="container">
    <div class="profile-header">
        <div class="welcome-section">
            <h1>Agendar Nueva Reserva - Diamond Bright</h1>
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
                    <a href="reservacliente.php">
                        <i class="fas fa-calendar-check"></i>
                        <div class="text">Reservas</div>
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
            <div class="reserva-form-container">
                <div class="form-header">
                    <h2><i class="fas fa-ship"></i> Agendar Nuevo Tour</h2>
                    <p>Selecciona un tour y completa los detalles de tu reserva</p>
                </div>

                <?php if ($mensaje): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
                        <p>Redirigiendo a tus reservas...</p>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post" id="reservaForm">
                    <div class="form-group">
                        <label class="form-label">Selecciona un tour:</label>
                        <div id="tours-container">
                            <?php foreach ($tours as $tour): ?>
                                <div class="tour-card" 
                                     data-id="<?php echo $tour['Id_Tour']; ?>"
                                     data-precio="<?php echo $tour['Precio']; ?>">
                                    <div class="tour-header">
                                        <div class="tour-title"><?php echo htmlspecialchars($tour['Nombre']); ?></div>
                                        <div class="tour-price">
                                            $<?php echo number_format($tour['Precio'], 2); ?>
                                        </div>
                                    </div>
                                    <div class="tour-description">
                                        <?php echo htmlspecialchars($tour['Descripcion']); ?>
                                    </div>
                                    <div class="tour-details">
                                        <div class="tour-detail">
                                            <i class="fas fa-users"></i>
                                            <span>Capacidad: <?php echo $tour['Capacidad']; ?> personas</span>
                                        </div>
                                        <div class="tour-detail">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>Ubicación: Isla Mujeres</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($tours)): ?>
                                <p>No hay tours disponibles en este momento</p>
                            <?php endif; ?>
                        </div>
                        <input type="hidden" name="tour" id="selectedTour" required>
                    </div>

                    <div class="form-row">
                        <div class="form-col">
                            <div class="form-group">
                                <label class="form-label" for="fecha">Fecha del tour:</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="preview-box">
                        <div class="preview-title">Resumen de Reserva</div>
                        <div class="preview-row">
                            <span class="preview-label">Tour seleccionado:</span>
                            <span class="preview-value" id="preview-tour">Ninguno</span>
                        </div>
                        <div class="preview-row">
                            <span class="preview-label">Fecha:</span>
                            <span class="preview-value" id="preview-fecha">--/--/----</span>
                        </div>
                        <div class="preview-total">
                            Total: <span id="preview-total">$0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-calendar-check"></i> Confirmar Reserva
                    </button>
                </form>
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
        // Selección de tour
        const tourCards = document.querySelectorAll('.tour-card');
        const selectedTourInput = document.getElementById('selectedTour');
        let selectedTour = null;
        
        tourCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remover selección anterior
                tourCards.forEach(c => c.classList.remove('selected'));
                
                // Seleccionar nueva
                this.classList.add('selected');
                selectedTour = this;
                selectedTourInput.value = this.dataset.id;
                
                updatePreview();
            });
        });

        // Actualizar vista previa al cambiar valores
        const fechaInput = document.getElementById('fecha');
        fechaInput.addEventListener('change', updatePreview);

        function updatePreview() {
            // Tour
            const previewTour = document.getElementById('preview-tour');
            if (selectedTour) {
                previewTour.textContent = selectedTour.querySelector('.tour-title').textContent;
            } else {
                previewTour.textContent = 'Ninguno';
            }
            
            // Fecha
            const previewFecha = document.getElementById('preview-fecha');
            if (fechaInput.value) {
                const fecha = new Date(fechaInput.value);
                previewFecha.textContent = fecha.toLocaleDateString('es-ES');
            } else {
                previewFecha.textContent = '--/--/----';
            }
            
            // Total
            const previewTotal = document.getElementById('preview-total');
            if (selectedTour) {
                const precio = parseFloat(selectedTour.dataset.precio);
                previewTotal.textContent = '$' + precio.toFixed(2);
            } else {
                previewTotal.textContent = '$0.00';
            }
        }
        
        // Seleccionar primer tour por defecto
        if (tourCards.length > 0) {
            tourCards[0].click();
        }
    });
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Reserva - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        .reserva-form-container {
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .btn-submit {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            display: block;
            width: 100%;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #3a4cca;
        }
        
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .tour-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .tour-card:hover {
            border-color: var(--secondary);
            box-shadow: 0 0 10px rgba(94, 114, 228, 0.2);
        }
        
        .tour-card.selected {
            border-color: var(--secondary);
            background-color: rgba(94, 114, 228, 0.05);
        }
        
        .tour-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .tour-title {
            font-weight: 600;
            color: var(--dark);
        }
        
        .tour-price {
            font-weight: bold;
            color: var(--secondary);
        }
        
        .tour-description {
            color: #666;
            margin-bottom: 10px;
        }
        
        .tour-details {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
        }
        
        .tour-detail {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #555;
        }
        
        .preview-box {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .preview-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--primary);
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        
        .preview-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .preview-label {
            color: #666;
        }
        
        .preview-value {
            font-weight: 500;
        }
        
        .preview-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: var(--secondary);
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
  <div class="container">
    <div class="profile-header">
        <div class="welcome-section">
            <h1>Agendar Nueva Reserva - Diamond Bright</h1>
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
                    <a href="reservacliente.php">
                        <i class="fas fa-calendar-check"></i>
                        <div class="text">Reservas</div>
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
        <div class="reserva-form-container">
            <div class="form-header">
                <h2><i class="fas fa-ship"></i> Agendar Nuevo Tour</h2>
                <p>Selecciona un tour y completa los detalles de tu reserva</p>
            </div>

            <?php if ($mensaje): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
                    <p>Redirigiendo a tus reservas...</p>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="post" id="reservaForm">
                <div class="form-group">
                    <label class="form-label">Selecciona un tour:</label>
                    <div id="tours-container">
                        <?php foreach ($tours as $tour): ?>
                            <div class="tour-card" 
                                 data-id="<?php echo $tour['Id_Tour']; ?>"
                                 data-precio="<?php echo $tour['Precio']; ?>">
                                <div class="tour-header">
                                    <div class="tour-title"><?php echo htmlspecialchars($tour['Nombre']); ?></div>
                                    <div class="tour-price">
                                        $<?php echo number_format($tour['Precio'], 2); ?>
                                    </div>
                                </div>
                                <div class="tour-description">
                                    <?php echo htmlspecialchars($tour['Descripcion']); ?>
                                </div>
                                <div class="tour-details">
                                    <div class="tour-detail">
                                        <i class="fas fa-users"></i>
                                        <span>Capacidad: <?php echo $tour['Capacidad']; ?> personas</span>
                                    </div>
                                    <div class="tour-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Ubicación: Isla Mujeres</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($tours)): ?>
                            <p>No hay tours disponibles en este momento</p>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="tour" id="selectedTour" required>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label" for="fecha">Fecha del tour:</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-col">
                        <div class="form-group">
                            <label class="form-label">Método de pago:</label>
                            <div class="radio-group">
                                <div class="radio-option">
                                    <input type="radio" id="pago_tarjeta" name="metodo_pago" value="tarjeta" required>
                                    <label for="pago_tarjeta"><i class="fas fa-credit-card"></i> Tarjeta</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="pago_transferencia" name="metodo_pago" value="transferencia">
                                    <label for="pago_transferencia"><i class="fas fa-exchange-alt"></i> Transferencia</label>
                                </div>
                                <div class="radio-option">
                                    <input type="radio" id="pago_efectivo" name="metodo_pago" value="efectivo">
                                    <label for="pago_efectivo"><i class="fas fa-money-bill-wave"></i> Efectivo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="preview-box">
                    <div class="preview-title">Resumen de Reserva</div>
                    <div class="preview-row">
                        <span class="preview-label">Tour seleccionado:</span>
                        <span class="preview-value" id="preview-tour">Ninguno</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Fecha:</span>
                        <span class="preview-value" id="preview-fecha">--/--/----</span>
                    </div>
                    <div class="preview-row">
                        <span class="preview-label">Método de pago:</span>
                        <span class="preview-value" id="preview-metodo">No seleccionado</span>
                    </div>
                    <div class="preview-total">
                        Total: <span id="preview-total">$0.00</span>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-calendar-check"></i> Confirmar Reserva
                </button>
            </form>
        </div>
    </div>
<footer>
    <div class="profile-footer">
        <p>© 2025 Diamond Bright Catamarans. Todos los derechos reservados.</p>
        <p>Tu cuenta está segura con nosotros. <a href="politica.php" style="color: var(--secondary); text-decoration: none;">Política de privacidad</a></p>
    </div>
  </div>
                        </footer>

 <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selección de tour
        const tourCards = document.querySelectorAll('.tour-card');
        const selectedTourInput = document.getElementById('selectedTour');
        let selectedTour = null;
        
        tourCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remover selección anterior
                tourCards.forEach(c => c.classList.remove('selected'));
                
                // Seleccionar nueva
                this.classList.add('selected');
                selectedTour = this;
                selectedTourInput.value = this.dataset.id;
                
                updatePreview();
            });
        });

        // Actualizar vista previa al cambiar valores
        const fechaInput = document.getElementById('fecha');
        const pagoOptions = document.querySelectorAll('input[name="metodo_pago"]');
        
        fechaInput.addEventListener('change', updatePreview);
        pagoOptions.forEach(option => {
            option.addEventListener('change', updatePreview);
        });

        function updatePreview() {
            // Tour
            const previewTour = document.getElementById('preview-tour');
            if (selectedTour) {
                previewTour.textContent = selectedTour.querySelector('.tour-title').textContent;
            } else {
                previewTour.textContent = 'Ninguno';
            }
            
            // Fecha
            const previewFecha = document.getElementById('preview-fecha');
            if (fechaInput.value) {
                const fecha = new Date(fechaInput.value);
                previewFecha.textContent = fecha.toLocaleDateString('es-ES');
            } else {
                previewFecha.textContent = '--/--/----';
            }
            
            // Método de pago
            const selectedPago = document.querySelector('input[name="metodo_pago"]:checked');
            const previewMetodo = document.getElementById('preview-metodo');
            if (selectedPago) {
                previewMetodo.textContent = selectedPago.nextElementSibling.textContent;
            } else {
                previewMetodo.textContent = 'No seleccionado';
            }
            
            // Total
            const previewTotal = document.getElementById('preview-total');
            if (selectedTour) {
                const precio = parseFloat(selectedTour.dataset.precio);
                previewTotal.textContent = '$' + precio.toFixed(2);
            } else {
                previewTotal.textContent = '$0.00';
            }
        }
        
        // Seleccionar primer tour por defecto
        if (tourCards.length > 0) {
            tourCards[0].click();
        }
    });
  </script>
</body>
</html>