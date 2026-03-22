<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

// Sample local paths
echo "=== 10 SAMPLE LOCAL THUMBNAILS ===\n";
$local = DB::table('posts')
    ->whereNotNull('thumbnail')
    ->where('thumbnail', '!=', '')
    ->where('thumbnail', 'not like', 'http%')
    ->limit(10)
    ->pluck('thumbnail');
foreach ($local as $t) echo $t . "\n";

// Sample external URLs
echo "\n=== 5 SAMPLE EXTERNAL THUMBNAILS ===\n";
$ext = DB::table('posts')
    ->where('thumbnail', 'like', 'http%')
    ->limit(5)
    ->pluck('thumbnail');
foreach ($ext as $t) echo $t . "\n";
