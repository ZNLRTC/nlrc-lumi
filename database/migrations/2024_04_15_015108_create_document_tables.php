<?php

use App\Enums\DocumentTraineesStatus;
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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->unique();
            $table->string('internal_name')
                ->nullable();
            $table->string('description')
                ->nullable();
            $table->string('internal_notes')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('agency_document_requireds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agency_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('document_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->boolean('is_required')
                ->default(true);
            $table->timestamps();
        });

        Schema::create('document_trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('url', 2048)
                ->nullable();
            $table->string('status', 32)
                ->nullable()
                ->default(DocumentTraineesStatus::PENDING_CHECKING);
            $table->string('comments')
                ->nullable();
            $table->string('internal_notes')
                ->nullable();
            $table->timestamps();

            $table->index('status'); // Maybe this is worth it?
        });

        Schema::create('document_trainee_override', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->boolean('is_required')
                ->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_document_requireds');
        Schema::dropIfExists('document_trainee_override');
        Schema::dropIfExists('document_trainees');
        Schema::dropIfExists('documents');
    }
};
