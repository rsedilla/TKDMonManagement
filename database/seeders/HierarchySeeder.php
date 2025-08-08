<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HierarchySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing test data
        DB::table('cell_members')->whereIn('name', ['Alice Santos', 'Bob Legazpi', 'Charlie Cruz'])->delete();
        DB::table('leaders')->whereIn('email', ['oriel.ballano@example.com', 'michael.roque@example.com'])->delete();

        // Insert Oriel Ballano (Top Level Leader)
        $orielId = DB::table('leaders')->insertGetId([
            'name' => 'Oriel Ballano',
            'position' => 'Regional Director',
            'department' => 'Operations',
            'email' => 'oriel.ballano@example.com',
            'phone' => '+1234567890',
            'bio' => 'Regional Director with 10+ years of experience',
            'status' => true,
            'parent_leader_id' => null,
            'level' => 0,
            'path' => '/',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Michael Roque (Under Oriel)
        $michaelId = DB::table('leaders')->insertGetId([
            'name' => 'Michael Roque',
            'position' => 'Area Manager',
            'department' => 'Operations',
            'email' => 'michael.roque@example.com',
            'phone' => '+1234567891',
            'bio' => 'Area Manager reporting to Oriel Ballano',
            'status' => true,
            'parent_leader_id' => $orielId,
            'level' => 1,
            'path' => "/$orielId/",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Alice Santos (under Oriel)
        DB::table('cell_members')->insert([
            'name' => 'Alice Santos',
            'age' => 25,
            'status' => true,
            'leader_id' => $orielId,
            'notes' => 'Direct cell member under Oriel Ballano',
            'enrollment_date' => now()->subMonths(6),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Bob Legazpi (under Michael)
        DB::table('cell_members')->insert([
            'name' => 'Bob Legazpi',
            'age' => 28,
            'status' => true,
            'leader_id' => $michaelId,
            'notes' => 'Cell member under Michael Roque',
            'enrollment_date' => now()->subMonths(3),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert Charlie Cruz (under Michael)
        DB::table('cell_members')->insert([
            'name' => 'Charlie Cruz',
            'age' => 30,
            'status' => true,
            'leader_id' => $michaelId,
            'notes' => 'Cell member under Michael Roque',
            'enrollment_date' => now()->subMonths(8),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('✅ Hierarchy test data created successfully!');
        $this->command->line('Oriel Ballano (Regional Director)');
        $this->command->line('├── Alice Santos (Cell Member)');
        $this->command->line('└── Michael Roque (Area Manager)');
        $this->command->line('    ├── Bob Legazpi (Cell Member)');
        $this->command->line('    └── Charlie Cruz (Cell Member)');
    }
}
