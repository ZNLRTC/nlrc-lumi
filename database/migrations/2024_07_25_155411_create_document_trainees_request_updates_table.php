<?php

use App\Enums\DocumentTraineesRequestUpdatesApprovalStatus;
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
        Schema::create('document_trainees_request_updates', function (Blueprint $table) {
            // The staff_user_id column is the user id of the staff who approved the document for removal by trainee
            $table->id();
            $table->foreignId('document_trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('staff_user_id')
                ->nullable();
            $table->string('reason', 255);
            $table->string('approval_status', 32)
                ->nullable()
                ->default(DocumentTraineesRequestUpdatesApprovalStatus::PENDING_APPROVAL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_trainees_request_updates');
    }
};
