<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->foreignId('cell_group_id')->nullable()->constrained('cell_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropForeign(['cell_group_id']);
            $table->dropColumn('cell_group_id');
        });
    }
};
