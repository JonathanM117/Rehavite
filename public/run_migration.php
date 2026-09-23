<?php
/**
 * Rehavite — Ejecutar migración de campos de eliminación
 * Accede via: https://admin.rehavite.com/run_migration.php?key=rehavite2024deploy
 * ELIMINA este archivo después de usarlo.
 */

$secret = $_GET['key'] ?? '';
if ($secret !== 'rehavite2024deploy') {
    die('Acceso denegado.');
}

$basePath = dirname(__DIR__);
$php      = '/usr/local/bin/php';

echo '<pre style="background:#1a1a2e; color:#14b8a6; padding:20px; font-size:13px;">';
echo "=== Rehavite Migration Runner ===\n";
echo "Directorio: $basePath\n\n";

$action = $_GET['action'] ?? 'status';

switch ($action) {
    case 'status':
        $out = shell_exec("$php {$basePath}/artisan migrate:status 2>&1");
        echo htmlspecialchars($out ?: 'Sin salida.');
        break;

    case 'migrate':
        $out = shell_exec("$php {$basePath}/artisan migrate --force 2>&1");
        echo htmlspecialchars($out ?: 'Sin salida.');
        break;

    case 'cache':
        $out = shell_exec("$php {$basePath}/artisan config:clear 2>&1 && $php {$basePath}/artisan route:clear 2>&1 && $php {$basePath}/artisan view:clear 2>&1");
        echo htmlspecialchars($out ?: 'Sin salida.');
        break;
}

echo "</pre><hr>";
$b = '?key=rehavite2024deploy&action=';
echo "<ul style='font-family:sans-serif;'>";
echo "<li><a href='{$b}status'>Ver estado de migraciones</a></li>";
echo "<li><a href='{$b}migrate'>Ejecutar migraciones pendientes</a></li>";
echo "<li><a href='{$b}cache'>Limpiar caché</a></li>";
echo "</ul>";
echo '<p style="color:red; font-family:sans-serif;"><strong>⚠ Elimina este archivo al terminar.</strong></p>';
