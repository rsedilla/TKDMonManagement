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
        Schema::create('cell_member_equipping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cell_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipping_id')->constrained()->onDelete('cascade');
            $table->date('completed_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['cell_member_id', 'equipping_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cell_member_equipping');
    }
};
