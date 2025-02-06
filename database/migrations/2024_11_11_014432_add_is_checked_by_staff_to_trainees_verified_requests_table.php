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
        Schema::table('trainees_verified_requests', function (Blueprint $table) {
            $table->boolean('is_checked_by_staff')
                ->default(false)
                ->after('staff_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees_verified_requests', function (Blueprint $table) {
            $table->dropColumn('is_checked_by_staff');
        });
    }
};
