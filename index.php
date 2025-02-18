<?php
require 'db.php';

try {
    // Consulta con JOIN para obtener todos los datos necesarios
    $sql = "SELECT l.nombre_usuario, b.nombre_box, l.hora_llamada
            FROM llamadas l
            LEFT JOIN box b ON l.nombre_box = b.nombre_box"; // Ajusta 'box_id' si es diferente
    $stmt = $pdo->query($sql);
    $llamadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $voice = new COM("SAPI.SpVoice");
    $vozTexto = "";

    foreach ($llamadas as $llamada) {
        $box = $llamada['nombre_box'] ?? "sin asignar";
        $hora = $llamada['hora_llamada'] ?? "sin hora";
        $vozTexto .= "El usuario " . $llamada['nombre_usuario'] . " tiene su turno en el box " . $box . " . ";
    }

    if (!empty($vozTexto)) {
        $voice->Speak($vozTexto);
    }
} catch (PDOException $e) {
    die("Error en la base de datos: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage()); // Captura otros errores
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Llamadas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- Encabezado -->
    <header class="bg-blue-600 text-white py-4 px-6 shadow-md flex justify-between items-center">
    <h1 class="text-2xl font-semibold">Sistema de Llamadas</h1>
    <span id="reloj" class="text-lg font-medium"></span> <!-- Reloj aquí -->
</header>


    <!-- Contenedor de la tabla -->
    <div class="max-w-4xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">Registros de Llamadas</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-separate border border-gray-300">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Usuario</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Box</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Hora Llamada</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php foreach ($llamadas as $llamada): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['nombre_usuario']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['nombre_box']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['hora_llamada']); ?></td>
                            <td class="border border-gray-300 px-4 py-2">
                                <?php echo isset($llamada['estado']) ? "Llamando" : "Sin llamar"; ?> 
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
    function actualizarReloj() {
        $.ajax({
            url: 'reloj.php', // Archivo PHP que devuelve la hora
            success: function(hora) {
                $('#reloj').text(hora); // Actualiza el span con la hora
            }
        });
    }

    setInterval(actualizarReloj, 1000); // Llamar cada 1 segundo
    actualizarReloj(); // Llamar una vez al cargar la página
</script>

</body>
</html>
