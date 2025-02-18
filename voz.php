<?php
require 'db.php';

try {
    $sql = "SELECT l.nombre_usuario, b.nombre_box 
            FROM llamadas l
            LEFT JOIN box b ON l.nombre_box = b.nombre_box";
    $stmt = $pdo->query($sql);
    $llamadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $vozTexto = "";
    foreach ($llamadas as $llamada) {
        $box = $llamada['nombre_box'] ?? "sin asignar";
        $vozTexto .= "El usuario " . $llamada['nombre_usuario'] . " tiene su turno en el box " . $box . ". ";
    }

    if (!empty($vozTexto)) {
        // Enviar el texto como respuesta para que el cliente lo use
        echo json_encode(["texto" => $vozTexto]);
    } else {
        echo json_encode(["texto" => ""]);
    }
} catch (PDOException $e) {
    echo json_encode(["texto" => "Error en la base de datos"]);
}
?>