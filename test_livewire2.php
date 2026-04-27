<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use Livewire\Livewire;

try {
    echo "Testing PatientDetail component...\n";
    $patient = Patient::with('consultations')->first();
    if (!$patient) die("No patient found.\n");
    
    if ($patient->consultations->count() > 0) {
        $consultaId = $patient->consultations->last()->id;
        echo "Testing with consultation ID: $consultaId\n";
        
        $component = Livewire::test('patient-detail', ['patient' => $patient])
            ->call('selectConsultation', $consultaId);
            
        echo "No exceptions thrown by Livewire backend!\n";
    } else {
        echo "No consultations found.\n";
    }
} catch (\Throwable $e) {
    echo "LIVEWIRE EXCEPTION CATCHED:\n";
    echo get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
