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
        Schema::create('consolidations', function (Blueprint $table) {
            $table->id();
            $table->string('vip_name');
            $table->text('vip_contact_details');
            $table->text('vip_address');
            $table->morphs('consolidator'); // This creates consolidator_id and consolidator_type
            $table->date('consolidation_date');
            $table->enum('consolidation_place', ['services', 'cell_group', 'ove']);
            $table->enum('vip_status', ['other_church', 'new_christian', 'recommitment']);
            $table->timestamps();

            // Add indexes for better performance
            $table->index('consolidation_date');
            $table->index('vip_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consolidations');
    }
};
