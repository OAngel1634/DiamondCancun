<?php
session_start();
require_once 'Usuario.php';
require_once 'conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

// Obtener información del usuario
$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

// Preparar datos para la vista
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));

// Mensajes
$mensaje = '';
if (isset($_GET['exito'])) {
    $mensaje = '¡Contraseña actualizada correctamente!';
} elseif (isset($_GET['error'])) {
    $mensaje = match ($_GET['error']) {
        'actual' => 'La contraseña actual es incorrecta',
        'coinciden' => 'Las nuevas contraseñas no coinciden',
        'campos' => 'Por favor completa todos los campos',
        default => 'Ocurrió un error al actualizar la contraseña'
    };
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguridad - Diamond Bright</title>
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

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border-left: 4px solid var(--success);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .security-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Seguridad - Diamond Bright</h1>
            </div>
            <button class="btn btn-primary" onclick="location.href='dashboard.php'">
                <i class="fas fa-arrow-left"></i> Volver al panel
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
                        <a href="seguridad.php" class="active">
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

            <div class="main-content">
                <h2 class="section-title">
                    <i class="fas fa-shield-alt"></i> Configuración de Seguridad
                </h2>

                <?php if ($mensaje): ?>
                    <?php if (isset($_GET['exito'])): ?>
                        <div class="success-message">
                            <i class="fas fa-check-circle"></i> <?php echo $mensaje; ?>
                        </div>
                    <?php else: ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form class="security-form" action="procesar_seguridad.php" method="POST">
                    <div class="form-group">
                        <label for="password_actual"><i class="fas fa-lock"></i> Contraseña Actual</label>
                        <input type="password" id="password_actual" name="password_actual" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nueva_password"><i class="fas fa-key"></i> Nueva Contraseña</label>
                        <input type="password" id="nueva_password" name="nueva_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmar_password"><i class="fas fa-key"></i> Confirmar Nueva Contraseña</label>
                        <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-cancel" onclick="location.href='dashboard.php'">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>