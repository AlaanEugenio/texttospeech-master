<?php
session_start();
require 'db.php';

date_default_timezone_set("America/Santiago"); // Zona horaria Santiago, Chile

$response = ["success" => false, "message" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hora_actual = date("H:i:s"); // Hora en formato HH:mm:ss
    $nombre_usuario = trim($_POST["nombre_usuario"]); // Obtener el nombre del usuario desde el POST

    if (!empty($nombre_usuario)) { // Verifica que el campo nombre no esté vacío
        try {
            // Actualizar la tabla de llamadas con la hora de la segunda llamada
            $sql_actualizar = "UPDATE llamadas SET segunda_llamada = :segunda_llamada WHERE nombre_usuario = :nombre_usuario";
            $stmt = $pdo->prepare($sql_actualizar);

            if ($stmt->execute([":segunda_llamada" => $hora_actual, ":nombre_usuario" => $nombre_usuario])) {
                $response["success"] = true;
                $response["message"] = "Hora de la segunda llamada registrada correctamente.";
            } else {
                $response["message"] = "Error al registrar la segunda llamada.";
            }
        } catch (PDOException $e) {
            $response["message"] = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $response["message"] = "El nombre del usuario no puede estar vacío.";
    }
} else {
    $response["message"] = "Método de solicitud no válido.";
}

echo json_encode($response);
?>