<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Leader;
use App\Models\CellMember;

class SetupHierarchy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:hierarchy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up example hierarchy with Oriel, Michael, and team members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up hierarchy example...');
        
        try {
            // Create or update Oriel Ballano (Top Level)
            $oriel = Leader::updateOrCreate(
                ['email' => 'oriel.ballano@example.com'],
                [
                    'name' => 'Oriel Ballano',
                    'position' => 'Regional Director',
                    'network' => 'mens',
                    'phone' => '+1234567890',
                    'age' => 39,
                    'birthday' => '1985-03-15',
                    'civil_status' => 'married',
                    'status' => true,
                    'parent_leader_id' => null,
                    'level' => 0,
                    'path' => '/'
                ]
            );
            $this->info("✓ Created/Updated Oriel Ballano (Level {$oriel->level})");

            // Create or update Michael Roque (Under Oriel)
            $michael = Leader::updateOrCreate(
                ['email' => 'michael.roque@example.com'],
                [
                    'name' => 'Michael Roque',
                    'position' => 'Area Manager',
                    'network' => 'mens',
                    'phone' => '+1234567891',
                    'age' => 34,
                    'birthday' => '1990-07-22',
                    'civil_status' => 'single',
                    'status' => true,
                    'parent_leader_id' => $oriel->id,
                    'level' => 1,
                    'path' => "/{$oriel->id}/"
                ]
            );
            $this->info("✓ Created/Updated Michael Roque (Level {$michael->level}) under Oriel");

            // Create cell members under Oriel
            $aliceOriel = CellMember::updateOrCreate(
                ['name' => 'Alice Santos', 'leader_id' => $oriel->id],
                [
                    'age' => 25,
                    'birthday' => '1999-11-10',
                    'grade' => 'Growing',
                    'network' => 'womens',
                    'civil_status' => 'single',
                    'status' => true,
                    'notes' => 'Direct cell member under Oriel Ballano',
                    'enrollment_date' => now()->subMonths(6)
                ]
            );
            $this->info("✓ Created/Updated Alice Santos as direct cell member under Oriel");

            // Create cell members under Michael
            $bobMichael = CellMember::updateOrCreate(
                ['name' => 'Bob Legazpi', 'leader_id' => $michael->id],
                [
                    'age' => 28,
                    'birthday' => '1996-05-18',
                    'grade' => 'Newcomer',
                    'network' => 'mens',
                    'civil_status' => 'married',
                    'status' => true,
                    'notes' => 'Cell member under Michael Roque',
                    'enrollment_date' => now()->subMonths(3)
                ]
            );
            $this->info("✓ Created/Updated Bob Legazpi as cell member under Michael");

            $charlieMichael = CellMember::updateOrCreate(
                ['name' => 'Charlie Cruz', 'leader_id' => $michael->id],
                [
                    'age' => 30,
                    'birthday' => '1994-12-03',
                    'grade' => 'Mature',
                    'network' => 'womens',
                    'civil_status' => 'widow',
                    'status' => true,
                    'notes' => 'Cell member under Michael Roque',
                    'enrollment_date' => now()->subMonths(8)
                ]
            );
            $this->info("✓ Created/Updated Charlie Cruz as cell member under Michael");

            $this->newLine();
            $this->info('=== HIERARCHY SUMMARY ===');
            $this->line("Oriel Ballano (Regional Director) - Level {$oriel->level}");
            $this->line("├── Alice Santos (Cell Member)");
            $this->line("└── Michael Roque (Area Manager) - Level {$michael->level}");
            $this->line("    ├── Bob Legazpi (Cell Member)");
            $this->line("    └── Charlie Cruz (Cell Member)");

            $this->newLine();
            $this->info('=== VERIFICATION ===');
            $this->line("Oriel's direct cell members: " . $oriel->cellMembers()->count());
            $this->line("Oriel's direct leaders: " . $oriel->childLeaders()->count());
            $this->line("Michael's cell members: " . $michael->cellMembers()->count());
            $this->line("Total network under Oriel: " . $oriel->getNetworkSize());

            $this->newLine();
            $this->info('✅ Hierarchy setup completed successfully!');
            $this->info('Now you can test the hierarchy view in the Leaders admin panel.');

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
