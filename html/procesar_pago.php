<?php
session_start();
require_once 'conexion.php';

// Verificar sesión activa
if (empty($_SESSION['usuario_id'])) {
    header("Location: inicio-sesion.php");
    exit();
}

$usuarioId = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

$database = new Database();
$pdo = $database->connect();

// Función para validar tarjeta
function validarTarjeta($numero, $expiracion, $cvv) {
    // Validar número de tarjeta (solo dígitos, 13 a 16 dígitos)
    if (!preg_match('/^[0-9]{13,16}$/', $numero)) {
        return 'El número de tarjeta no es válido.';
    }
    
    // Validar fecha de expiración (formato MM/YY y debe ser fecha futura)
    if (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $expiracion, $matches)) {
        return 'La fecha de expiración debe tener el formato MM/YY.';
    }
    
    $mes = $matches[1];
    $ano = $matches[2];
    $anoCompleto = 2000 + intval($ano);
    $fechaExpiracion = DateTime::createFromFormat('Y-m', "$anoCompleto-$mes");
    $hoy = new DateTime();
    $hoy->modify('first day of this month');
    
    if ($fechaExpiracion < $hoy) {
        return 'La fecha de expiración debe ser en el futuro.';
    }
    
    // Validar CVV (3 o 4 dígitos)
    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        return 'El CVV debe tener 3 o 4 dígitos.';
    }
    
    return null;
}

// Función para validar PayPal
function validarPayPal($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'El email de PayPal no es válido.';
    }
    return null;
}

try {
    switch ($accion) {
        case 'agregar':
            $tipo = $_POST['tipo'];
            $predeterminado = isset($_POST['predeterminado']) ? 1 : 0;
            $usuarioId = $_POST['usuario_id'];
            
            // Si se marca como predeterminado, quitar el predeterminado actual
            if ($predeterminado) {
                $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 0 WHERE usuario_id = ?");
                $stmt->execute([$usuarioId]);
            }
            
            if ($tipo === 'tarjeta') {
                $titular = $_POST['titular'];
                $numero = $_POST['numero'];
                $expiracion = $_POST['expiracion'];
                $cvv = $_POST['cvv'];
                
                $error = validarTarjeta($numero, $expiracion, $cvv);
                if ($error) {
                    header("Location: agregar-pago.php?error=" . urlencode($error));
                    exit();
                }
                
                $stmt = $pdo->prepare("INSERT INTO metodos_pago (usuario_id, tipo, titular, numero, expiracion, cvv, predeterminado) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$usuarioId, $tipo, $titular, $numero, $expiracion, $cvv, $predeterminado]);
            } elseif ($tipo === 'paypal') {
                $email = $_POST['email_paypal'];
                
                $error = validarPayPal($email);
                if ($error) {
                    header("Location: agregar-pago.php?error=" . urlencode($error));
                    exit();
                }
                
                $stmt = $pdo->prepare("INSERT INTO metodos_pago (usuario_id, tipo, numero, predeterminado) VALUES (?, ?, ?, ?)");
                $stmt->execute([$usuarioId, $tipo, $email, $predeterminado]);
            }
            
            header("Location: pagos.php?exito=agregado");
            break;
            
        case 'editar':
            $id = $_POST['id'];
            $tipo = $_POST['tipo'];
            $predeterminado = isset($_POST['predeterminado']) ? 1 : 0;
            $usuarioId = $_POST['usuario_id'];
            
            // Verificar que el método pertenece al usuario
            $stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            $metodo = $stmt->fetch();
            
            if (!$metodo) {
                header("Location: pagos.php?error=no_encontrado");
                exit();
            }
            
            // Si se marca como predeterminado, quitar el predeterminado actual
            if ($predeterminado) {
                $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 0 WHERE usuario_id = ?");
                $stmt->execute([$usuarioId]);
            }
            
            if ($tipo === 'tarjeta') {
                $titular = $_POST['titular'];
                $numero = $_POST['numero'];
                $expiracion = $_POST['expiracion'];
                $cvv = $_POST['cvv'];
                
                $error = validarTarjeta($numero, $expiracion, $cvv);
                if ($error) {
                    header("Location: editar-pago.php?id=$id&error=" . urlencode($error));
                    exit();
                }
                
                $stmt = $pdo->prepare("UPDATE metodos_pago SET tipo = ?, titular = ?, numero = ?, expiracion = ?, cvv = ?, predeterminado = ? WHERE id = ?");
                $stmt->execute([$tipo, $titular, $numero, $expiracion, $cvv, $predeterminado, $id]);
            } elseif ($tipo === 'paypal') {
                $email = $_POST['email_paypal'];
                
                $error = validarPayPal($email);
                if ($error) {
                    header("Location: editar-pago.php?id=$id&error=" . urlencode($error));
                    exit();
                }
                
                $stmt = $pdo->prepare("UPDATE metodos_pago SET tipo = ?, numero = ?, predeterminado = ? WHERE id = ?");
                $stmt->execute([$tipo, $email, $predeterminado, $id]);
            }
            
            header("Location: pagos.php?exito=actualizado");
            break;
            
        case 'eliminar':
            $id = $_GET['id'];
            
            // Verificar que el método pertenece al usuario
            $stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            $metodo = $stmt->fetch();
            
            if (!$metodo) {
                header("Location: pagos.php?error=no_encontrado");
                exit();
            }
            
            $stmt = $pdo->prepare("DELETE FROM metodos_pago WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: pagos.php?exito=eliminado");
            break;
            
        case 'predeterminado':
            $id = $_GET['id'];
            
            // Verificar que el método pertenece al usuario
            $stmt = $pdo->prepare("SELECT * FROM metodos_pago WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$id, $usuarioId]);
            $metodo = $stmt->fetch();
            
            if (!$metodo) {
                header("Location: pagos.php?error=no_encontrado");
                exit();
            }
            
            // Quitar el predeterminado actual
            $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 0 WHERE usuario_id = ?");
            $stmt->execute([$usuarioId]);
            
            // Establecer este como predeterminado
            $stmt = $pdo->prepare("UPDATE metodos_pago SET predeterminado = 1 WHERE id = ?");
            $stmt->execute([$id]);
            
            header("Location: pagos.php?exito=predeterminado");
            break;
            
        default:
            header("Location: pagos.php?error=general");
            break;
    }
} catch (PDOException $e) {
    header("Location: pagos.php?error=general");
    exit();
}