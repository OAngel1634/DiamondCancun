<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_functions.php';
session_start();

// Generar token CSRF si no existe (Persistente)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Lógica de redirección si ya está logueado
if (isset($_SESSION['AUTH_USER'])) {
    header("Location: /dashboard.php");
    exit();
}

$error = $_SESSION['FLASH_ERROR'] ?? null;
$oldEmail = $_SESSION['OLD_EMAIL'] ?? '';
unset($_SESSION['FLASH_ERROR'], $_SESSION['OLD_EMAIL']); // Limpiar mensajes flash
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login - Diamond Bright</title>
    </head>
<body>
    <div id="form-container">
        <?php if ($error): ?>
            <div class="error-msg"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="../actions/login_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            
            <label for="email">Email</label>
            <input type="email" name="email" value="<?= e($oldEmail) ?>" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" required>

            <button type="submit">Continuar</button>
        </form>
    </div>
</body>
</html>