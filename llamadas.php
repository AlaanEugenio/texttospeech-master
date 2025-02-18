<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';

date_default_timezone_set("America/Santiago"); // Zona horaria Santiago, Chile

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hora_actual = date("H:i:s"); // Hora en formato HH:mm:ss
    $nombre_usuario = trim($_POST["nombre"]); // Asegurar que coincida con el name del input en el form

    if (!empty($nombre_usuario)) { // Verifica que el campo nombre no esté vacío
        try {
            $sql_verificar = "SELECT COUNT(*) FROM llamadas WHERE nombre_usuario = :nombre_usuario";
            $stmt_verificar = $pdo->prepare($sql_verificar);
            $stmt_verificar->execute([":nombre_usuario" => $nombre_usuario]);
            $existe = $stmt_verificar->fetchColumn();

            if ($existe > 0) {
                $mensaje = "El usuario ya fue ingresado.";
            } else {
                $sql_box = "SELECT nombre_box FROM box WHERE nombre_box = :nombre_box"; 
                $stmt_box = $pdo->prepare($sql_box);
                $stmt_box->execute([':nombre_box' => $_SESSION['nombre_box']]);
                $nombre_box = $stmt_box->fetchColumn();

                if (!$nombre_box) {
                    $mensaje = "No hay boxes disponibles.";
                } else {
                    $sql_insertar = "INSERT INTO llamadas (nombre_usuario, hora_llamada, nombre_box) 
                                     VALUES (:nombre_usuario, :hora_llamada, :nombre_box)";
                    $stmt = $pdo->prepare($sql_insertar);

                    if ($stmt->execute([":nombre_usuario" => $nombre_usuario, ":hora_llamada" => $hora_actual, ":nombre_box" => $nombre_box])) {
                        $mensaje = "Usuario ingresado correctamente.";
                    } else {
                        $mensaje = "Error al registrar los datos.";
                    }
                }
            }
        } catch (PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $mensaje = "Por favor, ingresa un nombre.";
    }
}

// Consulta para obtener los registros de llamadas
$sql_obtener = "SELECT l.nombre_usuario, l.hora_llamada, l.segunda_llamada, 
                       b.nombre_box, s.nombre_sector, p.numero AS pisos
                FROM llamadas l
                JOIN box b ON l.nombre_box = b.nombre_box
                JOIN sectores s ON b.id_sector = s.id
                JOIN pisos p ON s.id = p.id";
$stmt_obtener = $pdo->prepare($sql_obtener);
$stmt_obtener->execute();
$pacientes = $stmt_obtener->fetchAll(PDO::FETCH_ASSOC);

// Obtener datos de sesión
$nomb_func = isset($_SESSION['nomb_func']) ? $_SESSION['nomb_func'] : "No identificado";
$nombre_box = isset($_SESSION['nombre_box']) ? $_SESSION['nombre_box'] : "No asignado";

// Obtener el sector y piso del box actual
$sql_sector_piso = "SELECT s.nombre_sector, p.numero AS pisos
                    FROM sectores s
                    JOIN pisos p ON p.id = s.id
                    JOIN box b ON b.id_sector = s.id
                    WHERE b.nombre_box = :nombre_box";
$stmt_sector_piso = $pdo->prepare($sql_sector_piso);
$stmt_sector_piso->execute([':nombre_box' => $nombre_box]);
$sector_piso = $stmt_sector_piso->fetch(PDO::FETCH_ASSOC);

$nombre_sector = $sector_piso ? $sector_piso['nombre_sector'] : "No asignado";
$pisos = $sector_piso ? $sector_piso['pisos'] : "No disponible";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Llamada usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen p-6">

<header class="w-full bg-blue-600 text-white py-4 px-6 flex justify-between items-center shadow-md fixed top-0 py-4 px-6">
    <h1 class="text-3xl font-semibold">Sistema de llamadas</h1>
    <div class="text-sm">
        <p class="text-xl"><strong>Funcionario:</strong> <?= htmlspecialchars($nomb_func) ?></p>
        <p class="text-xl"><strong>Piso:</strong> <?= htmlspecialchars($pisos) ?><strong>  Sector:</strong> <?= htmlspecialchars($nombre_sector) ?></p>
        <p class="text-xl"><strong>Box actual:</strong> <?= htmlspecialchars($nombre_box) ?></p>
    </div>
</header>

<div class="bg-white p-6 rounded-lg shadow-md w-96 mb-6 my-[100px]">
    <?php if (isset($mensaje)): ?>
        <div class="mb-4 text-center text-green-600 font-semibold">
            <?= htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre completo" required
                   class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
        <div class="mt-4">
            <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded-xl transition duration-300">
                Ingresar
            </button>
        </div>
    </form>
</div>

<div class="bg-white p-4 rounded-lg shadow-md w-[90%] max-w-3xl">
    <h2 class="text-lg font-semibold text-center mb-4">Registros de Llamadas</h2>
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-400 px-4 py-2">Usuario</th>
                    <th class="border border-gray-400 px-4 py-2">Hora llamada</th>
                    <th class="border border-gray-400 px-4 py-2">Hora segunda llamada</th>
                    <th class="border border-gray-400 px-4 py-2">Ubicación</th>
                    <th class="border border-gray-400 px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($pacientes) > 0): ?>
                    <?php foreach ($pacientes as $usuario): ?>
                        <tr class="text-center">
                            <td class="border border-gray-400 px-4 py-2"><?= htmlspecialchars($usuario['nombre_usuario']) ?></td>
                            <td class="border border-gray-400 px-4 py-2"><?= htmlspecialchars($usuario['hora_llamada']) ?></td>
                            <td class="border border-gray-400 px-4 py-2"><?= htmlspecialchars($usuario['segunda_llamada']) ?></td>
                            <td class="border border-gray-400 px-4 py-2">
                                <strong></strong> <?= htmlspecialchars($usuario['nombre_box']) ?><br>
                                <strong></strong> <?= htmlspecialchars($usuario['pisos']) ?><br>
                                <strong></strong> <?= htmlspecialchars($usuario['nombre_sector']) ?>
                            </td>                            
                            <td class="border border-gray-400 px-4 py-2 justify-center">
                                <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition" 
                                    onclick="eliminarUsuario('<?= htmlspecialchars($usuario['nombre_usuario']) ?>', this)">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                                <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition"
                                    onclick="registrarSegundaLlamada('<?= htmlspecialchars($usuario['nombre_usuario']) ?>', this)">
                                    <span class="material-symbols-outlined">campaign</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="4" class="border border-gray-400 px-4 py-2 text-center text-gray-500">No hay registros</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function eliminarUsuario(nombreUsuario, btn) {
    let formData = new FormData();
    formData.append("nombre_usuario", nombreUsuario);

    fetch("eliminar_usuario.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            let row = btn.closest("tr"); // Encuentra la fila del usuario
            row.remove(); // Elimina la fila sin recargar la página
        }
    })
    .catch(error => console.error("Error:", error));
}

function registrarSegundaLlamada(nombreUsuario, btn) {
    let formData = new FormData();
    formData.append("nombre_usuario", nombreUsuario);

    fetch("segunda_llamada.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        if (data.success) {
            // Actualizar la interfaz si es necesario
        }
    })
    .catch(error => console.error("Error:", error));
}
</script>
</body>
</html>

