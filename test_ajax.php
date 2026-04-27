<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Patient;
use Livewire\Livewire;

$patient = Patient::with('consultations')->first();
$component = Livewire::test('patient-detail', ['patient' => $patient]);

// We need to extract the fingerpint and serverMemo to craft a real POST request
$payload = [
    'fingerprint' => $component->payload['fingerprint'],
    'serverMemo' => $component->payload['serverMemo'],
    'updates' => [
        [
            'type' => 'callMethod',
            'payload' => [
                'id' => 'tab123',
                'method' => 'setTab',
                'params' => ['pagos']
            ]
        ]
    ]
];

$ch = curl_init('http://localhost:8000/livewire/message/patient-detail');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
// We don't have a real CSRF token, but we can bypass verification if we execute this via HTTP but artisan serve might block it.
// Actually, let's just make sure artisan serve is running and we bypass the CSRF checking for this specific request...
// Wait, to bypass CSRF we can use the application directly without cURL.
$request = Illuminate\Http\Request::create('/livewire/message/patient-detail', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
echo "Headers: " . json_encode($response->headers->all()) . "\n";
echo "Content: \n" . substr($response->getContent(), 0, 1000) . "\n...";
