<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
print_r(App\Models\Patient::selectRaw('status, count(*) as count')->groupBy('status')->get()->toArray());
