<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

$isProduction = ($_ENV['RAILWAY_ENVIRONMENT'] ?? $_ENV['NODE_ENV'] ?? 'development') === 'production';
$isLocal = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' || 
           ($_SERVER['SERVER_ADDR'] ?? '') === '127.0.0.1' ||
           str_contains($_SERVER['HTTP_HOST'] ?? '', '.local');

if ($isProduction && !$isLocal && 
    (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') &&
    ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'http') !== 'https') {
    $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirectUrl");
    exit();
}
+
session_start([
    'cookie_path' => '/',
    'cookie_secure' => $isProduction,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cache_limiter' => 'nocache'
]);

if (isset($_SESSION['usuario_id']) && is_numeric($_SESSION['usuario_id'])) {
    header("Location: /dashboard.php");
    exit();
}

$error = null;
$emailVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    // Validar y limpiar datos
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $emailVal = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, introduce un correo electrónico válido";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres";
    } else {
        
        try {
            require_once __DIR__ . '/../includes/conexion.php';
            
            $stmt = $conn->prepare("SELECT id, email, password, nombre, activo FROM usuarios WHERE email = ? AND activo = 1 LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $usuario = $result->fetch_assoc();
                
                if (password_verify($password, $usuario['password'])) {
                    // Registrar intento exitoso
                    if ($isProduction) {
                        $updateStmt = $conn->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?");
                        $updateStmt->bind_param("i", $usuario['id']);
                        $updateStmt->execute();
                        $updateStmt->close();
                    }
                    
                    $_SESSION['usuario_id'] = (int)$usuario['id'];
                    $_SESSION['usuario_email'] = htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8');
                    $_SESSION['usuario_nombre'] = htmlspecialchars($usuario['nombre'], ENT_QUOTES, 'UTF-8');
                    $_SESSION['login_time'] = time();
                    
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    
                    header("Location: /dashboard.php");
                    exit();
                }
            }
            
            if ($isProduction) {
                
            }
            
            $error = "Correo electrónico o contraseña incorrectos";
            $stmt->close();
            
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = $isProduction ? 
                "Error del sistema. Por favor, intenta más tarde." : 
                "Error de conexión: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inicia sesión en Diamond Bright Catamarans">
    <meta name="robots" content="noindex, nofollow">
    <title>Iniciar Sesión - Diamond Bright Catamarans</title>
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    
    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-p1CmWvQg2cL0+9J1Nc9MvdSEZHt+6iweMn5LhI5UUl/FUWFuRFu8r9ZtOtjmCl8pq23THPCAAUeHz6D3Ym0hA==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer">

     <link rel="stylesheet" href="/css/login.css?v=<?php echo time(); ?>">
    
</head>

<body>
   
    <a href="#form-container" class="sr-only">Saltar al formulario de inicio de sesión</a>
    
    <div class="app-container">
     
        <div class="brand-section">
            <div class="brand-header">
                <div class="logo" aria-label="Diamond Bright Catamarans">
                    <div class="logo-icon" aria-hidden="true">
                        <i class="fas fa-ship"></i>
                    </div>
                    <div class="logo-text">DIAMOND BRIGHT</div>
                </div>
                <h1 class="brand-title">Explora las aguas cristalinas de Isla Mujeres</h1>
                <p class="brand-subtitle">
                    Vive la experiencia de navegar en nuestros catamaranes de lujo y descubre 
                    la belleza del Caribe Mexicano con Diamond Bright.
                </p>
            </div>
            <div class="brand-image" aria-hidden="true">
                <i class="fas fa-sailboat sailboat-icon"></i>
            </div>
            <div class="brand-footer">
                © <?php echo date('Y'); ?> Diamond Bright Catamarans. Todos los derechos reservados.
            </div>
        </div>

        <div class="login-section">
            <div class="login-header">
                <h2 class="login-title">Bienvenido de nuevo</h2>
                <p class="login-subtitle">Inicia sesión para acceder a tu cuenta</p>
            </div>

            <div class="form-container" id="form-container">
                <?php if ($error): ?>
                    <div class="error-message" role="alert" aria-live="assertive">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <form id="loginForm" method="post" novalidate>
                    <div class="input-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               placeholder="tu@email.com" 
                               required
                               value="<?php echo htmlspecialchars($emailVal); ?>"
                               aria-describedby="email-help"
                               autocomplete="email">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <div id="email-help" class="sr-only">Introduce tu dirección de correo electrónico</div>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required
                               minlength="8"
                               autocomplete="current-password"
                               aria-describedby="password-help">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div id="password-help" class="sr-only">La contraseña debe tener al menos 8 caracteres</div>
                    </div>

                    <div class="form-options">
                        <label class="remember">
                            <input type="checkbox" id="remember" name="remember" value="1">
                            <span>Recordar sesión</span>
                        </label>
                        <a href="/html/recuperar-password.php" class="forgot-link">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="btnText">Continuar</span>
                        <span class="btn-spinner" id="btnSpinner" aria-hidden="true"></span>
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                    </button>
                </form>

                <p class="register-link">
                    ¿No tienes una cuenta? 
                    <a href="/html/registro.php" id="showRegister">
                        Regístrate ahora
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const emailInput = document.getElementById('email');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                    this.setAttribute('aria-label', type === 'text' ? 'Ocultar contraseña' : 'Mostrar contraseña');
                });
            }

            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    this.setCustomValidity('');
                    if (!this.validity.valid) {
                        this.setCustomValidity('Por favor, introduce un correo electrónico válido');
                    }
                });
            }

            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }

                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'block';
                    submitBtn.disabled = true;
                    submitBtn.setAttribute('aria-busy', 'true');

                    setTimeout(() => {
                        this.submit();
                    }, 500);
                });
            }

            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
        });
    </script>
</body>
</html>