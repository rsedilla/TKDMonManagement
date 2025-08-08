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
        // Drop existing equippings table and pivot tables
        Schema::dropIfExists('cell_member_equipping');
        Schema::dropIfExists('leader_equipping');
        Schema::dropIfExists('equippings');
        
        // Create new equippings table structure
        Schema::create('equippings', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relationship to handle both cell members and leaders
            $table->morphs('equippable'); // Creates equippable_type and equippable_id
            
            // Training attended dropdown
            $table->enum('training_attended', [
                'SUYNL', 
                'LIFECLASS', 
                'ENCOUNTER', 
                'SOL1', 
                'SOL2', 
                'SOL3', 
                'SOL GRADUATE'
            ])->nullable();
            
            // Cell group status
            $table->enum('have_cell_group', ['Active', 'Inactive'])->default('Inactive');
            
            $table->timestamps();
            
            // Ensure one record per person
            $table->unique(['equippable_type', 'equippable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equippings');
        
        // Recreate original structure if needed for rollback
        Schema::create('equippings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        Schema::create('cell_member_equipping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipping_id')->constrained()->onDelete('cascade');
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['cell_member_id', 'equipping_id']);
        });
        
        Schema::create('leader_equipping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leader_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipping_id')->constrained()->onDelete('cascade');
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['leader_id', 'equipping_id']);
        });
    }
};
