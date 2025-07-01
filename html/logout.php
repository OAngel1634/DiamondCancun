<?php
session_start();
require_once 'Sesion.php';

if (!empty($_COOKIE['db_session'])) {
    // Eliminar sesión de la base de datos
    $sesion = new Sesion();
    $sesion->eliminar($_COOKIE['db_session']);
    
    // Eliminar cookie
    setcookie('db_session', '', time() - 3600, '/');
}

// Redirigir al inicio de sesión
header("Location: inicio-sesion.php");
exit();
?>