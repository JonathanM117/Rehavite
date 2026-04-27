<?php
// Crear usuario admin inicial - BORRAR DESPUÉS DE USAR
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../rehavite_app/vendor/autoload.php';
$app = require_once __DIR__ . '/../rehavite_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<pre style='background:#1a1a1a;color:#00ff00;padding:20px;font-family:monospace;'>";
echo "═══ CREAR USUARIO ADMINISTRADOR ═══\n\n";

try {
    // Verificar si ya existe el usuario
    $exists = \App\Models\User::where('email', 'admin@rehavite.com')->first();

    if ($exists) {
        echo "⚠ El usuario admin@rehavite.com ya existe.\n";
        echo "  ID: " . $exists->id . "\n";
        echo "  Admin: " . ($exists->admin ? 'SÍ' : 'NO') . "\n";
    } else {
        $user = \App\Models\User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@rehavite.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Rehavite2026!'),
            'admin'    => true,
        ]);

        echo "✓ Usuario administrador creado!\n\n";
        echo "  Email:      admin@rehavite.com\n";
        echo "  Contraseña: Rehavite2026!\n";
        echo "  Admin:      SÍ\n\n";
        echo "  ⚠ CAMBIA LA CONTRASEÑA DESPUÉS DE ENTRAR\n";
    }

    // Contar usuarios existentes
    $total = \App\Models\User::count();
    echo "\nTotal de usuarios en BD: $total\n";

} catch (\Throwable $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n═══ BORRA ESTE ARCHIVO DEL SERVIDOR ═══\n";
echo "</pre>";
