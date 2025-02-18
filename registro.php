<?php
require 'db.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $rut = $_POST['rut'];
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);

    $sql_verificar = "SELECT COUNT(*) FROM funcionarios WHERE rut = :rut";
    $stmt_verificar = $pdo->prepare($sql_verificar);
    $stmt_verificar->execute([':rut' => $rut]);
    $existe = $stmt_verificar->fetchColumn();

    if ($existe) {
        $mensaje = "El RUT ya está registrado. Intenta con otro.";
    } else {
        // Insertar nuevo registro
        $sql_insertar = "INSERT INTO funcionarios (nomb_func, rut, clave) VALUES (:nombre, :rut, :clave)";
        $stmt_insertar = $pdo->prepare($sql_insertar);

        try {
            $stmt_insertar->execute([
                ':nombre'     => $nombre,
                ':rut'        => $rut,
                ':clave' => $clave
            ]);

            header("Location: login.php?mensaje=registro_exitoso");
            exit(); // Importante para detener la ejecución después de la redirección

        } catch (PDOException $e) {
            $mensaje = "Error en el registro: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registro de Funcionarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Registro de Funcionarios</h2>

        <?php if ($mensaje): ?>
            <div class="mb-4 p-3 rounded-md text-center bg-red-100 text-red-700">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required
                    class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="rut" class="block text-sm font-medium text-gray-700">RUT</label>
                <input type="text" name="rut" id="rut" placeholder="Ingresa tu rut sin puntos ni verificador" required
                    class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="clave" class="block text-sm font-medium text-gray-700">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Crea una clave segura" required
                    class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-xl transition duration-300">
                Registrarse
            </button>
        </form>
    </div>
</body>

</html>