<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CellMember;
use Illuminate\Support\Facades\DB;

class MigrateStudentsToCellMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:students-to-cell-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all student data to cell members table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting migration from students to cell members...');
        
        // Check if students table exists
        if (!DB::getSchemaBuilder()->hasTable('students')) {
            $this->error('Students table does not exist. Migration cannot proceed.');
            return;
        }
        
        $students = DB::table('students')->get();
        $count = $students->count();
        
        if ($count === 0) {
            $this->info('No students found to migrate.');
            return;
        }
        
        $this->info("Found {$count} students to migrate.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($students as $student) {
            CellMember::create([
                'name' => $student->name,
                'age' => $student->age,
                'grade' => $student->grade,
                'status' => $student->status,
                'leader_id' => $student->leader_id,
                'notes' => $student->notes,
                'enrollment_date' => $student->enrollment_date,
                'created_at' => $student->created_at,
                'updated_at' => $student->updated_at,
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully migrated {$count} students to cell members!");
        
        // Ask if user wants to verify the migration
        if ($this->confirm('Would you like to verify the migration by comparing counts?')) {
            $cellMembersCount = CellMember::count();
            $this->info("Students count: {$count}");
            $this->info("Cell Members count: {$cellMembersCount}");
            
            if ($count === $cellMembersCount) {
                $this->info('✅ Migration verified successfully!');
            } else {
                $this->error('❌ Migration verification failed! Counts do not match.');
            }
        }
    }
}
