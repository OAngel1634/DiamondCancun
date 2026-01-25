<?php
// includes/auth_functions.php

/**
 * Registra un nuevo usuario aplicando el hash de contraseña.
 * Retorna true si tuvo éxito o un mensaje de error.
 */
function registrarUsuario($conn, $nombre, $email, $password) {
    // 1. Verificar si el email ya existe
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        return "El correo ya está registrado.";
    }

    // 2. Encriptar contraseña (NUNCA guardes texto plano)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // 3. Insertar en la base de datos simplificada
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $hash);
    
    return $stmt->execute() ? true : "Error al registrar el usuario.";
}