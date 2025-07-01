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

// Conectar a la base de datos
$database = new Database();
$pdo = $database->connect();

// Obtener métodos de pago del usuario
$stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE usuario_id = ?");
$stmt->execute([$usuario->id]);
$metodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Preparar datos para la vista
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formas de Pago - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos base (copiados de perfil.php) */
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
        }

        .navigation li {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 5px;
        }

        .navigation li:hover,
        .navigation li.active {
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

        .btn-add {
            background: var(--success);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background: #218838;
        }

        .payment-methods {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .payment-card {
            border: 1px solid var(--light-gray);
            border-radius: var(--border-radius);
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: var(--transition);
        }

        .payment-card:hover {
            border-color: var(--secondary);
            box-shadow: var(--shadow);
        }

        .payment-card.default {
            border: 2px solid var(--accent);
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .payment-type {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-actions {
            display: flex;
            gap: 10px;
        }

        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-light);
            font-size: 16px;
            transition: var(--transition);
        }

        .btn-action:hover {
            color: var(--secondary);
        }

        .payment-details {
            margin-bottom: 15px;
        }

        .payment-detail {
            margin-bottom: 8px;
            font-size: 15px;
        }

        .default-tag {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--accent);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
        }

        .set-default {
            margin-top: 10px;
            display: inline-block;
            color: var(--accent);
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        .set-default:hover {
            text-decoration: underline;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            background: var(--success);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            background: var(--danger);
        }

        .no-payments {
            text-align: center;
            padding: 40px 20px;
        }

        .no-payments i {
            font-size: 50px;
            color: var(--light-gray);
            margin-bottom: 15px;
        }

        .no-payments p {
            font-size: 18px;
            color: var(--text-light);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Formas de Pago - Diamond Bright</h1>
                <p>Administra tus métodos de pago</p>
            </div>
            <button class="btn btn-primary" onclick="location.href='perfil.php'">
                <i class="fas fa-arrow-left"></i> Volver al perfil
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
            <div class="main-content">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i> Mis Métodos de Pago
                </h2>

                <a href="agregar-pago.php" class="btn-add">
                    <i class="fas fa-plus"></i> Agregar nuevo método
                </a>

                <?php if (isset($_GET['exito'])): ?>
                    <div class="toast show">
                        <?php echo match($_GET['exito']) {
                            'agregado' => 'Método de pago agregado correctamente!',
                            'actualizado' => 'Método de pago actualizado correctamente!',
                            'eliminado' => 'Método de pago eliminado correctamente!',
                            default => 'Operación realizada con éxito!'
                        }; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="toast show error">
                        <?php echo match($_GET['error']) {
                            'general' => 'Ocurrió un error. Inténtalo de nuevo.',
                            'no_encontrado' => 'Método de pago no encontrado.',
                            default => 'Ocurrió un error inesperado.'
                        }; ?>
                    </div>
                <?php endif; ?>

                <div class="payment-methods">
    <?php if (count($metodos) > 0): ?>
        <?php foreach ($metodos as $metodo): ?>
            <div class="payment-card <?= $metodo['predeterminado'] ? 'default' : '' ?>">
                                <div class="payment-header">
                                    <div class="payment-type">
                                        <?php if ($metodo['tipo'] === 'tarjeta'): ?>
                                            <i class="fas fa-credit-card"></i> Tarjeta de crédito/débito
                                        <?php elseif ($metodo['tipo'] === 'paypal'): ?>
                                            <i class="fab fa-paypal"></i> PayPal
                                        <?php endif; ?>
                                    </div>
                                    <div class="payment-actions">
                                        <button class="btn-action" onclick="editarMetodo(<?php echo $metodo['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-action" onclick="eliminarMetodo(<?php echo $metodo['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="payment-details">
                                    <?php if ($metodo['tipo'] === 'tarjeta'): ?>
                                        <div class="payment-detail">
                                            <strong>Titular:</strong> <?php echo htmlspecialchars($metodo['titular']); ?>
                                        </div>
                                        <div class="payment-detail">
                                            <strong>Número:</strong> **** **** **** <?php echo substr($metodo['numero'], -4); ?>
                                        </div>
                                        <div class="payment-detail">
                                            <strong>Expira:</strong> <?php echo htmlspecialchars($metodo['expiracion']); ?>
                                        </div>
                                    <?php elseif ($metodo['tipo'] === 'paypal'): ?>
                                        <div class="payment-detail">
                                            <strong>Email:</strong> <?php echo htmlspecialchars($metodo['numero']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if ($metodo['predeterminado']): ?>
                                    <div class="default-tag">
                                        <i class="fas fa-star"></i> Predeterminado
                                    </div>
                                <?php else: ?>
                                    <span class="set-default" onclick="establecerPredeterminado(<?php echo $metodo['id']; ?>)">
                                        <i class="fas fa-star"></i> Establecer como predeterminado
                                    </span>
                                <?php endif; ?>
                             </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-payments">
            <i class="fas fa-credit-card"></i>
            <p>No tienes métodos de pago guardados</p>
        </div>
    <?php endif; ?>
</div>
            </div>
        </div>
    </div>

    <script>
        // Ocultar toast después de 3 segundos
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) toast.classList.remove('show');
        }, 3000);

        function editarMetodo(id) {
            window.location.href = `editar-pago.php?id=${id}`;
        }

        function eliminarMetodo(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este método de pago?')) {
                window.location.href = `procesar_pago.php?accion=eliminar&id=${id}`;
            }
        }

        function establecerPredeterminado(id) {
            window.location.href = `procesar_pago.php?accion=predeterminado&id=${id}`;
        }
    </script>
</body>
</html>