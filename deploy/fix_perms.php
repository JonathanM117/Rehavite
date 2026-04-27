<?php
// Script para arreglar permisos de vendor recursivamente
$vendorPath = __DIR__ . '/../rehavite_app/vendor';

$fixed = 0;
$errors = 0;

function fixPermissions($path, &$fixed, &$errors) {
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $fullPath = $path . '/' . $item;
        if (is_dir($fullPath)) {
            if (@chmod($fullPath, 0755)) $fixed++;
            else $errors++;
            fixPermissions($fullPath, $fixed, $errors);
        } else {
            if (@chmod($fullPath, 0644)) $fixed++;
            else $errors++;
        }
    }
}

echo "<h2>Arreglando permisos de vendor/...</h2>";
fixPermissions($vendorPath, $fixed, $errors);
echo "<p style='color:green'>✓ Arreglados: <b>$fixed</b> archivos/carpetas</p>";
if ($errors > 0) {
    echo "<p style='color:red'>✗ Errores: <b>$errors</b></p>";
}
echo "<p><b>¡Listo! Borra este archivo del servidor y prueba rehavite.com</b></p>";
