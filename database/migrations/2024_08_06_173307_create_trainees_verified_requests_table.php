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
        Schema::create('trainees_verified_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('staff_user_id')
                ->nullable();
            $table->boolean('is_verified')
                ->default(false);
            $table->timestamp('requested_at')
                ->nullable()
                ->useCurrent();
            $table->timestamp('verified_at')
                ->nullable()
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainees_verified_requests');
    }
};
