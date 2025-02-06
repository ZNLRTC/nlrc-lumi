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
        Schema::table('trainees', function (Blueprint $table) {
            $table->string('occupation', 64) // eg. Nurse
                ->nullable()
                ->after('phone_number');
            $table->string('field_of_work', 64) // eg. Healthcare Services
                ->nullable()
                ->after('occupation');
            $table->tinyInteger('work_experience')
                ->nullable()
                ->after('field_of_work');
            $table->tinyInteger('marital_status')
                ->nullable()
                ->after('work_experience');
            $table->tinyInteger('education')
                ->nullable()
                ->after('marital_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trainees', function (Blueprint $table) {
            $table->dropColumn([
                'occupation',
                'field_of_work',
                'work_experience',
                'marital_status',
                'education'
            ]);
        });
    }
};
