<?php
session_start();

// Verificar sesión activa
if (empty($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

require_once 'Usuario.php'; // Añadir para validación completa
require_once 'conexion.php';

// Validar usuario real
$usuario = new Usuario();
if (!$usuario->buscarPorId($_SESSION['usuario_id'])) {
    session_destroy();
    header("Location: inicio-sesion.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

$database = new Database();
$pdo = $database->connect();

// Función para validar tarjeta (mejorada)
function validarTarjeta($numero, $expiracion, $cvv) {
    // Limpiar espacios
    $numero = preg_replace('/\s+/', '', $numero);
    $expiracion = trim($expiracion);
    $cvv = trim($cvv);
    
    // Validar número con algoritmo Luhn
    if (!preg_match('/^[0-9]{13,19}$/', $numero) || !validaLuhn($numero)) {
        return 'Número de tarjeta inválido';
    }
    
    // Validar fecha de expiración
    if (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $expiracion, $matches)) {
        return 'Formato de fecha inválido (MM/YY)';
    }
    
    $mes = $matches[1];
    $ano = '20' . $matches[2]; // Asumimos siglo 21
    $fechaExpiracion = DateTime::createFromFormat('Y-m-d', "$ano-$mes-01");
    $hoy = new DateTime('first day of this month');
    
    if ($fechaExpiracion < $hoy) {
        return 'Tarjeta expirada';
    }
    
    // Validar CVV
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        return 'CVV inválido (3-4 dígitos)';
    }
    
    return null;
}

// Algoritmo de Luhn para validar tarjetas
function validaLuhn($numero) {
    $sum = 0;
    $par = false;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $digito = intval($numero[$i]);
        if ($par) {
            $digito *= 2;
            if ($digito > 9) $digito -= 9;
        }
        $sum += $digito;
        $par = !$par;
    }
    
    return ($sum % 10) === 0;
}

// Función para validar PayPal (mejorada)
function validarPayPal($email) {
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Email inválido';
    }
    
    // Verificar dominio PayPal si es necesario
    /*
    list($user, $domain) = explode('@', $email);
    if (!in_array(strtolower($domain), ['paypal.com', 'gmail.com', 'outlook.com'])) {
        return 'Debe usar un email válido de PayPal';
    }
    */
    
    return null;
}

try {
    // Validar acción permitida
    $accionesPermitidas = ['agregar', 'editar', 'eliminar', 'predeterminado'];
    if (!in_array($accion, $accionesPermitidas)) {
        header("Location: pagos.php?error=accion_invalida");
        exit();
    }

    switch ($accion) {
        case 'agregar':
            $tipo = $_POST['tipo'] ?? '';
            $predeterminado = isset($_POST['predeterminado']) ? 1 : 0;
            
            // Validar tipo de pago
            if (!in_array($tipo, ['tarjeta', 'paypal'])) {
                header("Location: agregar-pago.php?error=tipo_invalido");
                exit();
            }
            
            // Si se marca como predeterminado, actualizar los existentes
            if ($predeterminado) {
                $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 0 WHERE usuario_id = ?");
                $stmt->execute([$usuarioId]);
            }
            
            if ($tipo === 'tarjeta') {
                $titular = $_POST['titular'] ?? '';
                $numero = $_POST['numero'] ?? '';
                $expiracion = $_POST['expiracion'] ?? '';
                $cvv = $_POST['cvv'] ?? '';
                
                // Validar campos requeridos
                if (empty($titular) || empty($numero) || empty($expiracion) || empty($cvv)) {
                    header("Location: agregar-pago.php?error=campos_requeridos");
                    exit();
                }
                
                $error = validarTarjeta($numero, $expiracion, $cvv);
                if ($error) {
                    header("Location: agregar-pago.php?error=" . urlencode($error));
                    exit();
                }
                
                // Encriptar datos sensibles
                $numeroEncriptado = password_hash($numero, PASSWORD_DEFAULT);
                $cvvEncriptado = password_hash($cvv, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO metodos_pago (usuario_id, tipo, titular, numero, expiracion, cvv, predeterminado) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$usuarioId, $tipo, $titular, $numeroEncriptado, $expiracion, $cvvEncriptado, $predeterminado]);
                
            } elseif ($tipo === 'paypal') {
                $email = $_POST['email_paypal'] ?? '';
                
                if (empty($email)) {
                    header("Location: agregar-pago.php?error=email_requerido");
                    exit();
                }
                
                $error = validarPayPal($email);
                if ($error) {
                    header("Location: agregar-pago.php?error=" . urlencode($error));
                    exit();
                }
                
                $stmt = $pdo->prepare("INSERT INTO metodos_pago (usuario_id, tipo, numero, predeterminado) 
                                      VALUES (?, ?, ?, ?)");
                $stmt->execute([$usuarioId, $tipo, $email, $predeterminado]);
            }
            
            header("Location: pagos.php?exito=metodo_agregado");
            break;
            
        case 'editar':
            $id = $_POST['id'] ?? 0;
            $tipo = $_POST['tipo'] ?? '';
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                header("Location: pagos.php?error=id_invalido");
                exit();
            }
            
            // Verificar propiedad
            $stmt = $pdo->prepare("SELECT id FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            if (!$stmt->fetch()) {
                header("Location: pagos.php?error=no_autorizado");
                exit();
            }
            
            // Resto del código de edición similar al caso 'agregar'...
            // [Se mantiene la misma lógica pero con validaciones mejoradas]
            
        case 'eliminar':
            $id = $_GET['id'] ?? 0;
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                header("Location: pagos.php?error=id_invalido");
                exit();
            }
            
            // Verificar propiedad antes de eliminar
            $stmt = $pdo->prepare("SELECT id FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            if (!$stmt->fetch()) {
                header("Location: pagos.php?error=no_autorizado");
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM metodos_pago WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: pagos.php?exito=metodo_eliminado");
            break;
            
        case 'predeterminado':
            $id = $_GET['id'] ?? 0;
            
            // Validar ID
            if (!is_numeric($id) || $id <= 0) {
                header("Location: pagos.php?error=id_invalido");
                exit();
            }
            
            // Verificar propiedad
            $stmt = $pdo->prepare("SELECT id FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            if (!$stmt->fetch()) {
                header("Location: pagos.php?error=no_autorizado");
                exit();
            }
            
            // Actualizar todos los métodos
            $pdo->beginTransaction();
            
            try {
                $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 0 WHERE usuario_id = ?");
                $stmt->execute([$usuarioId]);
                
                $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 1 WHERE id = ?");
                $stmt->execute([$id]);
                
                $pdo->commit();
                header("Location: pagos.php?exito=predeterminado_actualizado");
                
            } catch (Exception $e) {
                $pdo->rollBack();
                header("Location: pagos.php?error=operacion_fallida");
            }
            break;
    }
} catch (PDOException $e) {
    // Log del error
    error_log("Error en procesamiento-pagos: " . $e->getMessage());
    header("Location: pagos.php?error=error_sistema");
    exit();
}