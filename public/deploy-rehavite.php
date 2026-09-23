<?php
/**
 * Script temporal para ejecutar comandos de Laravel en servidores sin acceso a terminal (SSH)
 * INSTRUCCIONES:
 * 1. Sube este archivo a la carpeta "public" de tu servidor (donde está el index.php)
 * 2. Visita en tu navegador: tudominio.com/deploy-rehavite.php
 * 3. CUANDO TERMINE, ¡BORRA ESTE ARCHIVO POR SEGURIDAD!
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto;'>";
echo "<h1>🚀 Despliegue Rehavité (Con limpieza de OPcache)</h1>";

try {
    // 0. Limpiar OPcache de PHP (Crítico cuando se reemplazan archivos PHP)
    echo "<h3>🔄 Limpiando OPcache de PHP...</h3>";
    if (function_exists('opcache_reset')) {
        opcache_reset();
        echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>OPcache limpiado exitosamente.</pre>";
    } else {
        echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>OPcache no está habilitado o no disponible. No es necesario limpiarlo.</pre>";
    }

    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    // 1. Limpiar todas las cachés
    echo "<h3>🧹 Limpiando Cachés de Laravel...</h3>";
    $kernel->call('optimize:clear');
    echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>" . $kernel->output() . "</pre>";

    // 2. Storage Link
    echo "<h3>📁 Configurando Storage (Archivos y Fotos)...</h3>";
    if (file_exists(__DIR__ . '/storage') && is_link(__DIR__ . '/storage')) {
        unlink(__DIR__ . '/storage');
    }
    $kernel->call('storage:link');
    echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>" . $kernel->output() . "</pre>";

    // 3. Migraciones
    echo "<h3>🗄️ Ejecutando Migraciones (Base de Datos)...</h3>";
    $kernel->call('migrate', ['--force' => true]);
    echo "<pre style='background:#f4f4f4; padding:10px; border-radius:5px;'>" . $kernel->output() . "</pre>";

    echo "<h2 style='color: green;'>✅ ¡Proceso completado con éxito!</h2>";
    echo "<p style='color: red; font-weight: bold;'>⚠️ MUY IMPORTANTE: Por seguridad, borra este archivo (deploy-rehavite.php) de tu servidor ahora mismo.</p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Ocurrió un error:</h2>";
    echo "<pre style='background:#ffebee; padding:10px; border-radius:5px;'>" . $e->getMessage() . "</pre>";
}

echo "</div>";
