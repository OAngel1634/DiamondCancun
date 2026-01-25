<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

// Detección de entorno (consistente con login/registro)
$isProduction = ($_ENV['RAILWAY_ENVIRONMENT'] ?? $_ENV['NODE_ENV'] ?? 'development') === 'production';
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || 
           ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1' ||
           str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

// Redirección HTTPS en producción
if ($isProduction && !$isLocal && 
    (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') &&
    ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') !== 'https') {
    $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirectUrl");
    exit();
}

// Configuración segura de sesiones (consistente con login/registro)
session_start([
    'cookie_path' => '/',
    'cookie_secure' => $isProduction,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cache_limiter' => 'nocache'
]);

// Headers de seguridad adicionales
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Logging de acceso (solo en desarrollo)
if (!$isProduction) {
    error_log("Accediendo a dashboard.php. ID de sesión: " . session_id());
    error_log("Usuario ID en sesión: " . ($_SESSION['usuario_id'] ?? 'No definido'));
}

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || !is_numeric($_SESSION['usuario_id'])) {
    if (!$isProduction) {
        error_log("Redirigiendo a inicio-sesion.php desde dashboard.php - Sesión inválida");
    }
    header("Location: /html/inicio-sesion.php");
    exit();
}

// Verificar tiempo de sesión (24 horas máximo)
$sessionTimeout = 86400; // 24 horas
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionTimeout) {
    session_destroy();
    header("Location: /html/inicio-sesion.php?expired=1");
    exit();
}

// Verificar token CSRF (si existe en sesión)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inicializar variables
$nombreUsuario = '';
$emailUsuario = '';
$inicialAvatar = '';
$reservasActivas = 0;
$valoracionPromedio = 0;
$viajesRealizados = 0;
$cuponesDisponibles = 0;

