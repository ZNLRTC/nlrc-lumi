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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('internal_name');
            $table->string('description', 1000);
            $table->string('internal_notes', 1000)
                ->nullable();
            $table->string('submission_type')
                ->default('text');
            $table->string('attachment_type')
                ->nullable();
            $table->string('slug')
                ->unique();
            $table->timestamps();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('instructor_id')
                ->nullable();
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users');
            $table->string('submission', 500);
            $table->string('attachment_url', 2048)
                ->nullable();
            $table->string('feedback', 500)
                ->nullable();
            $table->string('submission_status')
                ->default('not checked');
            $table->timestamp('checked_at')
                ->nullable();
            $table->timestamp('edited_at')
                ->nullable();
            $table->timestamp('submitted_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
