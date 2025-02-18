<?php
session_start();
require 'db.php';  // Asegúrate de que 'db.php' contiene la conexión a la BD

$mensaje = "";

// Manejar la solicitud AJAX para obtener ubicaciones
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['fetch_locations'])) {
    try {
        $sql = "SELECT box.id, box.nombre_box, pisos.numero AS piso, sectores.nombre AS sector 
                FROM box
                INNER JOIN pisos ON box.id_piso = pisos.id
                INNER JOIN sectores ON box.id_sector = sectores.id";
                
        $stmt = $pdo->query($sql);
        $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verifica si hay datos y genera la respuesta JSON
        if ($ubicaciones) {
            echo json_encode($ubicaciones); // Devuelve los datos como JSON
        } else {
            echo json_encode([]); // Si no hay datos, devolver un array vacío
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    }
    exit();
}

// Verificar si hay un mensaje de éxito en el registro
if (isset($_GET['mensaje']) && $_GET['mensaje'] === 'registro_exitoso') {
    $mensaje = "¡Registro exitoso! Ahora puedes iniciar sesión.";
}

// Procesar inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = preg_replace('/[^0-9kK]/', '', $_POST['rut']);
    $clave = $_POST['clave'];
    $ubicacion_id = $_POST['ubicacion']; // Ubicación seleccionada (id del box)

    // Consulta para buscar usuario por RUT
    $sql = "SELECT id_funcionario, nom_func, rut, clave FROM funcionarios WHERE rut = :rut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':rut' => $rut]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        if (password_verify($clave, $usuario['clave'])) {
            $_SESSION['nom_func'] = $usuario['nom_func'];
            $_SESSION['id_funcionario'] = $usuario['id_funcionario'];
            $_SESSION['ubicacion_id'] = $ubicacion_id;

            // Buscar detalles del box seleccionado
            $sql_box = "SELECT nombre_box FROM box WHERE id = :id";
            $stmt_box = $pdo->prepare($sql_box);
            $stmt_box->execute([':id' => $ubicacion_id]);
            $box = $stmt_box->fetch(PDO::FETCH_ASSOC);

            if ($box) {
                $_SESSION['nombre_box'] = $box['nombre_box'];
            }

            header("Location: llamadas.php");
            exit();
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "RUT no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            // Cargar ubicaciones dinámicamente
            $.get("login.php?fetch_locations=1", function (data) {
                let locations = JSON.parse(data);
                let select = $("#ubicacion");

                select.empty(); // Limpiar opciones previas
                select.append('<option value="">Seleccione una ubicación</option>');

                locations.forEach(function (loc) {
                    select.append(`<option value="${loc.id}">${loc.piso} - ${loc.sector} - ${loc.nombre_box} </option>`);
                });
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Error al cargar ubicaciones: ", textStatus, errorThrown);
            });
        });
    </script>
</head>
<body class="bg-[#6693C7] flex items-center justify-center min-h-screen">
    <div class="bg-[#C9CDD5] shadow-xl rounded-2xl p-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Iniciar Sesión</h2>

        <?php if (!empty($mensaje)): ?>
            <div class="mb-4 p-3 rounded-md text-center 
                        <?php echo strpos($mensaje, '¡Registro exitoso!') !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="rut" class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="rut" id="rut" placeholder="Ingresa tu RUT sin puntos ni verificador" required
                       class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="clave" class="block text-sm font-medium text-gray-700">Clave</label>
                <input type="password" name="clave" id="clave" placeholder="Clave" required
                       class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Menú desplegable de ubicaciones -->
            <div>
                <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
                <select name="ubicacion" id="ubicacion" required
                    class="mt-1 w-full px-4 py-2 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Cargando ubicaciones...</option>
                </select>
            </div>

            <button type="submit"
                    class="w-full bg-[#6693C7] hover:bg-blue-600 text-white font-semibold py-2 rounded-xl transition duration-300">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-gray-600">Registro</p>
            <a href="registro.php" 
               class="inline-block mt-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-xl transition duration-300">
                Registrarse
            </a>
        </div>
    </div>
</body>
</html>