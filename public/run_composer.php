<?php
/**
 * Rehavite — Script de deploy para instalar dependencias en servidor
 * IMPORTANTE: Elimina este archivo del servidor después de usarlo.
 * Accede via: https://tudominio.com/run_composer.php
 */

// Seguridad básica — cambia esta clave
$secret = $_GET['key'] ?? '';
if ($secret !== 'rehavite2024deploy') {
    die('Acceso denegado. Usa ?key=rehavite2024deploy');
}

$action = $_GET['action'] ?? 'install';
$basePath = dirname(__DIR__); // raíz del proyecto

echo '<pre style="background:#1a1a2e; color:#14b8a6; padding:20px; font-size:13px;">';
echo "=== Rehavite Deploy Tool ===\n";
echo "Directorio: $basePath\n\n";

switch ($action) {
    case 'install':
        echo "▶ Ejecutando: composer install --no-dev --optimize-autoloader\n\n";
        $output = shell_exec("cd $basePath && composer install --no-dev --optimize-autoloader 2>&1");
        echo htmlspecialchars($output ?: 'Sin salida. Revisa permisos de shell_exec.');
        break;

    case 'autoload':
        echo "▶ Ejecutando: composer dump-autoload\n\n";
        $output = shell_exec("cd $basePath && composer dump-autoload 2>&1");
        echo htmlspecialchars($output ?: 'Sin salida.');
        break;

    case 'cache':
        echo "▶ Limpiando caché de Laravel...\n\n";
        $output = shell_exec("cd $basePath && php artisan cache:clear && php artisan config:clear && php artisan view:clear && php artisan route:clear 2>&1");
        echo htmlspecialchars($output ?: 'Sin salida.');
        break;

    case 'check':
        echo "▶ Verificando extensiones PHP...\n\n";
        $exts = ['gd', 'zip', 'mbstring', 'openssl', 'pdo', 'xml'];
        foreach ($exts as $ext) {
            $status = extension_loaded($ext) ? '✓' : '✗ FALTA';
            echo "  $ext: $status\n";
        }
        echo "\nPHP versión: " . PHP_VERSION . "\n";
        echo "Composer check: ";
        echo shell_exec("composer --version 2>&1") ?: "No encontrado\n";
        break;

    default:
        echo "Acción desconocida. Usa ?action=install | autoload | cache | check\n";
}

echo '</pre>';
echo '<hr>';
echo '<p style="font-family:sans-serif; color:#666;">Acciones disponibles:</p>';
echo '<ul style="font-family:monospace;">';
$base = '?key=rehavite2024deploy';
echo "<li><a href='{$base}&action=check'>Verificar extensiones</a></li>";
echo "<li><a href='{$base}&action=install'>Instalar dependencias (composer install)</a></li>";
echo "<li><a href='{$base}&action=autoload'>Regenerar autoload</a></li>";
echo "<li><a href='{$base}&action=cache'>Limpiar caché Laravel</a></li>";
echo '</ul>';
echo '<p style="color:red; font-family:sans-serif;"><strong>⚠ Elimina este archivo del servidor después de usar.</strong></p>';
