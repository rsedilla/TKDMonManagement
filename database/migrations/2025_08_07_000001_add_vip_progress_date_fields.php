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
            // SUYNL Lesson dates (10 lessons)
            for ($i = 1; $i <= 10; $i++) {
                $table->date("suynl_lesson_{$i}_date")->nullable();
            }
            
            // Sunday Service dates (4 services)
            for ($i = 1; $i <= 4; $i++) {
                $table->date("sunday_service_{$i}_date")->nullable();
            }
            
            // Cell Group dates (4 sessions)
            for ($i = 1; $i <= 4; $i++) {
                $table->date("cell_group_{$i}_date")->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consolidations', function (Blueprint $table) {
            // Drop SUYNL Lesson date columns
            for ($i = 1; $i <= 10; $i++) {
                $table->dropColumn("suynl_lesson_{$i}_date");
            }
            
            // Drop Sunday Service date columns
            for ($i = 1; $i <= 4; $i++) {
                $table->dropColumn("sunday_service_{$i}_date");
            }
            
            // Drop Cell Group date columns
            for ($i = 1; $i <= 4; $i++) {
                $table->dropColumn("cell_group_{$i}_date");
            }
        });
    }
};
