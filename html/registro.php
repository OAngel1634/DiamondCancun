<?php
// Configuración segura de sesiones
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
session_set_cookie_params([
    'lifetime' => 86400 * 30,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// ELIMINADO: session_name('user_session'); // Causa conflicto con otras páginas
session_start();

// Configuración de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// INCLUSIÓN SIMPLIFICADA - Usuario.php está en el mismo directorio
require_once __DIR__ . '/Usuario.php';

// Inicializar variables
$error = '';
$inputValues = [
    'nombre' => '',
    'email' => ''
];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    try {
        // Sanitizar entradas
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Guardar valores para repoblar formulario
        $inputValues['nombre'] = $nombre;
        $inputValues['email'] = $email;
        
        // Validaciones
        if (empty($nombre)) {
            throw new Exception("El nombre es obligatorio");
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }
        if (strlen($password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres");
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una mayúscula");
        }
        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un número");
        }
        if ($password !== $confirm_password) {
            throw new Exception("Las contraseñas no coinciden");
        }
        if (empty($_POST['terms'])) {
            throw new Exception("Debes aceptar los términos y condiciones");
        }
        
        // Crear usuario
        $usuario = new Usuario();
        
        if ($usuario->buscarPorEmail($email)) {
            throw new Exception("Este email ya está registrado");
        }
        
        $usuario_id = $usuario->crear([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password
        ]);
        
        // Verificar creación exitosa
        if (!$usuario_id) {
            throw new Exception("Error al crear el usuario en la base de datos");
        }
        
        // ELIMINADO: session_regenerate_id(true); // Causaba problemas
        
        // Establecer sesión
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_nombre'] = $nombre;
        
        // Redirigir al dashboard
        header("Location: dashboard.php");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Asegurar que $error sea string
$error = (string)$error;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos optimizados y mejorados */
        :root {
            --primary: #003366;
            --secondary: #0099cc;
            --accent: #D4AF37;
            --light-bg: #f0f8ff;
            --white: #ffffff;
            --text-dark: #333333;
            --text-light: #666666;
            --danger: #d32f2f;
            --border-radius: 15px;
            --shadow: 0 8px 30px rgba(0, 51, 102, 0.2);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0,153,204,0.1) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        
        .header {
            background: var(--primary);
            color: var(--white);
            padding: 30px 20px;
            text-align: center;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .logo i {
            color: var(--accent);
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 500;
        }
        
        .form-container {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        input {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        input:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,153,204,0.1);
        }
        
        .error {
            color: var(--danger);
            background: #ffebee;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
            display: <?= $error ? 'block' : 'none' ?>;
        }
        
        button {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        button:hover {
            background: #002244;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.2);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
        }
        
        .login-link a {
            color: var(--secondary);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .terms {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 20px;
            font-size: 14px;
            color: var(--text-light);
        }
        
        .terms input {
            width: auto;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: var(--text-light);
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: var(--text-light);
        }
        
        .strength-meter {
            height: 5px;
            background: #eee;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-ship"></i>
                <span>DIAMOND BRIGHT</span>
            </div>
            <h1>Crea tu cuenta</h1>
        </div>
        
        <div class="form-container">
            <div class="error" id="error-message"><?= htmlspecialchars($error) ?></div>
            
            <form method="POST" id="registration-form">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" required 
                           value="<?= htmlspecialchars($inputValues['nombre']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($inputValues['email']) ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                        <div id="strength-text">Seguridad: baja</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="terms">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Acepto los <a href="politica-privacidad.php" target="_blank">términos y condiciones</a> y <a href="politica-privacidad.php" target="_blank">política de privacidad</a></label>
                </div>
                
                <button type="submit" name="registro">
                    <i class="fas fa-user-plus"></i> Crear cuenta
                </button>
            </form>
            
            <div class="login-link">
                ¿Ya tienes una cuenta? <a href="inicio-sesion.php">Inicia sesión</a>
            </div>
        </div>
        
        <div class="footer">
            &copy; <?= date('Y') ?> Diamond Bright Catamarans. Todos los derechos reservados.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registration-form');
            const errorMessage = document.getElementById('error-message');
            const passwordInput = document.getElementById('password');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            
            // Medidor de fortaleza de contraseña
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Longitud mínima
                if (password.length >= 8) strength += 1;
                
                // Contiene mayúsculas
                if (/[A-Z]/.test(password)) strength += 1;
                
                // Contiene números
                if (/[0-9]/.test(password)) strength += 1;
                
                // Contiene caracteres especiales
                if (/[^A-Za-z0-9]/.test(password)) strength += 1;
                
                // Actualizar barra de progreso
                const width = strength * 25;
                strengthFill.style.width = `${width}%`;
                
                // Actualizar texto
                let text = '';
                let color = '';
                switch(strength) {
                    case 0:
                    case 1:
                        text = 'Muy débil';
                        color = '#d32f2f';
                        break;
                    case 2:
                        text = 'Débil';
                        color = '#ff9800';
                        break;
                    case 3:
                        text = 'Buena';
                        color = '#4caf50';
                        break;
                    case 4:
                        text = 'Fuerte';
                        color = '#2e7d32';
                        break;
                }
                
                strengthFill.style.backgroundColor = color;
                strengthText.textContent = `Seguridad: ${text}`;
                strengthText.style.color = color;
            });
            
            // Validación del formulario
            form.addEventListener('submit', function(e) {
                const password = passwordInput.value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const terms = document.getElementById('terms').checked;
                
                // Resetear mensaje de error
                errorMessage.style.display = 'none';
                
                // Validación de contraseña
                if (password.length < 8) {
                    e.preventDefault();
                    errorMessage.textContent = 'La contraseña debe tener al menos 8 caracteres';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                if (!/[A-Z]/.test(password)) {
                    e.preventDefault();
                    errorMessage.textContent = 'La contraseña debe contener al menos una mayúscula';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                if (!/[0-9]/.test(password)) {
                    e.preventDefault();
                    errorMessage.textContent = 'La contraseña debe contener al menos un número';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    errorMessage.textContent = 'Las contraseñas no coinciden';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                if (!terms) {
                    e.preventDefault();
                    errorMessage.textContent = 'Debes aceptar los términos y condiciones';
                    errorMessage.style.display = 'block';
                    return;
                }
                
                // Mostrar estado de carga
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>