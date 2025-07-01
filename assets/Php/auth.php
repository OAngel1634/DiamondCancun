<?php
session_start();

// Configuración de usuarios (en producción usa base de datos)
$users = [
    'cliente@ejemplo.com' => [
        'id' => 1,
        'nombre' => 'Juan Pérez',
        'password' => password_hash('password123', PASSWORD_DEFAULT)
    ]
];

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    if (isset($users[$email]) {
        if (password_verify($password, $users[$email]['password'])) {
            // Autenticación exitosa
            $_SESSION['user_id'] = $users[$email]['id'];
            $_SESSION['user_name'] = $users[$email]['nombre'];
            $_SESSION['user_email'] = $email;
            
            // Redirigir a la página principal
            header('Location: index.php');
            exit;
        }
    }
    
    // Si falla la autenticación
    $_SESSION['login_error'] = "Credenciales incorrectas";
    header('Location: index.php');
    exit;
}