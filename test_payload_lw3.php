<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use Livewire\Livewire;

$patient = Patient::with('consultations')->first();
$consultaId = $patient->consultations->last()->id;

$component = Livewire::test('patient-detail', ['patient' => $patient])
    ->call('selectConsultation', $consultaId);

$payload = $component->lastResponse; // wait, testable response in LW3?
// In LW3, it's `$component->effects` and `$component->snapshot`.
$snapshot = $component->snapshot;
$effects = $component->effects;

echo "Snapshot:\n";
echo substr(json_encode($snapshot), 0, 1000) . "\n\n";

echo "Effects (HTML):\n";
echo substr(json_encode($effects), 0, 1000) . "\n\n";
