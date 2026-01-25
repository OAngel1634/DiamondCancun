<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

// Detección de entorno similar al login
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

// Configuración segura de sesiones (similar al login)
session_start([
    'cookie_path' => '/',
    'cookie_secure' => $isProduction,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'use_only_cookies' => true,
    'cache_limiter' => 'nocache'
]);

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id']) && is_numeric($_SESSION['usuario_id'])) {
    header("Location: /dashboard.php");
    exit();
}

// Headers de seguridad adicionales
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Inicializar variables
$error = '';
$inputValues = [
    'nombre' => '',
    'email' => ''
];
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    try {
        // Verificar CSRF token
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Token de seguridad inválido. Por favor, recarga la página.");
        }
        
        // Rate limiting básico
        if (!isset($_SESSION['reg_attempts'])) {
            $_SESSION['reg_attempts'] = 0;
            $_SESSION['reg_last_attempt'] = time();
        }
        
        $currentTime = time();
        if ($_SESSION['reg_attempts'] > 5 && ($currentTime - $_SESSION['reg_last_attempt']) < 300) {
            throw new Exception("Demasiados intentos. Por favor, espera 5 minutos.");
        }
        
        // Sanitizar y validar entradas
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Guardar valores para repoblar formulario (con sanitización adicional)
        $inputValues['nombre'] = htmlspecialchars($nombre ?? '', ENT_QUOTES, 'UTF-8');
        $inputValues['email'] = htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8');
        
        // Validaciones
        if (empty($nombre) || strlen(trim($nombre)) < 2) {
            throw new Exception("El nombre debe tener al menos 2 caracteres");
        }
        
        if (strlen($nombre) > 100) {
            throw new Exception("El nombre es demasiado largo (máximo 100 caracteres)");
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Por favor, introduce un email válido");
        }
        
        if (strlen($email) > 255) {
            throw new Exception("El email es demasiado largo");
        }
        
        if (strlen($password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres");
        }
        
        if (strlen($password) > 72) { // Límite para bcrypt
            throw new Exception("La contraseña es demasiado larga (máximo 72 caracteres)");
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra mayúscula");
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un número");
        }
        
        if (!preg_match('/[\W_]/', $password)) { // Carácter especial
            throw new Exception("La contraseña debe contener al menos un carácter especial");
        }
        
        if ($password !== $confirm_password) {
            throw new Exception("Las contraseñas no coinciden");
        }
        
        if (empty($_POST['terms'])) {
            throw new Exception("Debes aceptar los términos y condiciones");
        }
        
        // Verificar si el usuario ya existe
        require_once __DIR__ . '/../includes/conexion.php';
        
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Este email ya está registrado");
        }
        $stmt->close();
        
        // Crear hash de contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Limpiar contraseña de memoria
        unset($password, $confirm_password);
        
        // Insertar usuario
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, activo, fecha_registro) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param("sss", $nombre, $email, $passwordHash);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al crear el usuario. Por favor, intenta nuevamente.");
        }
        
        $usuario_id = $stmt->insert_id;
        $stmt->close();
        
        // Registrar éxito
        if ($isProduction) {
            error_log("Registro exitoso - Usuario ID: $usuario_id, Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
        }
        
        // Resetear intentos de registro
        $_SESSION['reg_attempts'] = 0;
        
        // Establecer sesión (similar al login)
        $_SESSION['usuario_id'] = (int)$usuario_id;
        $_SESSION['usuario_email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $_SESSION['usuario_nombre'] = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $_SESSION['login_time'] = time();
        
        // Generar nuevo token CSRF para la sesión
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        // Redirigir al dashboard
        header("Location: /dashboard.php");
        exit();
        
    } catch (Exception $e) {
        // Incrementar contador de intentos fallidos
        if (!isset($_SESSION['reg_attempts'])) {
            $_SESSION['reg_attempts'] = 1;
        } else {
            $_SESSION['reg_attempts']++;
        }
        $_SESSION['reg_last_attempt'] = time();
        
        $error = $e->getMessage();
        error_log("Error en registro: " . $e->getMessage() . " - IP: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
        
        if ($isProduction && !str_contains($error, 'Token de seguridad') && !str_contains($error, 'Demasiados intentos')) {
            $error = "Error en el registro. Por favor, verifica tus datos.";
        }
    }
}

// Asegurar que $error sea string
$error = (string)$error;
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Regístrate en Diamond Bright Catamarans">
    <meta name="robots" content="noindex, nofollow">
    <title>Registro - Diamond Bright Catamarans</title>
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            --warning-color: #ff9800;
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

        .register-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .register-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .register-title {
            font-size: 32px;
            color: var(--primary-blue);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .register-subtitle {
            color: var(--text-light);
            font-size: 16px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .error-message {
            background-color: #fff5f5;
            color: var(--error-color);
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            text-align: center;
            border: 1px solid #fed7d7;
            font-size: 15px;
            display: <?= $error ? 'flex' : 'none' ?>;
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

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
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

        .password-strength {
            margin-top: 8px;
            font-size: 14px;
        }

        .strength-meter {
            height: 6px;
            background: #eee;
            border-radius: 3px;
            margin: 8px 0;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .strength-text {
            font-size: 13px;
            font-weight: 500;
        }

        .requirements {
            margin-top: 12px;
            font-size: 13px;
            color: var(--text-light);
        }

        .requirement {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .requirement i {
            font-size: 12px;
            width: 16px;
        }

        .requirement.met {
            color: var(--success-color);
        }

        .requirement.unmet {
            color: var(--text-light);
        }

        .terms {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 24px 0;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 12px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .terms input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            cursor: pointer;
        }

        .terms a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .terms a:hover {
            text-decoration: underline;
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

        .login-link {
            text-align: center;
            font-size: 15px;
            color: var(--text-light);
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--light-gray);
        }

        .login-link a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
            position: relative;
            padding: 4px 0;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-gold);
            transition: width 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-blue);
        }

        .login-link a:hover::after {
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

            .register-section {
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
            .register-section {
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

            .register-title {
                font-size: 28px;
            }

            .input-group input {
                height: 52px;
                padding: 0 16px;
            }

            .btn {
                height: 52px;
            }

            .terms {
                flex-direction: column;
                align-items: flex-start;
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
    <a href="#form-container" class="sr-only">Saltar al formulario de registro</a>
    
    <div class="app-container">
        <div class="brand-section">
            <div class="brand-header">
                <div class="logo" aria-label="Diamond Bright Catamarans">
                    <div class="logo-icon" aria-hidden="true">
                        <i class="fas fa-ship"></i>
                    </div>
                    <div class="logo-text">DIAMOND BRIGHT</div>
                </div>
                <h1 class="brand-title">Únete a nuestra comunidad náutica</h1>
                <p class="brand-subtitle">
                    Regístrate para reservar catamaranes de lujo, acceder a ofertas exclusivas y vivir experiencias únicas en el Caribe.
                </p>
            </div>
            <div class="brand-image" aria-hidden="true">
                <i class="fas fa-sailboat sailboat-icon"></i>
            </div>
            <div class="brand-footer">
                © <?php echo date('Y'); ?> Diamond Bright Catamarans. Todos los derechos reservados.
            </div>
        </div>

        <div class="register-section">
            <div class="register-header">
                <h2 class="register-title">Crear cuenta</h2>
                <p class="register-subtitle">Regístrate para empezar tu aventura</p>
            </div>

            <div class="form-container" id="form-container">
                <?php if ($error): ?>
                    <div class="error-message" role="alert" aria-live="assertive">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                <?php endif; ?>

                <form id="registrationForm" method="post" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="input-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               required
                               minlength="2"
                               maxlength="100"
                               value="<?php echo htmlspecialchars($inputValues['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                               aria-describedby="nombre-help">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <div id="nombre-help" class="sr-only">Introduce tu nombre completo</div>
                    </div>

                    <div class="input-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required
                               maxlength="255"
                               value="<?php echo htmlspecialchars($inputValues['email'], ENT_QUOTES, 'UTF-8'); ?>"
                               aria-describedby="email-help">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                        <div id="email-help" class="sr-only">Introduce tu dirección de correo electrónico</div>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               minlength="8"
                               maxlength="72"
                               autocomplete="new-password"
                               aria-describedby="password-help password-requirements">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div id="password-help" class="sr-only">La contraseña debe tener al menos 8 caracteres</div>
                        
                        <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Seguridad: muy débil</div>
                        </div>
                        
                        <div class="requirements" id="passwordRequirements">
                            <div class="requirement unmet" id="reqLength">
                                <i class="fas fa-circle" id="reqLengthIcon"></i>
                                <span>Al menos 8 caracteres</span>
                            </div>
                            <div class="requirement unmet" id="reqUppercase">
                                <i class="fas fa-circle" id="reqUppercaseIcon"></i>
                                <span>Una letra mayúscula</span>
                            </div>
                            <div class="requirement unmet" id="reqNumber">
                                <i class="fas fa-circle" id="reqNumberIcon"></i>
                                <span>Un número</span>
                            </div>
                            <div class="requirement unmet" id="reqSpecial">
                                <i class="fas fa-circle" id="reqSpecialIcon"></i>
                                <span>Un carácter especial</span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               required
                               minlength="8"
                               maxlength="72"
                               autocomplete="new-password"
                               aria-describedby="confirm-help">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <button type="button" class="password-toggle" id="toggleConfirmPassword" aria-label="Mostrar contraseña">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div id="confirm-help" class="sr-only">Repite tu contraseña para confirmar</div>
                    </div>

                    <div class="terms">
                        <input type="checkbox" id="terms" name="terms" value="1" required>
                        <label for="terms">
                            Acepto los <a href="/html/terminos.php" target="_blank" rel="noopener noreferrer">términos y condiciones</a> 
                            y la <a href="/html/privacidad.php" target="_blank" rel="noopener noreferrer">política de privacidad</a>
                        </label>
                    </div>

                    <button type="submit" name="registro" class="btn btn-primary" id="submitBtn">
                        <span id="btnText">Crear cuenta</span>
                        <span class="btn-spinner" id="btnSpinner" aria-hidden="true"></span>
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                    </button>
                </form>

                <p class="login-link">
                    ¿Ya tienes una cuenta? 
                    <a href="/html/inicio-sesion.php" id="showLogin">
                        Inicia sesión
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            // Toggle visibilidad de contraseña
            function setupPasswordToggle(toggleBtn, inputField) {
                if (toggleBtn && inputField) {
                    toggleBtn.addEventListener('click', function() {
                        const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
                        inputField.setAttribute('type', type);
                        const icon = this.querySelector('i');
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                        this.setAttribute('aria-label', type === 'text' ? 'Ocultar contraseña' : 'Mostrar contraseña');
                    });
                }
            }
            
            setupPasswordToggle(togglePassword, passwordInput);
            setupPasswordToggle(toggleConfirmPassword, confirmPasswordInput);
            
            // Validación de fortaleza de contraseña
            function checkPasswordStrength(password) {
                let strength = 0;
                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[\W_]/.test(password)
                };
                
                // Calcular fuerza
                Object.values(requirements).forEach(req => {
                    if (req) strength += 1;
                });
                
                // Actualizar UI
                updateStrengthIndicator(strength, requirements);
                
                return requirements;
            }
            
            function updateStrengthIndicator(strength, requirements) {
                const colors = {
                    0: '#d32f2f',
                    1: '#d32f2f',
                    2: '#ff9800',
                    3: '#4caf50',
                    4: '#2e7d32'
                };
                
                const texts = {
                    0: 'Muy débil',
                    1: 'Muy débil',
                    2: 'Débil',
                    3: 'Buena',
                    4: 'Fuerte'
                };
                
                const width = strength * 25;
                strengthFill.style.width = `${width}%`;
                strengthFill.style.backgroundColor = colors[strength];
                strengthText.textContent = `Seguridad: ${texts[strength]}`;
                strengthText.style.color = colors[strength];
                
                // Actualizar requerimientos
                updateRequirement('length', requirements.length);
                updateRequirement('uppercase', requirements.uppercase);
                updateRequirement('number', requirements.number);
                updateRequirement('special', requirements.special);
            }
            
            function updateRequirement(type, met) {
                const element = document.getElementById(`req${type.charAt(0).toUpperCase() + type.slice(1)}`);
                const icon = document.getElementById(`req${type.charAt(0).toUpperCase() + type.slice(1)}Icon`);
                
                if (element && icon) {
                    element.classList.toggle('met', met);
                    element.classList.toggle('unmet', !met);
                    icon.style.color = met ? '#28a745' : '#666666';
                    icon.className = met ? 'fas fa-check-circle' : 'fas fa-circle';
                }
            }
            
            // Event listeners
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    
                    // Validar coincidencia de contraseñas
                    if (confirmPasswordInput.value) {
                        validatePasswordMatch();
                    }
                });
            }
            
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
            }
            
            function validatePasswordMatch() {
                const password = passwordInput.value;
                const confirm = confirmPasswordInput.value;
                
                if (confirm && password !== confirm) {
                    confirmPasswordInput.style.borderColor = 'var(--error-color)';
                } else if (confirm) {
                    confirmPasswordInput.style.borderColor = 'var(--success-color)';
                } else {
                    confirmPasswordInput.style.borderColor = 'var(--light-gray)';
                }
            }
            
            // Validación del formulario
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }
                    
                    const password = passwordInput.value;
                    const confirm = confirmPasswordInput.value;
                    const terms = document.getElementById('terms').checked;
                    
                    // Validación adicional
                    if (password !== confirm) {
                        alert('Las contraseñas no coinciden');
                        return;
                    }
                    
                    if (!terms) {
                        alert('Debes aceptar los términos y condiciones');
                        return;
                    }
                    
                    const requirements = checkPasswordStrength(password);
                    const allMet = Object.values(requirements).every(req => req);
                    
                    if (!allMet) {
                        alert('Por favor, cumple con todos los requisitos de la contraseña');
                        return;
                    }
                    
                    // Mostrar estado de carga
                    btnText.style.display = 'none';
                    btnSpinner.style.display = 'block';
                    submitBtn.disabled = true;
                    submitBtn.setAttribute('aria-busy', 'true');
                    
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                });
            }
            
            // Prevenir reenvío del formulario
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
            
            // Autofocus
            const nombreInput = document.getElementById('nombre');
            if (nombreInput && !nombreInput.value) {
                nombreInput.focus();
            }
        });
    </script>
</body>
</html>