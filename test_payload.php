<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use Livewire\Livewire;

try {
    $patient = Patient::with('consultations')->first();
    $consultaId = $patient->consultations->last()->id;
    
    $component = Livewire::test('patient-detail', ['patient' => $patient])
        ->call('selectConsultation', $consultaId);
        
    $payload = $component->lastResponse;
    echo "Payload size: " . strlen(json_encode($payload)) . " bytes\n";
    
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
