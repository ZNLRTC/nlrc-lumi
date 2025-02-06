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
        Schema::create('quiz_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id')->nullable();
            $table->string('title',255);
            $table->string('title-translation',255);
            $table->string('description',1000)->nullable();
            $table->string('description-translation',1000)->nullable();
            $table->integer('version_number')->default(1);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('current_title',255);
            $table->unsignedBigInteger('version_id');
            $table->foreign('version_id')->references('id')->on('quiz_versions')->onDelete('cascade');
            $table->unsignedBigInteger('topic_id')->nullable();
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
            $table->boolean('archive')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        Schema::table('quiz_versions', function (Blueprint $table) {
            $table->foreign('quiz_id')->references('id')->on('quizzes');
        });

        Schema::create('quiz_questionnaire_versions', function (Blueprint $table) {
            $table->id();
            $table->string('question',5000);
            $table->unsignedBigInteger('quiz_questionnaire_id')->nullable();
            $table->string('question_type');
            $table->string('explanation',5000)->nullable();
            $table->integer('version_number')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
        
        Schema::create('quiz_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->integer('sort_number');
            $table->foreignId('quiz_version_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('version_id');
            $table->foreign('version_id')->references('id')->on('quiz_questionnaire_versions')->onDelete('cascade');
            $table->boolean('archive')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('quiz_questionnaire_versions', function (Blueprint $table) {
            $table->foreign('quiz_questionnaire_id')->references('id')->on('quiz_questionnaires')->onDelete('cascade');
        });

        Schema::create('quiz_choice_option_versions', function (Blueprint $table) {
            $table->id();
            $table->string('option', 1000);
            $table->unsignedBigInteger('quiz_choice_option_id')->nullable();
            $table->integer("version_number")->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
        
        Schema::create('quiz_choice_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_questionnaire_version_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('version_id');
            $table->foreign('version_id')->references('id')->on('quiz_choice_option_versions')->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('quiz_choice_option_versions', function (Blueprint $table) {
            $table->foreign('quiz_choice_option_id')->references('id')->on('quiz_choice_options')->onDelete('cascade');
        });

        Schema::create('quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_questionnaire_version_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_choice_option_version_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trainee_id');
            $table->foreign('trainee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('quiz_version_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->onDelete('cascade');;
            $table->foreignId('quiz_questionnaire_version_id')->constrained()->onDelete('cascade');
            $table->string('answer', 1000);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_versions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('quiz_questionnaire_versions');
        Schema::dropIfExists('quiz_questionnaires');
        Schema::dropIfExists('quiz_choice_option_versions');
        Schema::dropIfExists('quiz_choice_options');
        Schema::dropIfExists('quiz_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_attempt_answers');
    }
};
