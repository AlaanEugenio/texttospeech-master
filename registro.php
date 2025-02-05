<?php
require 'db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rut = $_POST['rut'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Verificar si el RUT ya está registrado
    $sql_verificar = "SELECT COUNT(*) FROM funcionarios WHERE rut = :rut";
    $stmt_verificar = $pdo->prepare($sql_verificar);
    $stmt_verificar->execute([':rut' => $rut]);
    $existe = $stmt_verificar->fetchColumn();

    if ($existe) {
        $mensaje = "El RUT ya está registrado. Intenta con otro.";
    } else {
        // Insertar nuevo registro
        $sql_insertar = "INSERT INTO funcionarios (nombre, rut, contrasena) VALUES (:nombre, :rut, :contrasena)";
        $stmt_insertar = $pdo->prepare($sql_insertar);

        try {
            $stmt_insertar->execute([
                ':nombre'     => $nombre,
                ':rut'        => $rut,
                ':contrasena' => $contrasena
            ]);

            // ✅ Redirigir al login después del registro exitoso
            header("Location: login.php?mensaje=registro_exitoso");
            exit(); // Importante para detener la ejecución después de la redirección

        } catch (PDOException $e) {
            $mensaje = "Error en el registro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Formulario de Registro SCIII</title>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>





</html>



