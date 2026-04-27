<?php
$public = __DIR__ . '/public';
$storage = $public . '/storage';

echo "Public directory: $public\n";
echo "Storage link exists? " . (file_exists($storage) ? 'YES' : 'NO') . "\n";
if (file_exists($storage)) {
    echo "Is it a directory? " . (is_dir($storage) ? 'YES' : 'NO') . "\n";
    echo "Is it a link? " . (is_link($storage) ? 'YES' : 'NO') . "\n";
}

$target = __DIR__ . '/storage/app/public';
echo "Target directory: $target\n";
echo "Target exists? " . (file_exists($target) ? 'YES' : 'NO') . "\n";
if (file_exists($target)) {
    echo "Contents of target:\n";
    print_r(scandir($target));
}
