<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;

echo "Cell Members columns: " . implode(', ', Schema::getColumnListing('cell_members')) . PHP_EOL;
echo "Leaders columns: " . implode(', ', Schema::getColumnListing('leaders')) . PHP_EOL;

// Test if deleted_at exists
if (Schema::hasColumn('cell_members', 'deleted_at')) {
    echo "✓ cell_members.deleted_at EXISTS" . PHP_EOL;
} else {
    echo "✗ cell_members.deleted_at MISSING" . PHP_EOL;
}

if (Schema::hasColumn('leaders', 'deleted_at')) {
    echo "✓ leaders.deleted_at EXISTS" . PHP_EOL;
} else {
    echo "✗ leaders.deleted_at MISSING" . PHP_EOL;
}
