<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CLEANING UP DATABASE ===" . PHP_EOL;

// Delete all existing test data
DB::table('cell_members')->delete();
DB::table('leaders')->delete();

echo "✓ Cleared all leaders and cell members" . PHP_EOL;

echo PHP_EOL . "=== CREATING FRESH HIERARCHY ===" . PHP_EOL;

// Create Oriel Ballano (Top Level Leader)
$orielId = DB::table('leaders')->insertGetId([
    'name' => 'Oriel Ballano',
    'position' => 'Regional Director',
    'department' => 'Operations',
    'email' => 'oriel.ballano@example.com',
    'phone' => '+1234567890',
    'bio' => 'Regional Director with 10+ years experience',
    'status' => true,
    'parent_leader_id' => null,
    'level' => 0,
    'path' => '/',
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Created Oriel Ballano (ID: $orielId)" . PHP_EOL;

// Create Michael Roque (Under Oriel)
$michaelId = DB::table('leaders')->insertGetId([
    'name' => 'Michael Roque',
    'position' => 'Area Manager',
    'department' => 'Operations',
    'email' => 'michael.roque@example.com',
    'phone' => '+1234567891',
    'bio' => 'Area Manager under Oriel Ballano',
    'status' => true,
    'parent_leader_id' => $orielId,
    'level' => 1,
    'path' => "/$orielId/",
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Created Michael Roque (ID: $michaelId) under Oriel" . PHP_EOL;

// Create Alice Santos (direct under Oriel)
DB::table('cell_members')->insert([
    'name' => 'Alice Santos',
    'age' => 25,
    'grade' => 'Growing',
    'status' => true,
    'leader_id' => $orielId,
    'notes' => 'Direct cell member under Oriel Ballano',
    'enrollment_date' => now()->subMonths(6),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Created Alice Santos under Oriel" . PHP_EOL;

// Create Bob Legazpi (under Michael)
DB::table('cell_members')->insert([
    'name' => 'Bob Legazpi',
    'age' => 28,
    'grade' => 'Newcomer',
    'status' => true,
    'leader_id' => $michaelId,
    'notes' => 'Cell member under Michael Roque',
    'enrollment_date' => now()->subMonths(3),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Created Bob Legazpi under Michael" . PHP_EOL;

// Create Charlie Cruz (under Michael)
DB::table('cell_members')->insert([
    'name' => 'Charlie Cruz',
    'age' => 30,
    'grade' => 'Mature',
    'status' => true,
    'leader_id' => $michaelId,
    'notes' => 'Cell member under Michael Roque',
    'enrollment_date' => now()->subMonths(8),
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "✓ Created Charlie Cruz under Michael" . PHP_EOL;

echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
$leadersCount = DB::table('leaders')->count();
$cellMembersCount = DB::table('cell_members')->count();
echo "Total Leaders: $leadersCount" . PHP_EOL;
echo "Total Cell Members: $cellMembersCount" . PHP_EOL;

echo PHP_EOL . "=== HIERARCHY STRUCTURE ===" . PHP_EOL;
echo "Oriel Ballano (Regional Director)" . PHP_EOL;
echo "├── Alice Santos (Cell Member)" . PHP_EOL;
echo "└── Michael Roque (Area Manager)" . PHP_EOL;
echo "    ├── Bob Legazpi (Cell Member)" . PHP_EOL;
echo "    └── Charlie Cruz (Cell Member)" . PHP_EOL;

echo PHP_EOL . "✅ SUCCESS! Clean hierarchy created successfully!" . PHP_EOL;
echo "You can now test the hierarchy view in the admin panel." . PHP_EOL;
