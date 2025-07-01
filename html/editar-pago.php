<?php
session_start();
require_once 'Sesion.php';
require_once 'Usuario.php';
require_once 'conexion.php';

// Función para redirigir a login
function redirectToLogin() {
    setcookie('db_session', '', time() - 3600, '/');
    header("Location: inicio-sesion.php");
    exit();
}

// Verificar sesión
if (empty($_COOKIE['db_session'])) {
    redirectToLogin();
}

// Validar sesión
$sesion = new Sesion();
$sesion_valida = $sesion->validar($_COOKIE['db_session']);

if (!$sesion_valida) {
    redirectToLogin();
}

// Obtener información del usuario
$usuario = new Usuario();
if (!$usuario->buscarPorId($sesion_valida['usuario_id'])) {
    redirectToLogin();
}

if (isset($_GET['id'])) {
    $metodoId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$metodoId, $usuario->id]);
    $metodo = $stmt->fetch(PDO::FETCH_ASSOC);
}

$metodoId = $_GET['id'];

// Conectar a la base de datos
$database = new Database();
$pdo = $database->connect();

// Obtener el método de pago
$stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
$stmt->execute([$metodoId, $usuario->id]);
$metodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$metodo) {
    header("Location: pagos.php?error=no_encontrado");
    exit();
}

// Preparar datos para la vista
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));

// Mensaje de error si hay
$error = '';
if (isset($_GET['error'])) {
    $error = match ($_GET['error']) {
        'campos' => 'Por favor, complete todos los campos obligatorios.',
        'tarjeta' => 'El número de tarjeta no es válido.',
        'expiracion' => 'La fecha de expiración debe ser en el futuro y en formato MM/YY.',
        'cvv' => 'El CVV debe tener 3 o 4 dígitos.',
        'email' => 'El email de PayPal no es válido.',
        'general' => 'Ocurrió un error al guardar el método de pago.',
        default => 'Ocurrió un error.'
    };
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Método de Pago - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* ... (estilos similares a agregar-pago.php) ... */
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Editar Método de Pago - Diamond Bright</h1>
            </div>
            <button class="btn btn-primary" onclick="location.href='pagos.php'">
                <i class="fas fa-arrow-left"></i> Volver a formas de pago
            </button>
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
                    <li onclick="location.href='perfil.php'">
                        <i class="fas fa-user"></i>
                        <div class="text">Perfil</div>
                    </li>
                    <li onclick="location.href='reservas.php'">
                        <i class="fas fa-calendar-check"></i>
                        <div class="text">Reservas</div>
                    </li>
                    <li>
                        <i class="fas fa-bell"></i>
                        <div class="text">Notificaciones</div>
                    </li>
                    <li class="active">
                        <i class="fas fa-credit-card"></i>
                        <div class="text">Formas de pago</div>
                    </li>
                    <li>
                        <i class="fas fa-star"></i>
                        <div class="text">Opiniones dadas</div>
                    </li>
                    <li>
                        <i class="fas fa-shield-alt"></i>
                        <div class="text">Seguridad</div>
                    </li>
                </ul>

                <div class="logout-container">
                    <button class="logout-btn" onclick="location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </button>
                </div>
            </div>

            <!-- Main Content -->
             <form action="procesar_pago.php" method="POST">
    <input type="hidden" name="accion" value="<?= isset($metodo) ? 'editar' : 'agregar' ?>">
    <?php if (isset($metodo)): ?>
        <input type="hidden" name="id" value="<?= $metodo['id'] ?>">
    <?php endif; ?>
            <div class="main-content">
                <h2 class="section-title">
                    <i class="fas fa-edit"></i> Editar método de pago
                </h2>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form class="add-form" action="procesar_pago.php" method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id" value="<?php echo $metodo['id']; ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario->id; ?>">
                    
                    <div class="payment-type-selector">
                        <div class="payment-option <?php echo $metodo['tipo'] === 'tarjeta' ? 'selected' : ''; ?>" onclick="selectPaymentType('tarjeta')" id="option-tarjeta">
                            <i class="fas fa-credit-card"></i>
                            <span>Tarjeta de crédito/débito</span>
                        </div>
                        <div class="payment-option <?php echo $metodo['tipo'] === 'paypal' ? 'selected' : ''; ?>" onclick="selectPaymentType('paypal')" id="option-paypal">
                            <i class="fab fa-paypal"></i>
                            <span>PayPal</span>
                        </div>
                        <input type="hidden" name="tipo" id="tipo" value="<?php echo $metodo['tipo']; ?>">
                    </div>
                    
                    <!-- Campos para tarjeta -->
                    <div class="payment-fields <?php echo $metodo['tipo'] === 'tarjeta' ? 'active' : ''; ?>" id="fields-tarjeta">
                        <div class="form-group">
                            <label for="titular"><i class="fas fa-user"></i> Nombre del titular</label>
                            <input type="text" id="titular" name="titular" class="form-control" value="<?php echo htmlspecialchars($metodo['titular']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero"><i class="fas fa-credit-card"></i> Número de tarjeta</label>
                            <input type="text" id="numero" name="numero" class="form-control" placeholder="1234 5678 9012 3456" value="<?php echo htmlspecialchars($metodo['numero']); ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiracion"><i class="fas fa-calendar"></i> Fecha de expiración (MM/YY)</label>
                                <input type="text" id="expiracion" name="expiracion" class="form-control" placeholder="MM/YY" value="<?php echo htmlspecialchars($metodo['expiracion']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv"><i class="fas fa-lock"></i> CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" value="<?php echo htmlspecialchars($metodo['cvv']); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos para PayPal -->
                    <div class="payment-fields <?php echo $metodo['tipo'] === 'paypal' ? 'active' : ''; ?>" id="fields-paypal">
                        <div class="form-group">
                            <label for="email_paypal"><i class="fas fa-envelope"></i> Email de PayPal</label>
                            <input type="email" id="email_paypal" name="email_paypal" class="form-control" value="<?php echo htmlspecialchars($metodo['numero']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="predeterminado" value="1" <?php echo $metodo['predeterminado'] ? 'checked' : ''; ?>>
                            Establecer como método de pago predeterminado
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-cancel" onclick="location.href='pagos.php'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectPaymentType(tipo) {
            // Reset all options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Set selected option
            document.getElementById(`option-${tipo}`).classList.add('selected');
            document.getElementById('tipo').value = tipo;
            
            // Show corresponding fields
            document.querySelectorAll('.payment-fields').forEach(field => {
                field.classList.remove('active');
            });
            document.getElementById(`fields-${tipo}`).classList.add('active');
        }
    </script>
</body>
</html>