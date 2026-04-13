<?php
declare(strict_types=1);

function authenticate_user(PDO $pdo, string $email, string $password): ?array {
    $sql = "SELECT user_id, email, password_hash, user_role, is_active 
            FROM system_users 
            WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || empty($user['password_hash'])) {
        return null; 
    }

    if (!password_verify($password, $user['password_hash'])) {
        return null;
    }

    if (!$user['is_active']) {
        return null;
    }

    return [
        'id'     => $user['user_id'],
        'nombre' => explode('@', $email)[0] ?? 'Usuario', // Placeholder
        'email'  => $user['email'],
        'rol'    => $user['user_role']
    ];
}

/**
 * Registra un nuevo usuario en system_users
 * @return string|true  true si éxito, mensaje de error si falla
 */
function registrarUsuario(PDO $pdo, string $email, string $password, string $user_role = 'customer'): string|bool {
    // Validaciones básicas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Email inválido.";
    }
    if (strlen($password) < 8) {
        return "La contraseña debe tener al menos 8 caracteres.";
    }

    // Verificar si el email ya existe
    $check = $pdo->prepare("SELECT user_id FROM system_users WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        return "El correo ya está registrado.";
    }

    // Insertar nuevo usuario
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare(
        "INSERT INTO system_users (email, password_hash, user_role) 
         VALUES (:email, :hash, :role)"
    );
    $success = $stmt->execute([
        ':email' => $email,
        ':hash'  => $hash,
        ':role'  => $user_role
    ]);

    return $success ? true : "Error al registrar el usuario.";
}

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}