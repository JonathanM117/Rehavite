<?php

/**
 * Ejecutar migraciones y limpiar cache sin terminal.
 * ELIMINAR ESTE ARCHIVO DESPUÉS DE USARLO.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h2>🔧 Rehavité — Mantenimiento del Servidor</h2>";
echo "<pre style='background:#1a1a2e;color:#0f0;padding:20px;border-radius:8px;font-size:14px;'>";

// 1. Migrar tablas nuevas
echo "▶ Ejecutando migraciones...\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "✅ Migraciones completadas.\n\n";
} catch (Exception $e) {
    echo "❌ Error en migraciones: " . $e->getMessage() . "\n\n";
}

// 2. Limpiar cache de configuración
echo "▶ Limpiando cache de configuración...\n";
Artisan::call('config:cache');
echo Artisan::output();
echo "✅ Cache de configuración actualizado.\n\n";

// 3. Limpiar cache de vistas
echo "▶ Limpiando cache de vistas...\n";
Artisan::call('view:clear');
echo Artisan::output();
echo "✅ Cache de vistas limpiado.\n\n";

// 4. Limpiar cache general
echo "▶ Limpiando cache general...\n";
Artisan::call('cache:clear');
echo Artisan::output();
echo "✅ Cache general limpiado.\n\n";

echo "═══════════════════════════════\n";
echo "🎉 ¡Todo listo! Elimina este archivo (deploy.php) del servidor.\n";
echo "</pre>";
