<?php

declare(strict_types=1);

function getConnection(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $host = 'localhost';
        $port = '5432';
        $dbname = 'diamond_bright_cancun';
        $user = 'postgres';
        $password = 'Tegianroren83_'; 

        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $user, $password, $options);
    }
    return $pdo;
}