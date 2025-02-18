<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

require 'db.php';

try {
    $sql = "SELECT l.nombre_usuario, b.nombre_box, l.hora_llamada
            FROM llamadas l
            LEFT JOIN box b ON l.nombre_box = b.nombre_box";
    $stmt = $pdo->query($sql);
    $llamadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($llamadas);
} catch (PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => $e->getMessage()]);
}
?>