<?php
session_start();
if (!isset($_SESSION['id_funcionario'])) {
    header("Location: login.php");
    exit();
}
require 'db.php';

try {
    // Consulta con JOIN para obtener todos los datos necesarios
    $sql = "SELECT l.nombre_usuario, b.nombre_box, l.hora_llamada, l.segunda_llamada
            FROM llamadas l
            LEFT JOIN box b ON l.nombre_box = b.nombre_box";
    $stmt = $pdo->query($sql);
    $llamadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="text-4xl font-semibold">Sistema de Llamadas</h1>
        <span id="reloj" class="text-4xl font-medium"></span> <!-- Reloj aquí -->
    </header>

    <!-- Contenedor de la tabla -->
    <div class="max-w-4xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4 text-center text-gray-800">Registros de Llamadas</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-separate border border-gray-300">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 text-left">Usuario</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Ubicación</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Hora Llamada</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Segunda llamada</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    <?php foreach ($llamadas as $llamada): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['nombre_usuario']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['nombre_box']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['hora_llamada']); ?></td>
                            <td class="border border-gray-300 px-4 py-2"><?php echo htmlspecialchars($llamada['segunda_llamada']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Actualizar el reloj -->
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
    
    <!-- Obtener y reproducir voz -->
    <script>
    function hablarTexto(texto) {
        const speech = new SpeechSynthesisUtterance(texto);
        speech.lang = "es-ES"; // Configurar idioma español
        window.speechSynthesis.speak(speech);
    }
    function obtenerVoz() {
        $.ajax({
            url: 'voz.php',
            dataType: 'json',
            success: function(respuesta) {
                if (respuesta.texto.trim() !== "") {
                    hablarTexto(respuesta.texto);
                }
            },
            error: function() {
                console.error("Error al obtener la voz.");
            }
        });
    }
    obtenerVoz(); // Llamar al cargar la página
    setInterval(obtenerVoz, 3000); // Llamar cada 3 segundos
    </script>

</body>
</html>
