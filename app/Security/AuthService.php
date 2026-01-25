<?php
declare(strict_types=1);

namespace App\Security;

use mysqli;
use Exception;

class AuthService {
    public function __construct(private mysqli $db) {}

    public function register(string $nombre, string $email, string $password): int {
        if (!$this->validatePassword($password)) {
            throw new Exception("Contraseña no cumple requisitos.");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $email, $hash);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al registrar: " . $this->db->error);
        }

        return $this->db->insert_id;
    }

    private function validatePassword(string $password): bool {
        return strlen($password) >= 8 && preg_match('/[A-Z]/', $password);
    }
}