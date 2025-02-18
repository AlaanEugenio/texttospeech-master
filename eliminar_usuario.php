<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre_usuario'])) {
    try {
        $sql_eliminar = "DELETE FROM llamadas WHERE nombre_usuario = :nombre_usuario";
        $stmt = $pdo->prepare($sql_eliminar);
        $stmt->execute([':nombre_usuario' => $_POST['nombre_usuario']]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => "Usuario eliminado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró el usuario."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Solicitud inválida."]);
}
?>