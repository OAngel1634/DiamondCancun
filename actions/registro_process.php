<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_functions.php';
require_once __DIR__ . '/../includes/conexion.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /registro.php");
    exit();
}

try {
    // 1. Verificación de CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? null, $_SESSION['csrf_token'] ?? '')) {
        throw new Exception("Error de seguridad: Token inválido.");
    }

    // 2. Sanitización básica de entrada
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // 3. Validaciones de Negocio
    if (empty($nombre) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Datos de registro inválidos.");
    }

    if (!validate_password_strength($password)) {
        throw new Exception("La contraseña no cumple con los requisitos de seguridad.");
    }

    // 4. Verificación de duplicados (Uso de Prepared Statements)
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("El correo electrónico ya está registrado.");
    }

    // 5. Inserción
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insert = $conn->prepare("INSERT INTO usuarios (nombre, email, password, fecha_registro) VALUES (?, ?, ?, NOW())");
    $insert->bind_param("sss", $nombre, $email, $hashedPassword);
    
    if (!$insert->execute()) {
        throw new Exception("Error interno al crear la cuenta.");
    }

    // 6. Éxito: Estandarización de Sesión
    $_SESSION['AUTH_USER_ID']   = $insert->insert_id;
    $_SESSION['AUTH_USER_NAME'] = $nombre;
    $_SESSION['AUTH_LOGGED_IN'] = true;

    header("Location: /dashboard.php");
    exit();

} catch (Exception $e) {
    $_SESSION['FLASH_ERROR'] = $e->getMessage();
    $_SESSION['OLD_INPUT'] = ['nombre' => $nombre, 'email' => $email];
    header("Location: /registro.php");
    exit();
}