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
        Schema::table('equippings', function (Blueprint $table) {
            // Change have_cell_group from enum to boolean
            $table->boolean('have_cell_group')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equippings', function (Blueprint $table) {
            // Revert back to enum if needed
            $table->enum('have_cell_group', ['Active', 'Inactive'])->default('Inactive')->change();
        });
    }
};
