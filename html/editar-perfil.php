<?php
session_start(); // Usar sesiones PHP nativas

// Verificar sesión PHP (mismo método que perfil.php)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

require_once 'Usuario.php';
require_once 'conexion.php';

// Obtener información del usuario (idéntico a perfil.php)
$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

// Crear instancia de Database y conectar
$database = new Database();
$pdo = $database->connect();

// Obtener sección a editar
$seccion = isset($_GET['seccion']) ? $_GET['seccion'] : 'basica';

// Procesar formulario si se envió
$mensaje = '';
$mensaje_tipo = ''; // Inicializar variable para tipo de mensaje (éxito/error)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($seccion === 'basica') {
            // Actualizar información básica
            $nombre = $_POST['nombre'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $genero = $_POST['genero'];
            $facilidades = $_POST['facilidades'];
            
            // Actualizar usuario
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, fecha_nacimiento = ? WHERE id = ?");
            $stmt->execute([$nombre, $fecha_nacimiento, $usuario->id]);
            
            // Obtener preferencias actualizadas después de la operación
            $stmt = $pdo->prepare("SELECT * FROM preferencias WHERE usuario_id = ?");
            $stmt->execute([$usuario->id]);
            $preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // VERIFICAR SI EXISTEN PREFERENCIAS
            if (!$preferencias) {
                // CREAR NUEVO REGISTRO SI NO EXISTE
                $stmt = $pdo->prepare("INSERT INTO preferencias (usuario_id, genero, facilidades_acceso) VALUES (?, ?, ?)");
                $stmt->execute([$usuario->id, $genero, $facilidades]);
            } else {
                // Actualizar preferencias existentes
                $stmt = $pdo->prepare("UPDATE preferencias SET genero = ?, facilidades_acceso = ? WHERE usuario_id = ?");
                $stmt->execute([$genero, $facilidades, $usuario->id]);
            }
            
            $mensaje = "¡Información básica actualizada correctamente!";
            
        } elseif ($seccion === 'contacto') {
            // Actualizar datos de contacto
            $telefono = $_POST['telefono'];
            $contacto_emergencia = $_POST['contacto_emergencia'];
            $email = $_POST['email'];
            $direccion = $_POST['direccion'];
            
            // Actualizar usuario
            $stmt = $pdo->prepare("UPDATE usuarios SET telefono = ?, contacto_emergencia = ?, email = ?, direccion = ? WHERE id = ?");
            $stmt->execute([$telefono, $contacto_emergencia, $email, $direccion, $usuario->id]);
            
            $mensaje = "¡Datos de contacto actualizados correctamente!";
            
        } elseif ($seccion === 'preferencias') {
            // Actualizar preferencias
            $notificaciones = $_POST['notificaciones'];
            $idioma = $_POST['idioma'];
            $experiencia_favorita = $_POST['experiencia_favorita'];
            $preferencias_gastronomicas = $_POST['preferencias_gastronomicas'];
            
            // Obtener preferencias actualizadas después de la operación
            $stmt = $pdo->prepare("SELECT * FROM preferencias WHERE usuario_id = ?");
            $stmt->execute([$usuario->id]);
            $preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // VERIFICAR SI EXISTEN PREFERENCIAS
            if (!$preferencias) {
                // CREAR NUEVO REGISTRO SI NO EXISTE
                $stmt = $pdo->prepare("INSERT INTO preferencias 
                    (usuario_id, notificaciones, idioma, experiencia_favorita, preferencias_gastronomicas) 
                    VALUES (?, ?, ?, ?, ?)");
                    
                $stmt->execute([
                    $usuario->id,
                    $notificaciones,
                    $idioma,
                    $experiencia_favorita,
                    $preferencias_gastronomicas
                ]);
            } else {
                // Actualizar preferencias existentes
                $stmt = $pdo->prepare("UPDATE preferencias SET 
                    notificaciones = ?, 
                    idioma = ?, 
                    experiencia_favorita = ?, 
                    preferencias_gastronomicas = ? 
                    WHERE usuario_id = ?");
                    
                $stmt->execute([
                    $notificaciones,
                    $idioma,
                    $experiencia_favorita,
                    $preferencias_gastronomicas,
                    $usuario->id
                ]);
            }
            
            $mensaje = "¡Preferencias actualizadas correctamente!";
        }
        
        // Recargar datos del usuario después de actualizar
        $usuario->buscarPorId($usuario->id);
        $stmt = $pdo->prepare("SELECT * FROM preferencias WHERE usuario_id = ?");
        $stmt->execute([$usuario->id]);
        $preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $mensaje = "Error: " . $e->getMessage();
        $mensaje_tipo = 'error';
    }
}

