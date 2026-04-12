<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_functions.php';
session_start();

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
unset($_SESSION['FLASH_ERROR'], $_SESSION['OLD_EMAIL']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="witdh=device-witdh, initial-scale=1.0">
    <title>Login - Diamond Bright</title>
    <link rel="preconnect" hrfe="https://fonts.googleapis.com">
    <link rel="preconnect" hrfe="https://fonts.gstatic.com" crossorigin> 
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">


    </head>
<body>
    <div class="login-page">
    <div class="login-container">
    <div class="login-card">
    <div id="form-container">
        <?php if ($error): ?>
            <div class="error-msg"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="/actions/login_process.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= e($oldEmail) ?>" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Continuar</button>
        </form>
    </div>
    </div>
    </div>
    </div>
</body>
</html>