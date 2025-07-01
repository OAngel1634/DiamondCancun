<?php
session_start();

// Inicializar variables
$googleAuthUrl = '';
$error = '';
$success = '';

// Incluir autoloader para Google API
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
    
    // Configuración de Google
    $clientID = '1089751372954-nasuroolveft9fek524am4u32tnfk32g.apps.googleusercontent.com';
    $clientSecret = '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';
    $redirectUri = 'http://localhost/laslos/DiamondPrueba/html/registro.php';
    
    try {
        $client = new Google_Client();
        $client->setClientId($clientID);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->addScope("email");
        $client->addScope("profile");
        $googleAuthUrl = $client->createAuthUrl();
    } catch (Exception $e) {
        $error = "Error al configurar Google: " . $e->getMessage();
    }
} else {
    $error = "Error: No se encontró el autoloader de Google";
}

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    // ... (código existente para procesar el formulario) ...
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Diamond Bright Catamarans</title>
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
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0,153,204,0.1) 100%);
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
        
        .register-section {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .register-title {
            font-size: 28px;
            color: var(--primary-blue);
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .register-subtitle {
            color: var(--text-light);
            font-size: 16px;
        }
        
        .form-container {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .input-group {
            margin-bottom: 20px;
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
        
        .login-link {
            text-align: center;
            font-size: 15px;
            color: var(--text-light);
        }
        
        .login-link a {
            color: var(--secondary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        /* Mensajes */
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        /* Términos y condiciones */
        .terms {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--text-light);
        }
        
        .terms input {
            margin-top: 3px;
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
            
            .register-section {
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
            
            .register-title {
                font-size: 24px;
            }
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
        
        <!-- Sección de registro -->
        <div class="register-section">
            <div class="register-header">
                <h2 class="register-title">Crea tu cuenta</h2>
                <p class="register-subtitle">Únete a Diamond Bright y comienza a explorar</p>
            </div>
            
            <div class="form-container">
                <?php if ($error): ?>
                    <div class="message error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="message success-message"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form id="registrationForm" method="post">
                    <div class="input-group">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
                        <i class="fas fa-user"></i>
                    </div>
                    
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
                    
                    <div class="input-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                        <i class="fas fa-lock"></i>
                    </div>
                    
                    <div class="terms">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">Acepto los <a href="Terminoscondiciones.php">Términos y Condiciones</a> y la <a href="politica.php">Política de Privacidad</a> de Diamond Bright</label>
                    </div>
                    
                    <button type="submit" name="registro" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Registrarse
                    </button>
                </form>
                
                <div class="divider">
                    <div class="divider-text">o regístrate con</div>
                </div>
                
                <?php if ($googleAuthUrl): ?>
                    <div class="social-login">
                        <a href="<?php echo $googleAuthUrl; ?>" class="btn social-btn">
                            <i class="fab fa-google"></i> Google
                        </a>
                    </div>
                <?php endif; ?>
                
                <p class="login-link">
                    ¿Ya tienes una cuenta? <a href="inicio-sesion.php">Inicia sesión</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Validación del formulario de registro
        document.addEventListener('DOMContentLoaded', function() {
            const registrationForm = document.getElementById('registrationForm');
            
            registrationForm.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                const terms = document.getElementById('terms').checked;
                
                if (!terms) {
                    e.preventDefault();
                    alert('Debes aceptar los términos y condiciones');
                    return;
                }
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                // Mostrar feedback visual
                const submitBtn = registrationForm.querySelector('.btn-primary');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta';
                submitBtn.disabled = true;
            });
            
            // Efectos para los botones
            document.querySelectorAll('.social-btn').forEach(button => {
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
          echo 
    sessionStorage.setItem("loginSuccess", "true");
    window.location.href = "index.php";

    </script>
</body>
</html>