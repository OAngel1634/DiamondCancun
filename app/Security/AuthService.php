<?php

declare(strict_types=1);

function authenticate_user(PDO $pdo, string $email, string $password): ?array {
    $sql = "SELECT user_id, email, password_hash, user_role, is_active 
            FROM system_users 
            WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        return null; 
    }

   
    if (empty($user['password_hash'])) {
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
        'nombre' => explode('@', $email)[0] ?? 'Usuario', 
        'email'  => $user['email'],
        'rol'    => $user['user_role']
    ];
}

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}