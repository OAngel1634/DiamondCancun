<?php
// Detectar si estamos en Railway o en local
$db_host = getenv('MYSQLHOST') ?: 'localhost';
$db_user = getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: ''; // Pon tu clave de local aquí si tienes
$db_name = getenv('MYSQL_DATABASE') ?: 'diamond_bright'; // Tu BD local
$db_port = getenv('MYSQLPORT') ?: 3306;

// Crear conexión
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// Verificar conexión (y matar el proceso si falla)
if (!$conn) {
    // IMPORTANTE: Esto imprimirá el error real en pantalla para que sepamos qué pasa
    die("Fallo fatal de conexión: " . mysqli_connect_error()); 
}

// Opcional: Para que acepte acentos y ñ
mysqli_set_charset($conn, "utf8");
?>