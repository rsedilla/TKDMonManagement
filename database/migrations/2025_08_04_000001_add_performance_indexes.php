<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations for production optimization.
     */
    public function up(): void
    {
        // Add indexes for better performance - check if they exist first
        $this->createIndexIfNotExists('leaders', 'leaders_name_index', ['name']);
        $this->createIndexIfNotExists('leaders', 'leaders_created_at_index', ['created_at']);
        $this->createIndexIfNotExists('leaders', 'leaders_parent_leader_id_index', ['parent_leader_id']);
        $this->createIndexIfNotExists('leaders', 'leaders_level_index', ['level']);

        $this->createIndexIfNotExists('cell_members', 'cell_members_name_index', ['name']);
        $this->createIndexIfNotExists('cell_members', 'cell_members_leader_id_index', ['leader_id']);
        $this->createIndexIfNotExists('cell_members', 'cell_members_created_at_index', ['created_at']);
        $this->createIndexIfNotExists('cell_members', 'cell_members_enrollment_date_index', ['enrollment_date']);

        $this->createIndexIfNotExists('consolidations', 'consolidations_vip_name_index', ['vip_name']);
        $this->createIndexIfNotExists('consolidations', 'consolidations_consolidator_type_consolidator_id_index', ['consolidator_type', 'consolidator_id']);
        $this->createIndexIfNotExists('consolidations', 'consolidations_consolidation_date_index', ['consolidation_date']);
        $this->createIndexIfNotExists('consolidations', 'consolidations_vip_status_index', ['vip_status']);
        $this->createIndexIfNotExists('consolidations', 'consolidations_created_at_index', ['created_at']);

        $this->createIndexIfNotExists('equippings', 'equippings_equippable_type_equippable_id_index', ['equippable_type', 'equippable_id']);
        $this->createIndexIfNotExists('equippings', 'equippings_created_at_index', ['created_at']);

        $this->createIndexIfNotExists('cell_groups', 'cell_groups_cell_group_id_index', ['cell_group_id']);
        $this->createIndexIfNotExists('cell_groups', 'cell_groups_leader_id_index', ['leader_id']);
        $this->createIndexIfNotExists('cell_groups', 'cell_groups_created_at_index', ['created_at']);
    }

    /**
     * Helper method to create index if it doesn't exist
     */
    private function createIndexIfNotExists(string $table, string $indexName, array $columns): void
    {
        // Check if index exists
        $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))
            ->pluck('Key_name')
            ->toArray();

        if (!in_array($indexName, $indexes)) {
            $columnList = implode(',', array_map(fn($col) => "`{$col}`", $columns));
            DB::statement("ALTER TABLE `{$table}` ADD INDEX `{$indexName}` ({$columnList})");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['sol_training_level']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('cell_members', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['leader_id']);
            $table->dropIndex(['sol_training_level']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('consolidations', function (Blueprint $table) {
            $table->dropIndex(['vip_name']);
            $table->dropIndex(['consolidator_type', 'consolidator_id']);
            $table->dropIndex(['consolidation_date']);
            $table->dropIndex(['vip_status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('equippings', function (Blueprint $table) {
            $table->dropIndex(['student_name']);
            $table->dropIndex(['leader_id']);
            $table->dropIndex(['equipping_level']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('cell_groups', function (Blueprint $table) {
            $table->dropIndex(['cell_group_name']);
            $table->dropIndex(['leader_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
