<?php

use Doctrine\DBAL\Schema\Index;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_name')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('internal_notes')->nullable();
            
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('internal_name')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('internal_description')->nullable();
            $table->unsignedInteger('sort'); // Position of the unit within the course
            $table->timestamps();
        });

        Schema::create('course_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('group_id')->constrained();
            $table->timestamps();
        });

        Schema::create('meeting_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('internal_notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('meeting_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('internal_notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained();
            $table->foreignId('meeting_type_id')->constrained();
            $table->string('description', length: 1000);
            $table->string('internal_notes', length: 1000)->nullable();
            $table->timestamps();
        });

        Schema::create('meeting_trainee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('meeting_status_id')->constrained();
            $table->unsignedBigInteger('instructor_id')
                ->nullable();
            $table->foreign('instructor_id')
                ->references('id')
                ->on('users');
            $table->string('internal_notes', length: 400)->nullable();
            $table->string('feedback', length: 400)->nullable();
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_trainee');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('meeting_types');
        Schema::dropIfExists('meeting_statuses');
        Schema::dropIfExists('course_group');
        Schema::dropIfExists('units');
        Schema::dropIfExists('courses');
    }
};
