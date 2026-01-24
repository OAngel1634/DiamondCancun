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

// ... EL RESTO DE TU CÓDIGO ACTUAL DE AGREGAR-PAGO.PHP ...
// Crear instancia de Database y conectar
$database = new Database();
$pdo = $database->connect();

// Inicializar variables
$metodo = null;
$metodoId = null;

// Comprobar si estamos editando un método existente
if (isset($_GET['id'])) {
    $metodoId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$metodoId, $usuario->id]);
    $metodo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$metodo) {
        header("Location: pagos.php?error=no_encontrado");
        exit();
    }
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
    <title><?= isset($metodo) ? 'Editar' : 'Agregar' ?> Método de Pago - Diamond Bright</title>
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

        .main-content {
            flex: 1;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
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

        .error-message {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border-left: 4px solid var(--danger);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .error-message i {
            font-size: 20px;
        }

        .add-form {
            max-width: 800px;
            margin: 0 auto;
        }

        .payment-type-selector {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .payment-option {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px solid var(--light-gray);
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            min-height: 150px;
        }

        .payment-option:hover {
            border-color: var(--secondary);
        }

        .payment-option.selected {
            border-color: var(--accent);
            background: rgba(212, 175, 55, 0.05);
        }

        .payment-option i {
            font-size: 36px;
            margin-bottom: 15px;
            color: var(--secondary);
        }

        .payment-option.selected i {
            color: var(--accent);
        }

        .payment-option span {
            font-weight: 500;
            font-size: 16px;
        }

        .payment-fields {
            display: none;
            padding: 20px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            background: var(--light-bg);
        }

        .payment-fields.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 153, 204, 0.2);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 25px 0;
        }

        .form-checkbox input {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-cancel {
            background: var(--light-gray);
            color: var(--text-dark);
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid var(--light-gray);
        }

        .btn-cancel:hover {
            background: #d0d0d0;
        }

        .btn-save {
            background: var(--accent);
            color: white;
            padding: 12px 25px;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-save:hover {
            background: #c19b1e;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .profile-footer {
            margin-top: 30px;
            text-align: center;
            padding: 20px;
            color: var(--text-light);
            font-size: 14px;
            border-top: 1px solid var(--light-gray);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <div class="welcome-section">
                <h1><?= isset($metodo) ? 'Editar' : 'Agregar' ?> Método de Pago - Diamond Bright</h1>
            </div>
            <button class="btn btn-primary" onclick="location.href='pagos.php'">
                <i class="fas fa-arrow-left"></i> Volver a formas de pago
            </button>
        </div>

        <div class="content-wrapper">
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
                        <a href="dashboard.php">
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
                        </a>
                    </li>
                    <li>
                        <a href="agregar-pago.php" class="active">
                            <i class="fas fa-credit-card"></i>
                            <div class="text">Agregar Pago</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-star"></i>
                            <div class="text">Opiniones dadas</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-shield-alt"></i>
                            <div class="text">Seguridad</div>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-question-circle"></i>
                            <div class="text">Ayuda</div>
                        </a>
                    </li>
                </ul>

                <div class="logout-container">
                    <button class="logout-btn" onclick="location.href='logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </button>
                </div>
            </div>

            <div class="main-content">
                <h2 class="section-title">
                    <i class="fas <?= isset($metodo) ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
                    <?= isset($metodo) ? 'Editar método de pago' : 'Agregar nuevo método de pago' ?>
                </h2>

                <?php if ($error): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form class="add-form" action="procesar_pago.php" method="POST">
                    <input type="hidden" name="accion" value="<?= isset($metodo) ? 'editar' : 'agregar' ?>">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario->id; ?>">
                    <?php if (isset($metodo)): ?>
                        <input type="hidden" name="id" value="<?= $metodo['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="payment-type-selector">
                        <div class="payment-option <?= (!isset($metodo) || $metodo['tipo'] === 'tarjeta') ? 'selected' : '' ?>" 
                             onclick="selectPaymentType('tarjeta')" 
                             id="option-tarjeta">
                            <i class="fas fa-credit-card"></i>
                            <span>Tarjeta de crédito/débito</span>
                        </div>
                        <div class="payment-option <?= (isset($metodo) && $metodo['tipo'] === 'paypal') ? 'selected' : '' ?>" 
                             onclick="selectPaymentType('paypal')" 
                             id="option-paypal">
                            <i class="fab fa-paypal"></i>
                            <span>PayPal</span>
                        </div>
                        <input type="hidden" name="tipo" id="tipo" 
                               value="<?= isset($metodo) ? $metodo['tipo'] : 'tarjeta' ?>">
                    </div>
                    
                    <div class="payment-fields <?= (!isset($metodo) || $metodo['tipo'] === 'tarjeta') ? 'active' : '' ?>" 
                         id="fields-tarjeta">
                        <div class="form-group">
                            <label for="titular"><i class="fas fa-user"></i> Nombre del titular</label>
                            <input type="text" id="titular" name="titular" class="form-control" 
                                   value="<?= isset($metodo) ? htmlspecialchars($metodo['titular']) : '' ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero"><i class="fas fa-credit-card"></i> Número de tarjeta</label>
                            <input type="text" id="numero" name="numero" class="form-control" 
                                   placeholder="1234 5678 9012 3456" 
                                   value="<?= isset($metodo) ? htmlspecialchars($metodo['numero']) : '' ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiracion"><i class="fas fa-calendar"></i> Fecha de expiración (MM/YY)</label>
                                <input type="text" id="expiracion" name="expiracion" class="form-control" 
                                       placeholder="MM/YY" 
                                       value="<?= isset($metodo) ? htmlspecialchars($metodo['expiracion']) : '' ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cvv"><i class="fas fa-lock"></i> CVV</label>
                                <input type="text" id="cvv" name="cvv" class="form-control" placeholder="123" 
                                       value="<?= isset($metodo) ? htmlspecialchars($metodo['cvv']) : '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="payment-fields <?= (isset($metodo) && $metodo['tipo'] === 'paypal') ? 'active' : '' ?>" 
                         id="fields-paypal">
                        <div class="form-group">
                            <label for="email_paypal"><i class="fas fa-envelope"></i> Email de PayPal</label>
                            <input type="email" id="email_paypal" name="email_paypal" class="form-control"
                                   value="<?= isset($metodo) ? htmlspecialchars($metodo['numero']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="predeterminado" value="1"
                                <?= (isset($metodo) && $metodo['predeterminado']) ? 'checked' : '' ?>>
                            Establecer como método de pago predeterminado
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-cancel" onclick="location.href='pagos.php'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> <?= isset($metodo) ? 'Actualizar' : 'Guardar' ?> método
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectPaymentType(tipo) {
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            document.getElementById(`option-${tipo}`).classList.add('selected');
            document.getElementById('tipo').value = tipo;
            
            document.querySelectorAll('.payment-fields').forEach(field => {
                field.classList.remove('active');
            });
            document.getElementById(`fields-${tipo}`).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tipo = "<?= isset($metodo) ? $metodo['tipo'] : 'tarjeta' ?>";
            selectPaymentType(tipo);
        });
    </script>
</body>
</html>