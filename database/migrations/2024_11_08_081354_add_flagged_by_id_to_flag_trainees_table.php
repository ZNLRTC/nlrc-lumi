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
        Schema::table('flag_trainees', function (Blueprint $table) {
            $table->unsignedBigInteger('flagged_by_id')
                ->nullable()
                ->after('flag_id');
            $table->foreign('flagged_by_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flag_trainees', function (Blueprint $table) {
            $table->dropForeign(['flagged_by_id']);
            $table->dropColumn('flagged_by_id');
        });
    }
};
