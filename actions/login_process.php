<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/conexion.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /inicio-sesion.php");
    exit();
}

try {
    // 1. Validación de CSRF (Persistente por sesión)
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        throw new Exception("Error de seguridad: Token inválido. Recarga la página.");
    }

    // 2. Captura y validación básica
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        throw new Exception("Todos los campos son obligatorios.");
    }

    // 3. Autenticación
    $user = authenticate_user($conn, $email, $password);

    if (!$user) {
        // Log para QA/Auditoría
        error_log("Fallo de login para: " . $email . " desde IP: " . $_SERVER['REMOTE_ADDR']);
        throw new Exception("Credenciales incorrectas o cuenta inactiva.");
    }

    // 4. Éxito: Regenerar ID de sesión para prevenir Session Fixation
    session_regenerate_id(true);
    
    $_SESSION['AUTH_USER'] = [
        'id'     => (int)$user['id'],
        'nombre' => $user['nombre'],
        'email'  => $user['email'],
        'rol'    => $user['rol'] ?? 'cliente'
    ];
    $_SESSION['LAST_ACTIVITY'] = time();

    header("Location: /dashboard.php");
    exit();

} catch (Exception $e) {
    $_SESSION['FLASH_ERROR'] = $e->getMessage();
    $_SESSION['OLD_EMAIL'] = $email;
    header("Location: /inicio-sesion.php");
    exit();
}