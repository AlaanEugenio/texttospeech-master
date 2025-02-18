<?php
setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain', 'es_ES'); // Configura el idioma a español
date_default_timezone_set('America/Santiago');

echo strftime("%d de %B de %Y, %H:%M:%S"); // Ejemplo: 17 de febrero de 2025, 14:45:30
?>
