<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../app/Security/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /inicio-sesion.php");
    exit();
}

try {

    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        throw new Exception("Error de seguridad: Token inválido. Recarga la página.");
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email || empty($password)) {
        throw new Exception("Todos los campos son obligatorios.");
    }

    $pdo = getConnection();
    $user = authenticate_user($pdo, $email, $password);

    if (!$user) {
        error_log("Fallo de login para: " . $email . " desde IP: " . $_SERVER['REMOTE_ADDR']);
        throw new Exception("Credenciales incorrectas o cuenta inactiva.");
    }

    session_regenerate_id(true);
    $_SESSION['AUTH_USER'] = $user;
    $_SESSION['LAST_ACTIVITY'] = time();
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    header("Location: " . $redirect);
    exit();

} catch (Exception $e) {
    $_SESSION['FLASH_ERROR'] = $e->getMessage();
    $_SESSION['OLD_EMAIL'] = $email;
    header("Location: /inicio-sesion.php");
    exit();
}