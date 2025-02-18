<?php
$host = 'localhost';
$db   = 'cesfam';
$user = 'root';
$pass = ''; // Cambia si tienes contraseña
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Modo de error por excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultados como array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva emulación de sentencias preparadas
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
