<?php
$lines = file('storage/logs/laravel.log');
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'local.ERROR: syntax error,') !== false) {
        $data = json_decode(substr($lines[$i], strpos($lines[$i], '{')), true);
        echo "View error in: " . ($data['view']['view'] ?? 'unknown') . "\n";
        break;
    }
}
