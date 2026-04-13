<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_functions.php';
session_start();

define('URL_BASE', '');
$imagesUrl = URL_BASE . 'assets/imagenes/';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (isset($_SESSION['AUTH_USER'])) {
    header("Location: /../public/dashboard.php");
    exit();
}


$error = $_SESSION['FLASH_ERROR'] ?? null;
$oldEmail = $_SESSION['OLD_EMAIL'] ?? '';
unset($_SESSION['FLASH_ERROR'], $_SESSION['OLD_EMAIL']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Diamond Bright</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">


    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">

                
                <div class="login-header">
                    <div class="login-logo">
                        <img src="<?php echo $imagesUrl; ?>logo.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
                    </div>
                    <h1 class="login-title">Iniciar sesión</h1>
                    <p class="login-subtitle">Accede a tu cuenta para gestionar reservas</p>
                </div>

               
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" role="alert" aria-live="assertive">
                        <span class="alert-icon">⚠️</span>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                
                <form action="/actions/login_process.php" method="POST" class="login-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">

                   
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <div class="input-wrapper">
                            <span class="input-icon">📧</span>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-input"
                                placeholder="tucorreo@ejemplo.com"
                                value="<?= e($oldEmail) ?>"
                                required
                                autocomplete="email"
                                autofocus
                            >
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <div class="label-row">
                            <label for="password" class="form-label">Contraseña</label>
                            <a href="/recuperar-password.php" class="forgot-link">¿Olvidaste tu contraseña?</a>
                        </div>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-input"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">👁️</button>
                        </div>
                    </div>

                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1"> Recordarme
                        </label>
                    </div>

                   
                    <button type="submit" class="btn btn-primary btn-block">
                        Ingresar
                    </button>

                   
                    <p class="login-footer-text">
                        ¿No tienes cuenta? <a href="/registro.php">Regístrate aquí</a>
                    </p>
                </form>

            </div>

            <footer class="login-footer">
                <p>&copy; <?= date('Y') ?> Diamond Bright Cancún. Todos los derechos reservados.</p>
                <nav class="footer-links">
                    <a href="/privacidad">Privacidad</a> · 
                    <a href="/terminos">Términos</a>
                </nav>
            </footer>

        </div>
    </div>

    
    <script>
        (function() {
            const toggleBtn = document.querySelector('.toggle-password');
            const passwordInput = document.getElementById('password');
            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.textContent = type === 'password' ? '👁️' : '🙈';
                });
            }
        })();
    </script>
</body>
</html>