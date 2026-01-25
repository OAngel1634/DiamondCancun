<?php
echo "<h1>Diagnóstico CSS</h1>";

// 1. Verificar archivo CSS
$cssPath = __DIR__ . '/css/styles.css';
echo "<h2>1. Archivo CSS:</h2>";
echo "<p>Ruta: $cssPath</p>";
echo "<p>Existe: " . (file_exists($cssPath) ? '✅ Sí' : '❌ No') . "</p>";
if (file_exists($cssPath)) {
    echo "<p>Tamaño: " . filesize($cssPath) . " bytes</p>";
    echo "<p>Permisos: " . substr(sprintf('%o', fileperms($cssPath)), -4) . "</p>";
}

// 2. Verificar estructura de carpetas
echo "<h2>2. Estructura de carpetas:</h2>";
function listarDirectorio($dir, $nivel = 0) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $ruta = $dir . '/' . $item;
        $espacio = str_repeat('&nbsp;&nbsp;', $nivel);
        if (is_dir($ruta)) {
            echo $espacio . "📁 " . $item . "<br>";
            listarDirectorio($ruta, $nivel + 1);
        } else {
            $icono = pathinfo($item, PATHINFO_EXTENSION) == 'css' ? '🎨' : '📄';
            echo $espacio . $icono . " " . $item . " (" . filesize($ruta) . " bytes)<br>";
        }
    }
}
listarDirectorio(__DIR__);

// 3. Probar acceso directo
echo "<h2>3. Acceso directo:</h2>";
echo "<p><a href='./css/styles.css' target='_blank'>Abrir styles.css</a></p>";

// 4. Verificar contenido CSS
if (file_exists($cssPath)) {
    echo "<h2>4. Primeras líneas del CSS:</h2>";
    echo "<pre style='background:#f0f0f0;padding:10px;'>";
    $lines = file($cssPath, FILE_IGNORE_NEW_LINES);
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
}
?>