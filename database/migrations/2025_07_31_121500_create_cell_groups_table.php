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
        Schema::create('cell_groups', function (Blueprint $table) {
            $table->id();
            $table->string('cell_group_id')->unique(); // Custom cell group identifier
            $table->foreignId('leader_id')->constrained('leaders')->onDelete('cascade'); // Cell leader reference
            $table->enum('cell_group_type', ['Cell Group', 'Open Cell', 'G12 Cell'])->default('Cell Group');
            $table->string('meeting_day'); // Day of the week for meetings
            $table->time('meeting_time'); // Time of the meeting
            $table->string('meeting_location'); // Location where meetings are held
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_groups');
    }
};
