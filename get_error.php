<?php
$lines = file('storage/logs/laravel.log');
for ($i = count($lines) - 1; $i >= 0; $i--) {
    if (strpos($lines[$i], 'local.ERROR') !== false) {
        $start = $i;
        for ($j = 0; $j < 10; $j++) {
            if (isset($lines[$start+$j])) {
                echo $lines[$start+$j];
            }
        }
        echo "\n";
        break;
    }
}