// Obtener preferencias del usuario (si no se procesó POST o para mostrar datos iniciales)
if (!isset($preferencias)) {
    $stmt = $pdo->prepare("SELECT * FROM preferencias WHERE usuario_id = ?");
    $stmt->execute([$usuario->id]);
    $preferencias = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Obtener datos reales del usuario
$nombreUsuario = htmlspecialchars($usuario->nombre);
$emailUsuario = htmlspecialchars($usuario->email);

// Preparar datos para el formulario
$datosUsuario = [
    'basica' => [
        'nombre' => $nombreUsuario,
        'fecha_nacimiento' => $usuario->fecha_nacimiento,
        'facilidades' => $preferencias['facilidades_acceso'] ?? '',
        'genero' => $preferencias['genero'] ?? ''
    ],
    'contacto' => [
        'telefono' => $usuario->telefono,
        'contacto_emergencia' => $usuario->contacto_emergencia,
        'email' => $emailUsuario,
        'direccion' => $usuario->direccion
    ],
    'preferencias' => [
        'notificaciones' => $preferencias['notificaciones'] ?? '',
        'idioma' => $preferencias['idioma'] ?? '',
        'experiencia_favorita' => $preferencias['experiencia_favorita'] ?? '',
        'preferencias_gastronomicas' => $preferencias['preferencias_gastronomicas'] ?? ''
    ]
];

// Función para formatear fecha
function formatearFecha($fecha) {
    if ($fecha) {
        return date('d/m/Y', strtotime($fecha));
    }
    return 'No especificada';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Diamond Bright</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos base del perfil (copiados de perfil.php) */
        :root {
            --primary: #003366;
            --secondary: #0099cc;
            --accent: #D4AF37;
            --light-bg: #f0f8ff;
            --white: #ffffff;
            --light-gray: #e0e0e0;
            --text-dark: #333333;
            --text-light: #666666;
            --shadow: 0 8px 20px rgba(0, 51, 102, 0.15);
            --transition: all 0.3s ease;
            --border-radius: 10px;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--light-bg) 0%, rgba(0,153,204,0.1) 100%);
            color: var(--text-dark);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            gap: 20px;
            flex-direction: column;
        }

        /* Header Styles */
        .profile-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            gap: 15px;
        }

        .welcome-section h1 {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none;
            font-size: 15px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #002244;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 51, 102, 0.3);
        }

        /* Formulario de edición */
        .edit-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 20px;
        }

        .edit-title {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .edit-form {
                grid-template-columns: 1fr 1fr;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            font-size: 16px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 153, 204, 0.2);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .form-actions {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 10px;
        }

        .btn-cancel {
            background: var(--light-gray);
            color: var(--text-dark);
        }

        .btn-cancel:hover {
            background: #d0d0d0;
        }

        .btn-save {
            background: var(--success);
            color: white;
        }

        .btn-save:hover {
            background: #218838;
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 4px;
            background: var(--success);
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            background: var(--danger);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="profile-header">
            <div class="welcome-section">
                <h1>Editar Perfil - Diamond Bright</h1>
            </div>
            <button class="btn btn-primary" onclick="location.href='perfil.php'">
                <i class="fas fa-arrow-left"></i> Volver al perfil
            </button>
        </div>

        <!-- Contenedor del formulario -->
        <div class="edit-container">
            <h2 class="edit-title">
                <i class="fas fa-edit"></i>
                <?php 
                    echo match($seccion) {
                        'basica' => 'Editar Información Básica',
                        'contacto' => 'Editar Datos de Contacto',
                        'preferencias' => 'Editar Preferencias',
                        default => 'Editar Perfil'
                    };
                ?>
            </h2>

            <?php if ($mensaje): ?>
                <div class="toast show <?php echo isset($mensaje_tipo) ? 'error' : ''; ?>"><?php echo $mensaje; ?></div>
            <?php endif; ?>

            <form class="edit-form" method="POST">
                <?php if ($seccion === 'basica'): ?>
                    <!-- Información Básica -->
                    <div class="form-group">
                        <label for="nombre"><i class="fas fa-user"></i> Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['basica']['nombre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_nacimiento"><i class="fas fa-birthday-cake"></i> Fecha de nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['basica']['fecha_nacimiento']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="genero"><i class="fas fa-venus-mars"></i> Género</label>
                        <select id="genero" name="genero" class="form-control">
                            <option value="Masculino" <?php echo ($datosUsuario['basica']['genero'] === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Femenino" <?php echo ($datosUsuario['basica']['genero'] === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                            <option value="Otro" <?php echo ($datosUsuario['basica']['genero'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            <option value="Prefiero no decir" <?php echo ($datosUsuario['basica']['genero'] === 'Prefiero no decir') ? 'selected' : ''; ?>>Prefiero no decir</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="facilidades"><i class="fas fa-universal-access"></i> Facilidades de acceso</label>
                        <input type="text" id="facilidades" name="facilidades" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['basica']['facilidades']); ?>">
                    </div>

                <?php elseif ($seccion === 'contacto'): ?>
                    <!-- Datos de Contacto -->
                    <div class="form-group">
                        <label for="telefono"><i class="fas fa-mobile-alt"></i> Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['contacto']['telefono']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="contacto_emergencia"><i class="fas fa-exclamation-triangle"></i> Contacto de emergencia</label>
                        <input type="text" id="contacto_emergencia" name="contacto_emergencia" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['contacto']['contacto_emergencia']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['contacto']['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <textarea id="direccion" name="direccion" class="form-control"><?php echo htmlspecialchars($datosUsuario['contacto']['direccion']); ?></textarea>
                    </div>

                <?php elseif ($seccion === 'preferencias'): ?>
                    <!-- Preferencias -->
                    <div class="form-group">
                        <label for="notificaciones"><i class="fas fa-bell"></i> Notificaciones</label>
                        <select id="notificaciones" name="notificaciones" class="form-control">
                            <option value="Activadas" <?php echo ($datosUsuario['preferencias']['notificaciones'] === 'Activadas') ? 'selected' : ''; ?>>Activadas</option>
                            <option value="Solo reservas" <?php echo ($datosUsuario['preferencias']['notificaciones'] === 'Solo reservas') ? 'selected' : ''; ?>>Solo reservas</option>
                            <option value="Solo promociones" <?php echo ($datosUsuario['preferencias']['notificaciones'] === 'Solo promociones') ? 'selected' : ''; ?>>Solo promociones</option>
                            <option value="Desactivadas" <?php echo ($datosUsuario['preferencias']['notificaciones'] === 'Desactivadas') ? 'selected' : ''; ?>>Desactivadas</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="idioma"><i class="fas fa-language"></i> Idioma preferido</label>
                        <select id="idioma" name="idioma" class="form-control">
                            <option value="Español" <?php echo ($datosUsuario['preferencias']['idioma'] === 'Español') ? 'selected' : ''; ?>>Español</option>
                            <option value="English" <?php echo ($datosUsuario['preferencias']['idioma'] === 'English') ? 'selected' : ''; ?>>English</option>
                            <option value="Français" <?php echo ($datosUsuario['preferencias']['idioma'] === 'Français') ? 'selected' : ''; ?>>Français</option>
                            <option value="Deutsch" <?php echo ($datosUsuario['preferencias']['idioma'] === 'Deutsch') ? 'selected' : ''; ?>>Deutsch</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="experiencia_favorita"><i class="fas fa-ship"></i> Experiencia favorita</label>
                        <input type="text" id="experiencia_favorita" name="experiencia_favorita" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['preferencias']['experiencia_favorita']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="preferencias_gastronomicas"><i class="fas fa-glass-cheers"></i> Preferencias gastronómicas</label>
                        <input type="text" id="preferencias_gastronomicas" name="preferencias_gastronomicas" class="form-control" 
                               value="<?php echo htmlspecialchars($datosUsuario['preferencias']['preferencias_gastronomicas']); ?>">
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="location.href='perfil.php'">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Ocultar toast después de 3 segundos
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) toast.classList.remove('show');
        }, 3000);
    </script>
</body>
</html>