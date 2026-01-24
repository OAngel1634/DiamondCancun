<?php
// 1. HTTPS redirect PRIMERO
if ($_SERVER['HTTP_HOST'] !== 'localhost' && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirectUrl");
    exit();
}

// 2. Session_start DESPUÉS
session_start([
    'cookie_secure' => false,
    'cookie_httponly' => false,
    'cookie_samesite' => 'Lax'
]);

if (isset($_SESSION['usuario_id'])) {
    // 3. Ruta ABSOLUTA para dashboard
    header("Location: /../html/dashboard.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Procesar login normal (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 4. Ruta CORRECTA para conexión (relativa a este archivo)
    require_once __DIR__ . '/../includes/conexion.php';

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, email, password, nombre FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
            // Autenticación exitosa
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            // 5. Ruta ABSOLUTA
            header("Location: /dashboard.php");
            exit();
        }
    }

    $error = "Credenciales inválidas";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Diamond Bright Catamarans</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-blue: #003366;
            --secondary-blue: #0099cc;
            --accent-gold: #D4AF37;
            --light-bg: #f0f8ff;
            --white: #ffffff;
            --light-gray: #e0e0e0;
            --text-dark: #333333;
            --text-light: #666666;
            --shadow: 0 8px 20px rgba(0, 51, 102, 0.15);
            --transition: all 0.3s ease;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0, 153, 204, 0.1) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .app-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            background: var(--white);
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(to bottom right, var(--primary-blue), var(--secondary-blue));
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .brand-section::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .brand-section::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -30px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .brand-header {
            position: relative;
            z-index: 2;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .logo-icon {
            font-size: 28px;
            color: var(--accent-gold);
        }

        .logo-text {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .brand-title {
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.3;
        }

        .brand-subtitle {
            font-size: 18px;
            opacity: 0.9;
            line-height: 1.6;
            max-width: 400px;
        }

        .brand-image {
            margin-top: 30px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .brand-footer {
            position: relative;
            z-index: 2;
            font-size: 14px;
            opacity: 0.8;
            margin-top: 20px;
        }

        .login-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-title {
            font-size: 28px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .login-subtitle {
            color: var(--text-light);
            font-size: 16px;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .input-group {
            margin-bottom: 25px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 15px;
        }

        .input-group input {
            width: 100%;
            height: 50px;
            padding: 0 15px;
            border: 2px solid var(--light-gray);
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }

        .input-group input:focus {
            border-color: var(--secondary-blue);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 153, 204, 0.2);
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 40px;
            color: var(--text-light);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember input {
            width: 18px;
            height: 18px;
        }

        .forgot-link {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: var(--white);
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            background: #002244;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
            color: var(--text-light);
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--light-gray);
        }

        .divider-text {
            padding: 0 15px;
            font-size: 14px;
        }

        .social-login {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }

        .social-btn {
            flex: 1;
            background: var(--white);
            border: 1px solid var(--light-gray);
            color: var(--text-dark);
            text-decoration: none;
            text-align: center;
            line-height: 50px;
        }

        .social-btn:hover {
            border-color: var(--secondary-blue);
            color: var(--secondary-blue);
            transform: translateY(-2px);
        }

        .register-link {
            text-align: center;
            font-size: 15px;
            color: var(--text-light);
        }

        .register-link a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Responsive design */
        @media (max-width: 900px) {
            .app-container {
                flex-direction: column;
            }

            .brand-section {
                padding: 30px;
                text-align: center;
            }

            .brand-title {
                font-size: 26px;
            }

            .brand-subtitle {
                max-width: 100%;
            }

            .brand-image {
                margin: 20px auto;
            }

            .login-section {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {
            .social-login {
                flex-direction: column;
            }

            .brand-title {
                font-size: 24px;
            }

            .login-title {
                font-size: 24px;
            }
        }

        /* Mensajes de error */
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sección de marca -->
        <div class="brand-section">
            <div class="brand-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-ship"></i>
                    </div>
                    <div class="logo-text">DIAMOND BRIGHT</div>
                </div>
                <h1 class="brand-title">Explora las aguas cristalinas de Isla Mujeres</h1>
                <p class="brand-subtitle">Vive la experiencia de navegar en nuestros catamaranes de lujo y descubre la belleza del Caribe Mexicano.</p>
            </div>
            <div class="brand-image">
                <i class="fas fa-sailboat" style="font-size: 180px; color: var(--accent-gold); opacity: 0.7;"></i>
            </div>
            <div class="brand-footer">
                © 2023 Diamond Bright Catamarans. Todos los derechos reservados.
            </div>
        </div>

        <!-- Sección de acceso -->
        <div class="login-section">
            <div class="login-header">
                <h2 class="login-title">Bienvenido de nuevo</h2>
                <p class="login-subtitle">Por favor, inicia sesión para acceder a tu cuenta</p>
            </div>

            <div class="form-container">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form id="loginForm" method="post">
                    <div class="input-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>

                    <div class="input-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <i class="fas fa-lock"></i>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Recordar sesión</label>
                        </div>
                        <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Continuar
                    </button>
                </form>

                <div class="divider">
                    <div class="divider-text">o crea una cuenta nueva</div>
                </div>

                <p class="register-link">
                    ¿No tienes una cuenta? <a href="registro.php" id="showRegister">Suscríbete ahora</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');

            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                this.submit(); // Envía el formulario real
            });

            // Efectos para botones
            document.querySelectorAll('.btn').forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                    this.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
                });

                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>