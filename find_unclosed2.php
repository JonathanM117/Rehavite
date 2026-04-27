<?php
$lines = file('resources/views/livewire/patient-detail.blade.php');
$blocks = ['@foreach' => '@endforeach', '@error' => '@enderror', '@auth' => '@endauth'];
foreach ($blocks as $start => $end) {
    $stack = [];
    foreach ($lines as $i => $line) {
        if (preg_match('/' . $start . '/', $line)) {
            $stack[] = $i + 1;
        }
        if (preg_match('/' . $end . '/', $line)) {
            array_pop($stack);
        }
    }
    echo "Unclosed $start on lines: " . implode(', ', $stack) . "\n";
}
