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
    
    <style>
        :root {
            --primary-blue: #003366;
            --secondary-blue: #0099cc;
            --accent-gold: #D4AF37;
            --light-bg: #f0f8ff;
            --white: #ffffff;
            --light-gray: #e0e0e0;
            --text-dark: #333333;
            --text-light: #666666;
            --shadow: 0 8px 20px rgba(0, 51, 102, 0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --error-color: #dc3545;
            --success-color: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0, 153, 204, 0.1) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            max-width: 1200px;
            width: 100%;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--white);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--white);
            position: relative;
            overflow: hidden;
            min-height: 600px;
        }

        .brand-section::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
        }

        .brand-section::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 15s infinite ease-in-out reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(20px, 20px); }
        }

        .brand-header {
            position: relative;
            z-index: 2;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 32px;
            color: var(--accent-gold);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .brand-title {
            font-size: 36px;
            margin-bottom: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.7;
            max-width: 400px;
        }

        .brand-image {
            text-align: center;
            position: relative;
            z-index: 2;
            margin: 40px 0;
        }

        .sailboat-icon {
            font-size: 180px;
            color: var(--accent-gold);
            opacity: 0.8;
            filter: drop-shadow(0 5px 15px rgba(212, 175, 55, 0.3));
        }

        .brand-footer {
            position: relative;
            z-index: 2;
            font-size: 14px;
            opacity: 0.8;
            margin-top: 20px;
        }

        .login-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .login-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .login-title {
            font-size: 32px;
            color: var(--primary-blue);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .login-subtitle {
            color: var(--text-light);
            font-size: 16px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Mensajes de error */
        .error-message {
            background-color: #fff5f5;
            color: var(--error-color);
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            border: 1px solid #fed7d7;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .error-message i {
            font-size: 18px;
        }

        .input-group {
            margin-bottom: 28px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 15px;
        }

        .input-group input {
            width: 100%;
            height: 56px;
            padding: 0 20px;
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            font-size: 16px;
            transition: var(--transition);
            background: #fafafa;
        }

        .input-group input:focus {
            border-color: var(--secondary-blue);
            background: var(--white);
            outline: none;
            box-shadow: 0 0 0 4px rgba(0, 153, 204, 0.15);
        }

        .input-group input:invalid:not(:focus):not(:placeholder-shown) {
            border-color: var(--error-color);
        }

        .input-group i {
            position: absolute;
            right: 20px;
            top: 44px;
            color: var(--text-light);
            pointer-events: none;
        }

        .password-toggle {
            position: absolute;
            right: 20px;
            top: 44px;
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .password-toggle:hover {
            color: var(--primary-blue);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
        }

        .remember input[type="checkbox"] {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid var(--light-gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .remember input[type="checkbox"]:checked {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
        }

        .forgot-link {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: var(--primary-blue);
        }

        .btn {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            color: var(--white);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover:not(:disabled),
        .btn-primary:focus-visible:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 51, 102, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--white);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .register-link {
            text-align: center;
            font-size: 15px;
            color: var(--text-light);
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--light-gray);
        }

        .register-link a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
            position: relative;
            padding: 4px 0;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-gold);
            transition: width 0.3s ease;
        }

        .register-link a:hover {
            color: var(--primary-blue);
        }

        .register-link a:hover::after {
            width: 100%;
        }

        @media (max-width: 992px) {
            .app-container {
                flex-direction: column;
                max-width: 600px;
            }

            .brand-section {
                min-height: 400px;
                padding: 30px;
            }

            .brand-title {
                font-size: 28px;
            }

            .sailboat-icon {
                font-size: 120px;
            }

            .login-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .app-container {
                border-radius: 16px;
            }

            .brand-section,
            .login-section {
                padding: 24px;
            }

            .logo {
                margin-bottom: 30px;
            }

            .logo-text {
                font-size: 24px;
            }

            .brand-title {
                font-size: 24px;
            }

            .brand-subtitle {
                font-size: 16px;
            }

            .login-title {
                font-size: 28px;
            }

            .input-group input {
                height: 52px;
                padding: 0 16px;
            }

            .btn {
                height: 52px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

    
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        *:focus-visible {
            outline: 3px solid var(--accent-gold);
            outline-offset: 2px;
        }
    </style>
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