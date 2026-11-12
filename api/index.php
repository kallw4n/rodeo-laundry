<?php

// Vercel's Linux libpq supports SNI natively; clear PGOPTIONS to avoid pooler/endpoint mismatch
putenv('PGOPTIONS');
unset($_ENV['PGOPTIONS'], $_SERVER['PGOPTIONS']);

// Ensure writable directories exist in Vercel's /tmp
$tmpDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';

