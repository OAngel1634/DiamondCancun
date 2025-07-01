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

// Obtener estadísticas del usuario (para la sidebar)
$database = new Database();
$pdo = $database->connect();

// Obtener reservas activas (para el badge)
$reservasActivas = 0;
$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                      WHERE usuario_id = ? AND estado IN ('pendiente', 'confirmada')");
$stmt->execute([$usuario->id]);
$reservasActivas = $stmt->fetchColumn();

// ... EL RESTO DE TU CÓDIGO ACTUAL DE PERFIL.PHP ...

$preferencias = $usuario->preferencias;

// Obtener preferencias del usuario
$stmt = $pdo->prepare("SELECT * FROM preferencias WHERE usuario_id = ?");
$stmt->execute([$usuario->id]);
$preferencias = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$preferencias) {
    $preferencias = [];
}

// Obtener datos del usuario
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);
$inicialAvatar = strtoupper(substr($nombreUsuario, 0, 1));

// Función para formatear fecha
function formatearFecha($fecha) {
    if ($fecha) {
        return date('d/m/Y', strtotime($fecha));
    }
    return 'No especificada';
}

// Obtener valores o mostrar 'No especificado' si no existen
$fechaNacimiento = $usuario->fecha_nacimiento;
$telefono = $usuario->telefono ? $usuario->telefono : 'No especificado';
$direccion = $usuario->direccion ? $usuario->direccion : 'No especificada';
$contactoEmergencia = $usuario->contacto_emergencia ? $usuario->contacto_emergencia : 'No especificado';
$genero = isset($preferencias['genero']) ? $preferencias['genero'] : 'No especificado';
$facilidades = isset($preferencias['facilidades_acceso']) ? $preferencias['facilidades_acceso'] : 'No especificadas';

// Preferencias adicionales
$notificacionesPref = isset($preferencias['notificaciones']) ? $preferencias['notificaciones'] : 'No especificado';
$idiomaPref = isset($preferencias['idioma']) ? $preferencias['idioma'] : 'No especificado';
$experienciaFav = isset($preferencias['experiencia_favorita']) ? $preferencias['experiencia_favorita'] : 'No especificado';
$prefGastronomicas = isset($preferencias['preferencias_gastronomicas']) ? $preferencias['preferencias_gastronomicas'] : 'No especificado';

// Calcular porcentaje de completado del perfil
$totalCampos = 8; // Campos importantes a completar
$camposCompletados = 0;

if (!empty($usuario->nombre)) $camposCompletados++;
if (!empty($usuario->email)) $camposCompletados++;
if (!empty($usuario->telefono)) $camposCompletados++;
if (!empty($usuario->direccion)) $camposCompletados++;
if (!empty($usuario->contacto_emergencia)) $camposCompletados++;
if (!empty($preferencias['genero'])) $camposCompletados++;
if (!empty($preferencias['facilidades_acceso'])) $camposCompletados++;
if (!empty($preferencias['preferencias_gastronomicas'])) $camposCompletados++;

