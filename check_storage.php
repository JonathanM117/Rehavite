<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Study;
use Illuminate\Support\Facades\Storage;

$studies = Study::all();
echo "Total studies: " . $studies->count() . "\n";

foreach ($studies as $study) {
    $exists = Storage::disk('public')->exists($study->file_path);
    $url = Storage::url($study->file_path);
    $fullPath = storage_path('app/public/' . $study->file_path);
    
    echo "ID: {$study->id}\n";
    echo "  DB Path: {$study->file_path}\n";
    echo "  URL: {$url}\n";
    echo "  Exists in public disk? " . ($exists ? 'YES' : 'NO') . "\n";
    echo "  Full System Path: {$fullPath}\n";
    echo "  Physical file exists? " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
    echo "-------------------\n";
}
