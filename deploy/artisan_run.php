<?php
// ══════════════════════════════════════════════
// Artisan Runner - Ejecutar migraciones sin Shell
// BORRAR INMEDIATAMENTE después de usar
// ══════════════════════════════════════════════

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../rehavite_app/vendor/autoload.php';

$app = require_once __DIR__ . '/../rehavite_app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre style='background:#1a1a1a;color:#00ff00;padding:20px;font-family:monospace;'>";
echo "═══ REHAVITE ARTISAN RUNNER ═══\n\n";

// Migrate
echo "▶ php artisan migrate --force\n";
echo "────────────────────────────────\n";
$status = $kernel->call('migrate', ['--force' => true]);
echo $kernel->output();
echo "\nExit code: $status\n\n";

// Seed SiteSettings
echo "▶ php artisan db:seed --class=SiteSettingSeeder\n";
echo "────────────────────────────────\n";
$status2 = $kernel->call('db:seed', ['--class' => 'SiteSettingSeeder', '--force' => true]);
echo $kernel->output();
echo "\nExit code: $status2\n\n";

echo "═══ LISTO - BORRA ESTE ARCHIVO DEL SERVIDOR ═══\n";
echo "</pre>";
