<?php
$lines = file('storage/logs/laravel.log');
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'local.ERROR: syntax error,') !== false) {
        $start = $i;
        for ($j=0; $j<500; $j++) {
            if (isset($lines[$start+$j]) && strpos($lines[$start+$j], '.blade.php') !== false) {
                echo "FOUND: " . trim($lines[$start+$j]) . "\n";
            }
        }
        break;
    }
}
