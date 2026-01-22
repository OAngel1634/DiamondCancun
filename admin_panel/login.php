<?php
// Usar nombre de sesión específico para el panel admin
session_name('admin_session');
session_start();

// Limpiar búfer de salida para evitar problemas con headers
ob_start();

require_once 'db.php';

$error = '';

// Pequeña protección contra fuerza bruta: esperar 2 segundos
sleep(2);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validación básica
    if (empty($email)) {
        $error = 'El email es requerido';
    } elseif (empty($password)) {
        $error = 'La contraseña es requerida';
    } else {
        try {
            $database = new AdminDB();
            $conn = $database->connect();
            
            $stmt = $conn->prepare("SELECT id, nombre, rol, password FROM administradores WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Establecer sesión
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_nombre'] = $admin['nombre'];
                $_SESSION['admin_rol'] = $admin['rol'];
                
                // Regenerar ID de sesión para prevenir fijación
                session_regenerate_id(true);
                
                // Redirigir al dashboard
                header('Location: index.php');
                exit;
            } else {
                $error = 'Credenciales incorrectas';
            }
        } catch (PDOException $e) {
            error_log("Error de base de datos en login: " . $e->getMessage());
            $error = 'Error interno. Por favor, intente más tarde.';
        } catch (Exception $e) {
            error_log("Error general en login: " . $e->getMessage());
            $error = 'Error inesperado.';
        }
    }
}

// Limpiar búfer y enviar contenido
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Diamond Bright</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #6f42c1 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-control {
            border-radius: 50px;
            padding: 12px 20px;
        }
        
        .btn-login {
            border-radius: 50px;
            padding: 12px;
            background: #0d6efd;
            border: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h3><i class="bi bi-gem"></i> Diamond Bright</h3>
            <p>Panel de Administración</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-login w-100">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>