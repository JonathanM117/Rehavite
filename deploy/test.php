<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico Laravel</h2>";

// 1. Limpiar caché vieja de bootstrap
$cacheDir = __DIR__ . '/../rehavite_app/bootstrap/cache/';
$cacheFiles = glob($cacheDir . '*.php');
if ($cacheFiles) {
    echo "<p>Archivos de caché encontrados:</p><ul>";
    foreach ($cacheFiles as $f) {
        echo "<li>" . basename($f) . "</li>";
        @unlink($f); // Borrar caché vieja
    }
    echo "</ul><p><b style='color:orange'>⚠ Caché borrada. Reintenta la página principal.</b></p>";
} else {
    echo "<p style='color:green'>✓ Sin caché vieja.</p>";
}

// 2. Intentar cargar Laravel
try {
    require __DIR__ . '/../rehavite_app/vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoload OK</p>";

    $app = require_once __DIR__ . '/../rehavite_app/bootstrap/app.php';
    echo "<p style='color:green'>✓ Bootstrap OK</p>";

} catch (\Throwable $e) {
    echo "<p style='color:red'><b>ERROR:</b> " . $e->getMessage() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . " línea " . $e->getLine() . "</p>";
}
