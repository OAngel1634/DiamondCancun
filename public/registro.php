<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_functions.php';
session_start();

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_SESSION['FLASH_ERROR'] ?? null;
$old   = $_SESSION['OLD_INPUT'] ?? ['nombre' => '', 'email' => ''];
unset($_SESSION['FLASH_ERROR'], $_SESSION['OLD_INPUT']); // Limpiar mensajes flash
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro | Diamond Bright</title>
    <link rel="stylesheet" href="/css/registro.css">
</head>
<body>
    <form action="/../actions/registro_process.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        
        <?php if ($error): ?>
            <div class="alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <input type="text" name="nombre" value="<?= e($old['nombre']) ?>" placeholder="Nombre" required>
        <input type="email" name="email" value="<?= e($old['email']) ?>" placeholder="Email" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        
        <button type="submit">Crear Cuenta</button>
    </form>
</body>
</html>