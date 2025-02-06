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
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('submission_type')->default('text')->change();
            $table->string('attachment_type')->nullable()->change();
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->string('submission_status')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->enum('submission_type', ['text', 'file', 'none'])->default('text')->change();
            $table->enum('attachment_type', ['audio', 'image', 'pdf'])->nullable()->change();
        });

        Schema::table('assignment_submissions', function (Blueprint $table) {
            $table->enum('submission_status', ['Completed', 'Incomplete', 'Not checked'])->default('Not checked')->change();
        });
    }
};
