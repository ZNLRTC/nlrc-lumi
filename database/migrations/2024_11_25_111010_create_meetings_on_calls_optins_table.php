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
        Schema::create('meetings_on_calls_opt_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meetings_on_call_id');
            $table->foreign('meetings_on_call_id')
                ->references('id')
                ->on('meetings_on_calls');
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->boolean('is_opt_in')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings_on_calls_opt_ins');
    }
};
