<?php
$lines = file('storage/logs/laravel.log');
$tail = array_slice($lines, -60);
echo implode('', $tail);