$porcentaje = round(($camposCompletados / $totalCampos) * 100);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Diamond Bright</title>
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
        }

        .navigation li {
            display: flex;
            align-items: center;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
            margin-bottom: 5px;
        }

        /* Nuevos estilos para los enlaces */
        .navigation li a {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 12px;
            text-decoration: none;
            color: inherit;
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

        .user-name {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .verified-badge {
            background-color: var(--success);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 20px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-edit {
            background: transparent;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            padding: 8px 15px;
            font-size: 14px;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-edit:hover {
            background: var(--light-bg);
            border-color: var(--secondary);
            color: var(--secondary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .info-item {
            padding: 15px;
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .info-item:hover {
            border-color: var(--secondary);
            box-shadow: var(--shadow);
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .info-value {
            font-size: 16px;
            margin: 0;
            padding-left: 28px; /* Alineación con el ícono */
        }

        .no-info {
            color: var(--text-light);
            font-style: italic;
        }

        /* Progress Section */
        .profile-completion {
            background: var(--light-bg);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid var(--light-gray);
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .progress-bar {
            height: 10px;
            background: var(--light-gray);
            border-radius: 5px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent);
            border-radius: 5px;
            transition: var(--transition);
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 14px;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Mi Perfil - Diamond Bright</h1>
                <p>Administra tu información personal</p>
            </div>
            <button class="btn btn-primary" onclick="location.href='dashboard.php'">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </button>
        </div>

        <div class="content-wrapper">
            <!-- Sidebar copiada de dashboard.php -->
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
                        <a href="perfil.php" class="active">
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
                <h1 class="user-name">
                    <?php echo $nombreUsuario; ?>
                    <?php if ($usuario->verificado): ?>
                    <span class="verified-badge">
                        <i class="fas fa-check-circle"></i> Verificado
                    </span>
                    <?php endif; ?>
                </h1>
                
                <!-- Barra de progreso del perfil -->
                <div class="profile-completion">
                    <div class="progress-header">
                        <h3 class="section-title">
                            <i class="fas fa-tasks"></i> Compleción de tu perfil
                        </h3>
                        <span><?php echo $porcentaje; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
                    </div>
                    <div class="progress-info">
                        <span>Perfil básico</span>
                        <span>Completa tu perfil para mejores experiencias</span>
                    </div>
                </div>
                
                <!-- Información Básica -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-id-card"></i> Información Básica
                        </h2>
                        <button class="btn-edit" onclick="openEditModal('basic')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-user"></i> Nombre completo
                            </span>
                            <p class="info-value"><?php echo $nombreUsuario; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-birthday-cake"></i> Fecha de nacimiento
                            </span>
                            <p class="info-value"><?php echo formatearFecha($fechaNacimiento); ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-universal-access"></i> Facilidades de acceso
                            </span>
                            <p class="info-value"><?php echo $facilidades; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-venus-mars"></i> Género
                            </span>
                            <p class="info-value"><?php echo $genero; ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Datos de Contacto -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-address-book"></i> Datos de Contacto
                        </h2>
                        <button class="btn-edit" onclick="openEditModal('contact')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-mobile-alt"></i> Número de celular
                            </span>
                            <p class="info-value"><?php echo $telefono; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-exclamation-triangle"></i> Contacto de emergencias
                            </span>
                            <p class="info-value"><?php echo $contactoEmergencia; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-envelope"></i> Correo electrónico
                            </span>
                            <p class="info-value"><?php echo $emailUsuario; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-map-marker-alt"></i> Dirección
                            </span>
                            <p class="info-value"><?php echo $direccion; ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Preferencias -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fas fa-cog"></i> Preferencias
                        </h2>
                        <button class="btn-edit" onclick="openEditModal('preferences')">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-bell"></i> Notificaciones
                            </span>
                            <p class="info-value"><?php echo $notificacionesPref; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-language"></i> Idioma preferido
                            </span>
                            <p class="info-value"><?php echo $idiomaPref; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-ship"></i> Tipo de experiencia favorita
                            </span>
                            <p class="info-value"><?php echo $experienciaFav; ?></p>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">
                                <i class="fas fa-glass-cheers"></i> Preferencias gastronómicas
                            </span>
                            <p class="info-value"><?php echo $prefGastronomicas; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-footer">
            <p>© <?php echo date('Y'); ?> Diamond Bright Catamarans. Todos los derechos reservados.</p>
            <p>Tu información está protegida con nosotros. <a href="politica.php" style="color: var(--secondary); text-decoration: none;">Política de privacidad</a></p>
        </div>
    </div>

    <!-- Toast para mensajes -->
    <div class="toast" id="toast"></div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Función para mostrar toast
        function showToast(message, isSuccess = true) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.backgroundColor = isSuccess ? 'var(--success)' : 'var(--danger)';
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    });
    
    // Función para redirigir a editar-perfil.php
    function openEditModal(section) {
        // Mapear secciones a los nombres que espera editar-perfil.php
        const sectionMap = {
            'basic': 'basica',
            'contact': 'contacto',
            'preferences': 'preferencias'
        };
        
        window.location.href = `editar-perfil.php?seccion=${sectionMap[section]}`;
    }
</script>
</body>
</html>