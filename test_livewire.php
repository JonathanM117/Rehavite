<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use Livewire\Livewire;

try {
    echo "Mounting PatientDetail component...\n";
    $patient = Patient::first();
    if (!$patient) {
        die("No patient found.\n");
    }
    
    // Simulate mounting
    $component = app('livewire')->mount('patient-detail', ['patient' => $patient]);
    echo "Mounted successfully.\n";
    
    // Simulate setting consultation
    if ($patient->consultations->count() > 0) {
        $consultaId = $patient->consultations->first()->id;
        echo "Selecting consultation $consultaId...\n";
        
        $component->instance->selectConsultation($consultaId);
        
        echo "Dehydrating component...\n";
        // Attempt to render which triggers dehydration
        $html = $component->instance->render()->render();
        echo "Rendered HTML successfully. Size: " . strlen($html) . " bytes\n";
    } else {
        echo "No consultations found to select.\n";
    }

} catch (\Throwable $e) {
    echo "EXCEPTION THROWN:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