try {
    // Obtener información del usuario
    require_once __DIR__ . '/Usuario.php';
    require_once __DIR__ . '/conexion.php';
    
    $usuario = new Usuario();
    $usuarioData = $usuario->buscarPorId((int)$_SESSION['usuario_id']);
    
    if (!$usuarioData) {
        throw new Exception("Usuario no encontrado");
    }
    
    // Verificar que el usuario esté activo
    if (isset($usuario->activo) && $usuario->activo != 1) {
        session_destroy();
        header("Location: /html/inicio-sesion.php?inactive=1");
        exit();
    }
    
    // Obtener datos del usuario
    $nombreUsuario = htmlspecialchars($usuario->nombre ?? '', ENT_QUOTES, 'UTF-8');
    $emailUsuario = htmlspecialchars($usuario->email ?? '', ENT_QUOTES, 'UTF-8');
    $inicialAvatar = !empty($nombreUsuario) ? strtoupper(substr($nombreUsuario, 0, 1)) : 'U';
    
    // Conectar a la base de datos
    $database = new Database();
    $pdo = $database->connect();
    
    // Obtener estadísticas del usuario con prepared statements
    // Reservas activas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                          WHERE usuario_id = ? AND estado IN ('pendiente', 'confirmada', 'procesando')");
    $stmt->execute([$_SESSION['usuario_id']]);
    $reservasActivas = (int)$stmt->fetchColumn();
    
    // Valoración promedio
    $stmt = $pdo->prepare("SELECT AVG(calificacion) FROM opiniones WHERE usuario_id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $valoracionPromedio = $stmt->fetchColumn();
    $valoracionPromedio = $valoracionPromedio ? number_format((float)$valoracionPromedio, 1) : '0.0';
    
    // Viajes realizados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservas 
                          WHERE usuario_id = ? AND estado = 'completada'");
    $stmt->execute([$_SESSION['usuario_id']]);
    $viajesRealizados = (int)$stmt->fetchColumn();
    
    // Cupones disponibles (con manejo de tabla inexistente)
    try {
        // Verificar si la tabla existe
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'cupones'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cupones 
                                  WHERE usuario_id = ? AND usado = 0 AND fecha_expiracion > NOW()");
            $stmt->execute([$_SESSION['usuario_id']]);
            $cuponesDisponibles = (int)$stmt->fetchColumn();
        }
    } catch (PDOException $e) {
        // Silenciar error de tabla no existente
        if (!$isProduction) {
            error_log("Tabla cupones no existe o error: " . $e->getMessage());
        }
        $cuponesDisponibles = 0;
    }
    
    // Actualizar último acceso
    if ($isProduction) {
        $updateStmt = $pdo->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
        $updateStmt->execute([$_SESSION['usuario_id']]);
    }
    
} catch (Exception $e) {
    error_log("Error en dashboard: " . $e->getMessage());
    if (!$isProduction) {
        die("Error del sistema: " . htmlspecialchars($e->getMessage()));
    } else {
        die("Error del sistema. Por favor, intente más tarde.");
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de usuario - Diamond Bright Catamarans">
    <meta name="robots" content="noindex, nofollow">
    <title>Panel de Usuario - Diamond Bright Catamarans</title>
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/css/dashboard.css?v=<?php echo time(); ?>">

</head>
<body>
    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">
        <i class="fas fa-bars"></i>
    </button>

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand-logo">
                    <i class="fas fa-ship"></i>
                    <span>DIAMOND BRIGHT</span>
                </div>
                
                <div class="user-card">
                    <div class="avatar" aria-label="Avatar de <?php echo $nombreUsuario; ?>">
                        <?php echo $inicialAvatar; ?>
                    </div>
                    <h2><?php echo $nombreUsuario; ?></h2>
                    <p class="email" title="<?php echo $emailUsuario; ?>">
                        <?php echo $emailUsuario; ?>
                    </p>
                </div>
            </div>

            <nav aria-label="Navegación principal">
                <ul class="navigation">
                    <li>
                        <a href="/dashboard.php" class="active">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="text">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/perfil.php">
                            <i class="fas fa-user"></i>
                            <span class="text">Mi Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/reservas.php">
                            <i class="fas fa-calendar-check"></i>
                            <span class="text">Mis Reservas</span>
                            <?php if ($reservasActivas > 0): ?>
                                <span class="badge"><?php echo $reservasActivas; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="/html/pagos.php">
                            <i class="fas fa-credit-card"></i>
                            <span class="text">Métodos de Pago</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/opiniones.php">
                            <i class="fas fa-star"></i>
                            <span class="text">Mis Opiniones</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/favoritos.php">
                            <i class="fas fa-heart"></i>
                            <span class="text">Favoritos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/seguridad.php">
                            <i class="fas fa-shield-alt"></i>
                            <span class="text">Seguridad</span>
                        </a>
                    </li>
                    <li>
                        <a href="/html/ayuda.php">
                            <i class="fas fa-question-circle"></i>
                            <span class="text">Ayuda</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="/html/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <header class="main-header">
                <div class="welcome-section">
                    <h1>Bienvenido de nuevo, <?php echo $nombreUsuario; ?>!</h1>
                    <p>Aquí puedes gestionar todas tus actividades y reservas</p>
                </div>
                
                <div class="quick-actions">
                    <a href="/html/nueva-reserva.php" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i>
                        Nueva Reserva
                    </a>
                    <a href="/" class="btn btn-outline">
                        <i class="fas fa-home"></i>
                        Ir al Inicio
                    </a>
                </div>
            </header>

            <!-- Estadísticas -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-chart-line"></i> Mis Estadísticas</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $reservasActivas; ?></div>
                        <div class="stat-label">Reservas Activas</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-number"><?php echo $valoracionPromedio; ?>/5</div>
                        <div class="stat-label">Valoración Promedio</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $cuponesDisponibles; ?></div>
                        <div class="stat-label">Cupones Disponibles</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="stat-number"><?php echo $viajesRealizados; ?></div>
                        <div class="stat-label">Viajes Realizados</div>
                    </div>
                </div>
            </section>

            <!-- Acciones Rápidas -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-bolt"></i> Acciones Rápidas</h2>
                </div>
                
                <div class="actions-grid">
                    <a href="/html/nueva-reserva.php" class="action-card">
                        <i class="fas fa-calendar-plus"></i>
                        <h3>Nueva Reserva</h3>
                        <p>Reserva tu próximo viaje</p>
                    </a>
                    
                    <a href="/html/perfil.php" class="action-card">
                        <i class="fas fa-user-edit"></i>
                        <h3>Editar Perfil</h3>
                        <p>Actualiza tu información</p>
                    </a>
                    
                    <a href="/html/pagos.php" class="action-card">
                        <i class="fas fa-credit-card"></i>
                        <h3>Gestionar Pagos</h3>
                        <p>Métodos de pago</p>
                    </a>
                    
                    <a href="/html/favoritos.php" class="action-card">
                        <i class="fas fa-heart"></i>
                        <h3>Mis Favoritos</h3>
                        <p>Ver favoritos guardados</p>
                    </a>
                    
                    <a href="/html/tours.php" class="action-card">
                        <i class="fas fa-binoculars"></i>
                        <h3>Explorar Tours</h3>
                        <p>Descubrir nuevos viajes</p>
                    </a>
                    
                    <a href="/html/ayuda.php" class="action-card">
                        <i class="fas fa-question-circle"></i>
                        <h3>Centro de Ayuda</h3>
                        <p>Soporte y preguntas</p>
                    </a>
                </div>
            </section>

            <!-- Última Actividad -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-history"></i> Actividad Reciente</h2>
                </div>
                
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Inicio de sesión exitoso</h4>
                            <p>Sesión iniciada desde <?php echo htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'IP desconocida'); ?></p>
                        </div>
                        <div class="activity-time">
                            Justo ahora
                        </div>
                    </li>
                    
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-ship"></i>
                        </div>
                        <div class="activity-content">
                            <h4><?php echo $viajesRealizados > 0 ? 'Último viaje completado' : 'Aún no has realizado viajes'; ?></h4>
                            <p><?php echo $viajesRealizados > 0 ? 'Viaje #' . $viajesRealizados : 'Reserva tu primer viaje ahora'; ?></p>
                        </div>
                        <div class="activity-time">
                            <?php echo $viajesRealizados > 0 ? 'Hace 2 semanas' : '---'; ?>
                        </div>
                    </li>
                    
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="activity-content">
                            <h4>Tu valoración actual</h4>
                            <p><?php echo $valoracionPromedio; ?> de 5 estrellas</p>
                        </div>
                        <div class="activity-time">
                            Actualizado
                        </div>
                    </li>
                </ul>
            </section>

            <footer class="dashboard-footer">
                <p>© <?php echo date('Y'); ?> Diamond Bright Catamarans. Todos los derechos reservados.</p>
                <p>
                    <a href="/html/politica-privacidad.php" style="color: var(--secondary-blue); text-decoration: none; margin-right: 15px;">
                        Política de Privacidad
                    </a>
                    <a href="/html/terminos.php" style="color: var(--secondary-blue); text-decoration: none;">
                        Términos y Condiciones
                    </a>
                </p>
            </footer>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            // Toggle sidebar en mobile
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    const icon = this.querySelector('i');
                    if (sidebar.classList.contains('open')) {
                        icon.className = 'fas fa-times';
                        this.setAttribute('aria-label', 'Cerrar menú');
                    } else {
                        icon.className = 'fas fa-bars';
                        this.setAttribute('aria-label', 'Abrir menú');
                    }
                });
            }
            
            // Cerrar sidebar al hacer clic fuera en mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && sidebar && menuToggle) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = menuToggle.contains(event.target);
                    
                    if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('open')) {
                        sidebar.classList.remove('open');
                        menuToggle.querySelector('i').className = 'fas fa-bars';
                        menuToggle.setAttribute('aria-label', 'Abrir menú');
                    }
                }
            });
            
            // Manejar navegación activa
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.navigation li a');
            
            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (currentPage === href || (currentPage === '' && href === '/dashboard.php')) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
            
            // Prevenir reenvío de formularios
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Actualizar tiempo de actividad
            function updateActivityTime() {
                const activityTime = document.querySelector('.activity-time');
                if (activityTime && activityTime.textContent === 'Justo ahora') {
                    setTimeout(() => {
                        activityTime.textContent = 'Hace unos minutos';
                    }, 60000); // 1 minuto
                }
            }
            
            updateActivityTime();
        });
    </script>
</body>
</html>