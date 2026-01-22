<?php
session_start();

// Destruir solo las variables de sesión del admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nombre']);
unset($_SESSION['admin_rol']);

// Redirigir al login del panel
header('Location: login.php');
exit;
?>