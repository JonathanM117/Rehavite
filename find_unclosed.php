<?php
$lines = file('resources/views/livewire/patient-detail.blade.php');
$stack = [];
$forelse_stack = [];
foreach ($lines as $i => $line) {
    if (preg_match('/@if\s*\(/', $line)) {
        $stack[] = $i + 1;
    }
    if (preg_match('/@endif/', $line)) {
        array_pop($stack);
    }
    if (preg_match('/@forelse\s*\(/', $line)) {
        $forelse_stack[] = $i + 1;
    }
    if (preg_match('/@endforelse/', $line)) {
        array_pop($forelse_stack);
    }
}
echo "Unclosed @if on lines: " . implode(', ', $stack) . "\n";
echo "Unclosed @forelse on lines: " . implode(', ', $forelse_stack) . "\n";
