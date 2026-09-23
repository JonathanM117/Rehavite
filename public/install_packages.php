<?php
/**
 * Rehavite — Auto-instala composer y los paquetes faltantes
 * ELIMINA este archivo después de usarlo.
 */

$secret = $_GET['key'] ?? '';
if ($secret !== 'rehavite2024deploy') {
    die('Acceso denegado.');
}

$basePath      = dirname(__DIR__);
$composerPhar  = $basePath . '/composer.phar';
$action        = $_GET['action'] ?? 'check';

set_time_limit(300); // 5 min máximo

echo '<pre style="background:#1a1a2e; color:#14b8a6; padding:20px; font-size:13px; word-wrap:break-word;">';
echo "=== Rehavite Package Installer v2 ===\n";
echo "Directorio: $basePath\n";
echo "PHP: " . PHP_VERSION . "\n\n";

// ── Encontrar PHP ───────────────────────────────────────────────────────────
function findPhp(): string {
    foreach (['/usr/local/bin/php', '/usr/bin/php', 'php'] as $p) {
        $v = @shell_exec("$p -r 'echo PHP_VERSION;' 2>&1");
        if ($v && strlen($v) < 20) return $p;
    }
    return 'php';
}

// ── Encontrar / descargar composer ─────────────────────────────────────────
function findComposer(string $basePath, string &$composerPhar): string {
    // 1. Buscar en rutas comunes
    $paths = [
        '/usr/local/bin/composer',
        '/usr/bin/composer',
        '/opt/cpanel/composer/bin/composer',
        $basePath . '/composer.phar',
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) return $p;
        $test = @shell_exec("$p --version 2>&1");
        if ($test && strpos($test, 'Composer') !== false) return $p;
    }
    return '';
}

switch ($action) {

    // ── CHECK ──────────────────────────────────────────────────────────────
    case 'check':
        echo "--- Clases PHP ---\n";
        $autoload = $basePath . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
            echo "vendor/autoload.php : ✓\n";
            echo "laravel-dompdf      : " . (class_exists('Barryvdh\DomPDF\PDF') ? "✓ OK\n" : "✗ FALTA\n");
            echo "PhpSpreadsheet      : " . (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet') ? "✓ OK\n" : "✗ FALTA\n");
        } else {
            echo "✗ vendor/autoload.php no existe\n";
        }
        echo "\n--- Extensiones ---\n";
        foreach (['gd','zip','mbstring','xml','openssl'] as $ext) {
            echo "  $ext: " . (extension_loaded($ext) ? '✓' : '✗') . "\n";
        }
        echo "\n--- Composer ---\n";
        $c = findComposer($basePath, $composerPhar);
        echo "  Ruta: " . ($c ?: 'No encontrado') . "\n";
        echo "  composer.phar en proyecto: " . (file_exists($basePath . '/composer.phar') ? '✓' : '✗') . "\n";
        break;

    // ── DESCARGAR COMPOSER ──────────────────────────────────────────────────
    case 'download_composer':
        echo "▶ Descargando composer.phar...\n\n";
        $php = findPhp();
        $composerHome = $basePath . '/.composer_home';
        if (!is_dir($composerHome)) mkdir($composerHome, 0755, true);
        $installerFile = $basePath . '/composer-setup.php';

        // Descargar installer
        $data = @file_get_contents('https://getcomposer.org/installer');
        if (empty($data)) {
            $data = shell_exec("curl -sS https://getcomposer.org/installer 2>&1");
        }
        if (empty($data)) {
            echo "✗ No se pudo descargar el installer\n";
            break;
        }
        file_put_contents($installerFile, $data);
        echo "✓ Installer descargado\n";

        // Ejecutar con COMPOSER_HOME definido
        $env = "HOME=" . escapeshellarg($composerHome) . " COMPOSER_HOME=" . escapeshellarg($composerHome);
        $out = shell_exec("$env $php " . escapeshellarg($installerFile) . " --install-dir=" . escapeshellarg($basePath) . " --filename=composer.phar 2>&1");
        echo htmlspecialchars($out);
        @unlink($installerFile);

        if (file_exists($basePath . '/composer.phar')) {
            echo "\n✓ composer.phar listo!\n";
        } else {
            echo "\n✗ No se creó composer.phar\n";
        }
        break;

    // ── INSTALAR PAQUETES ──────────────────────────────────────────────────
    case 'require':
        $php  = findPhp();
        $c    = findComposer($basePath, $composerPhar);
        $composerHome = $basePath . '/.composer_home';
        if (!is_dir($composerHome)) mkdir($composerHome, 0755, true);

        if (empty($c)) {
            echo "✗ Composer no encontrado. Primero ejecuta action=download_composer\n";
            break;
        }

        $composerCmd = (str_ends_with($c, '.phar')) ? "$php $c" : $c;
        $env = "HOME=" . escapeshellarg($composerHome) . " COMPOSER_HOME=" . escapeshellarg($composerHome);

        echo "▶ Composer: $c\n";
        echo "▶ Instalando paquetes...\n\n";

        $cmd = "cd " . escapeshellarg($basePath) . " && $env $composerCmd require "
             . "barryvdh/laravel-dompdf:^3.1 "
             . "phpoffice/phpspreadsheet:^3.0 "
             . "--no-interaction 2>&1";
        echo htmlspecialchars(shell_exec($cmd) ?: 'Sin salida.');
        break;

    // ── LIMPIAR CACHE ──────────────────────────────────────────────────────
    case 'cache':
        $php = findPhp();
        echo "▶ Limpiando caché Laravel...\n\n";
        $artisan = $basePath . '/artisan';
        $cmd = "cd " . escapeshellarg($basePath) . " && $php $artisan config:clear 2>&1 && $php $artisan cache:clear 2>&1 && $php $artisan view:clear 2>&1 && $php $artisan route:clear 2>&1";
        echo htmlspecialchars(shell_exec($cmd) ?: 'Sin salida.');
        break;
}

echo "\n</pre><hr>";
echo '<p style="font-family:sans-serif;font-size:14px;">Pasos en orden:</p><ol style="font-family:sans-serif;font-size:14px;">';
$b = '?key=rehavite2024deploy&action=';
echo "<li><a href='{$b}check'>Verificar estado</a></li>";
echo "<li><a href='{$b}download_composer'>Descargar composer.phar (si no está)</a></li>";
echo "<li><a href='{$b}require'>Instalar paquetes</a></li>";
echo "<li><a href='{$b}cache'>Limpiar caché Laravel</a></li>";
echo '</ol><p style="color:red;font-family:sans-serif;"><strong>⚠ Elimina este archivo al terminar.</strong></p>';
