<?php

/**
 * Laravel - public/index.php modificado para Hostgator
 * Los archivos de la app están en /home3/rehavite/rehavite_app/
 * Este archivo vive en /home3/rehavite/public_html/
 */

define('LARAVEL_START', microtime(true));

// Autoloader de Composer
require __DIR__.'/../rehavite_app/vendor/autoload.php';

// Bootstrap de la aplicación Laravel
$app = require_once __DIR__.'/../rehavite_app/bootstrap/app.php';

// Correr la aplicación
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
