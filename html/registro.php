<?php
declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

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

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

$error = '';
$inputValues = [
    'nombre' => '',
    'email' => ''
];
$csrfToken = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    try {
     
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("Token de seguridad inválido. Por favor, recarga la página.");
        }
        
        if (!isset($_SESSION['reg_attempts'])) {
            $_SESSION['reg_attempts'] = 0;
            $_SESSION['reg_last_attempt'] = time();
        }
        
        $currentTime = time();
        if ($_SESSION['reg_attempts'] > 5 && ($currentTime - $_SESSION['reg_last_attempt']) < 300) {
            throw new Exception("Demasiados intentos. Por favor, espera 5 minutos.");
        }
        
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $inputValues['nombre'] = htmlspecialchars($nombre ?? '', ENT_QUOTES, 'UTF-8');
        $inputValues['email'] = htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8');
        
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
        
        require_once __DIR__ . '/../includes/conexion.php';
        
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Este email ya está registrado");
        }
        $stmt->close();
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        unset($password, $confirm_password);
        
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, activo, fecha_registro) VALUES (?, ?, ?, 1, NOW())");
        $stmt->bind_param("sss", $nombre, $email, $passwordHash);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al crear el usuario. Por favor, intenta nuevamente.");
        }
        
        $usuario_id = $stmt->insert_id;
        $stmt->close();
        
        if ($isProduction) {
            error_log("Registro exitoso - Usuario ID: $usuario_id, Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
        }
        
        $_SESSION['reg_attempts'] = 0;
        
        $_SESSION['usuario_id'] = (int)$usuario_id;
        $_SESSION['usuario_email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $_SESSION['usuario_nombre'] = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $_SESSION['login_time'] = time();
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        header("Location: /dashboard.php");
        exit();
        
    } catch (Exception $e) {
     
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
     <link rel="stylesheet" href="/css/registro.css?v=<?php echo time(); ?>">
    
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