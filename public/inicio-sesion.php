<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_functions.php';
session_start();

define('URL_BASE', '');
$imagesUrl = URL_BASE . 'assets/imagenes/';

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirigir si ya está logueado
if (isset($_SESSION['AUTH_USER'])) {
    header("Location: /dashboard.php");
    exit();
}

// Mensajes flash
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

    <!-- Precarga de fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

    <!-- Hoja de estilos principal del login -->
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">

                <!-- Cabecera con logo y título -->
                <div class="login-header">
                    <div class="login-logo">
                        <img src="<?php echo $imagesUrl; ?>logo.jpg" alt="Tour en Isla Mujeres con Diamond Bright">
                    </div>
                    <h1 class="login-title">Iniciar sesión</h1>
                    <p class="login-subtitle">Accede a tu cuenta para gestionar reservas</p>
                </div>

                <!-- Mensaje de error (único) -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-error" role="alert" aria-live="assertive">
                        <span class="alert-icon">⚠️</span>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Formulario de acceso -->
                <form action="/actions/login_process.php" method="POST" class="login-form" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">

                    <!-- Campo Email -->
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

                    <!-- Campo Contraseña -->
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

                    <!-- Opciones adicionales -->
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1"> Recordarme
                        </label>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn btn-primary btn-block">
                        Ingresar
                    </button>

                    <!-- Enlace a registro -->
                    <p class="login-footer-text">
                        ¿No tienes cuenta? <a href="/registro.php">Regístrate aquí</a>
                    </p>
                </form>

            </div><!-- /.login-card -->

            <!-- Pie de página -->
            <footer class="login-footer">
                <p>&copy; <?= date('Y') ?> Diamond Bright Cancún. Todos los derechos reservados.</p>
                <nav class="footer-links">
                    <a href="/privacidad">Privacidad</a> · 
                    <a href="/terminos">Términos</a>
                </nav>
            </footer>

        </div><!-- /.login-container -->
    </div><!-- /.login-page -->

    <!-- Script para mostrar/ocultar contraseña -->
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