<?php

$host = 'localhost';
$dbname = 'cesfam';
$username = 'root';
$password = '';

try {
	$pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die("Error de conexión: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT id, nombre FROM pacientes ORDER BY nombre ASC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$voice = new COM("SAPI.SpVoice");

// Generar la frase que se convertirá a voz
$vozTexto = "";
foreach ($rows as $row) {
	// Formar un texto con los datos de cada paciente
	$vozTexto .= "El usuario " . $row['nombre'] . " tiene su turno. ";
}

// Convertir a voz inmediatamente con los datos extraídos
if (!empty($vozTexto)) {
	$voice->Speak($vozTexto);  // Convierte el texto formado a voz
}

?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="robots" content="noindex, nofollow">
	<title>Llamada usuarios</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Tailwind CSS CDN -->
	<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
	<nav class="bg-gray-800 text-white p-4">
		<div class="flex justify-between items-center">
			<div id="clock"></div>
			<div id="date"><?php echo date('l, F j, Y'); ?></div>
		</div>
	</nav>

	<div class="max-w-4xl mx-auto p-6">
		<div class="bg-blue-600 text-white p-4 rounded-md shadow-md">
			<center>
				<strong class="text-lg underline">Usuario(a) llamado</strong>
			</center>
		</div>
	</div>

	<br>
	<!-- Tabla con los campos solicitados y datos de la base de datos -->
	<div class="max-w-full overflow-x-auto">
		<table class="min-w-full table-auto border-collapse border border-gray-300">
			<thead>
				<tr class="bg-gray-200">
					<th class="border border-gray-300 px-2 py-1 text-xs">Nombre</th>
					<th class="border border-gray-300 px-2 py-1 text-xs">Rut</th>
					<th class="border border-gray-300 px-2 py-1 text-xs">Repetición</th>
					<th class="border border-gray-300 px-2 py-1 text-xs">Pasillo</th>
					<th class="border border-gray-300 px-2 py-1 text-xs">Box</th>
					<th class="border border-gray-300 px-2 py-1 text-xs">Estado</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $row): ?>
					<tr>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['nombre']); ?></td>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['id']); ?></td>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['repeticion']); ?></td>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['pasillo']); ?></td>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['box']); ?></td>
						<td class="border border-gray-300 px-2 py-1 text-xs"><?php echo htmlspecialchars($row['estado']); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<br>

	<script src="js/script.js"></script>
</body>

</html>