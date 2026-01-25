<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/conexion.php';

use App\Services\AuthService;

session_start();

try {
    // Protección CSRF
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        throw new Exception("Error de seguridad: Token inválido.");
    }

    // Rate Limiting Básico (Simulado)
    if (($_SESSION['last_reg_attempt'] ?? 0) > (time() - 30)) {
        throw new Exception("Demasiados intentos. Espera 30 segundos.");
    }
    $_SESSION['last_reg_attempt'] = time();

    // Captura de datos
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $pass   = $_POST['password'] ?? '';

    // Inyección de dependencia: Pasamos la conexión al servicio
    $authService = new AuthService($conn);
    $authService->registrarUsuario($nombre, $email, $pass);

    $_SESSION['success'] = "¡Cuenta creada con éxito!";
    header("Location: /public/inicio-sesion.php");

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: /public/registro.php");
}
exit();