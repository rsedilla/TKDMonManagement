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
            $table->unsignedBigInteger('parent_leader_id')->nullable()->after('status');
            $table->integer('level')->default(0)->after('parent_leader_id');
            $table->string('path')->default('/')->after('level');
            
            $table->foreign('parent_leader_id')->references('id')->on('leaders')->onDelete('set null');
            $table->index(['parent_leader_id', 'level']);
            $table->index('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leaders', function (Blueprint $table) {
            $table->dropForeign(['parent_leader_id']);
            $table->dropIndex(['parent_leader_id', 'level']);
            $table->dropIndex(['path']);
            $table->dropColumn(['parent_leader_id', 'level', 'path']);
        });
    }
};
