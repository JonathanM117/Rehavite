<?php
/**
 * Rehavite Sistema - index.php para subdominio sistema.rehavite.com
 * La app Laravel está en /home3/rehavite/rehavite_app/
 */
define('LARAVEL_START', microtime(true));

require __DIR__.'/../rehavite_app/vendor/autoload.php';

$app = require_once __DIR__.'/../rehavite_app/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
)->send();

$kernel->terminate($request, $response);
