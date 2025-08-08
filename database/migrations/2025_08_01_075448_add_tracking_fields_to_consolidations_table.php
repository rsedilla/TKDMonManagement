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
        Schema::table('consolidations', function (Blueprint $table) {
            // SUYNL Lessons (1-10) - JSON array to store completed lessons
            $table->json('suynl_lessons_completed')->nullable()->comment('Array of completed SUYNL lesson numbers (1-10)');
            
            // Sunday Services (1-4) - JSON array to store attended services
            $table->json('sunday_services_attended')->nullable()->comment('Array of attended Sunday service numbers (1-4)');
            
            // Cell Group Attendance (1-4) - JSON array to store attended sessions
            $table->json('cell_group_attended')->nullable()->comment('Array of attended cell group session numbers (1-4)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consolidations', function (Blueprint $table) {
            $table->dropColumn(['suynl_lessons_completed', 'sunday_services_attended', 'cell_group_attended']);
        });
    }
};
