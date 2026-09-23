<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$records = App\Models\MedicalRecord::all();
foreach ($records as $r) {
    echo "ID: " . $r->id . "\n";
    echo "Patient ID: " . $r->patient_id . "\n";
    echo "Pediatric History JSON: " . json_encode($r->pediatric_history) . "\n\n";
}
