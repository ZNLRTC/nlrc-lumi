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
        Schema::create('proficiencies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proficiency_id')
                ->constrained('proficiencies')
                ->onDelete('cascade');
            $table->string('name', 255);
            $table->string('type', 255);
            $table->boolean('any_instructor_can_grade')
                ->default(false);
            $table->json('allowed_instructors')->nullable();
            $table->json('exam_locations')->nullable();
            $table->string('exam_paper_url', 2048)->nullable();
            $table->timestamp('date')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_trainee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->onDelete('cascade');
            $table->foreignId('trainee_id')
                ->constrained('trainees')
                ->onDelete('cascade');
            $table->string('trainee_alias', 255)->nullable();
            $table->string('internal_notes', 255)->nullable();
            $table->string('status')->default('pending');
            $table->string('exam_location')->nullable();
            $table->timestamps();
        });

        Schema::create('exam_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('short_name', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->decimal('max_score', 5, 2);
            $table->decimal('min_score', 5, 2)->default(0);
            $table->decimal('passing_score', 5, 2)->nullable();
            $table->boolean('mandatory_to_pass')
                ->default(false)
                ->nullable();
            $table->timestamps();
        });

        Schema::create('exam_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('short_name', 255)->nullable();
            $table->decimal('passing_percentage', 5, 2);
            $table->timestamps();
        });

        Schema::create('exam_section_task', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_task_id')
                ->constrained('exam_tasks')
                ->onDelete('cascade');
            $table->foreignId('exam_section_id')
                ->constrained('exam_sections')
                ->onDelete('cascade');
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->nullable()
                ->constrained('exams')
                ->onDelete('cascade');
            $table->foreignId('trainee_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('instructor_id')
                ->references('id')->on('users');
            $table->string('status')->default('pending'); 
            $table->string('feedback', 500)->nullable();
            $table->string('internal_notes', 500)->nullable();
            $table->timestamp('date');
            $table->timestamp('earliest_next_attempt')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('exam_task_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_task_id')
                ->constrained('exam_tasks')
                ->onDelete('cascade');
            $table->foreignId('trainee_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('instructor_id')
                ->references('id')->on('users');
            $table->foreignId('exam_attempt_id')
                ->constrained('exam_attempts')
                ->onDelete('cascade');
            $table->decimal('score', 6, 2);
            $table->timestamps();
        });

        Schema::create('exam_exam_section', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                ->constrained('exams')
                ->onDelete('cascade');
            $table->foreignId('exam_section_id')
                ->constrained('exam_sections')
                ->onDelete('cascade');
        });

        Schema::create('proficiency_trainee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proficiency_id')
                ->constrained('proficiencies')
                ->onDelete('cascade');
            $table->foreignId('trainee_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('exam_attempt_id')
                ->constrained('exam_attempts')
                ->nullable();
            $table->boolean('is_proficient')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_proficiency');
        Schema::dropIfExists('proficiency_trainee');
        Schema::dropIfExists('exam_exam_section');
        Schema::dropIfExists('exam_task_scores');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_section_task');
        Schema::dropIfExists('exam_sections');
        Schema::dropIfExists('exam_tasks');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('proficiencies');
    }
};