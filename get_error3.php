<?php
$lines = file('storage/logs/laravel.log');
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'local.ERROR: syntax error,') !== false) {
        if (preg_match('/"view":"([^"]+)"/', $lines[$i], $matches)) {
            echo "View error in: " . stripslashes($matches[1]) . "\n";
            echo "Full line: " . substr($lines[$i], 0, 500) . "...\n";
            break;
        }
    }
}
