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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->unique();
            $table->string('description')
                ->nullable();
            $table->boolean('active')
                ->default(true);
            $table->timestamps();
        });

        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)
                ->unique();
            $table->string('nationality', 128)
                ->unique();
            $table->string('code', 4)
                ->unique();
        });

        // Types of groups, e.g. SUO, SUOM, FIN, etc.
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 16)
                ->unique();
            $table->string('description');
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name', 64);
            $table->timestamp('date_of_start')
                ->nullable();
            $table->string('notes')
                ->nullable();
            $table->boolean('active')
                ->default(true);
            $table->timestamps();
        });

        Schema::create('flag_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->unique();
            $table->string('notes')
                ->nullable();
            $table->timestamps();
        });

        Schema::create('flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flag_type_id');
            $table->foreign('flag_type_id')
                ->references('id')
                ->on('flag_types');
            $table->string('name')
                ->unique();
            $table->boolean('visible_to_trainee')
                ->default(false);
            $table->string('description');
            $table->timestamps();

            $table->index('flag_type_id');
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->unique();
            $table->string('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->after('id')
                ->default(4)
                ->required()
                ->constrained()
                ->cascadeOnDelete();
            $table->boolean('restricted')
                ->default(false)
                ->after('password');
            $table->text('notes')
                ->nullable()
                ->after('restricted');
        });

        Schema::create('trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('agency_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->string('first_name')
                ->nullable();
            $table->string('middle_name')
                ->nullable();
            $table->string('last_name')
                ->nullable();
            $table->date('date_of_birth')
                ->nullable();
            $table->string('sex', 32)
                ->nullable();
            $table->unsignedBigInteger('country_of_residence_id')
                ->nullable();
            $table->unsignedBigInteger('country_of_citizenship_id')
                ->nullable();
            $table->foreign('country_of_residence_id')
                ->references('id')
                ->on('countries');
            $table->foreign('country_of_citizenship_id')
                ->references('id')
                ->on('countries');
            $table->boolean('active')
                ->default(true);
            $table->date('date_of_training_start')
                ->nullable();
            $table->string('address')
                ->nullable();
            $table->string('phone_number', 32)
                ->nullable();
            $table->string('other_email')
                ->nullable();
            $table->timestamps();

            $table->index('first_name');
            $table->index('last_name');
        });

        Schema::create('flag_trainees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('flag_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('active')
                ->default(true);
            $table->string('description')
                ->nullable();
            $table->string('internal_notes')
                ->nullable();
            $table->timestamps();

            $table->index('active');
        });

        Schema::create('group_trainee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainee_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('group_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedBigInteger('added_by')
                ->nullable();
            $table->foreign('added_by')->references('id')->on('users');
            $table->string('notes')
                ->nullable();
            $table->boolean('active')
                ->default(true);
            $table->timestamps();

            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flag_trainees');
        Schema::dropIfExists('flags');
        Schema::dropIfExists('flag_types');
        Schema::dropIfExists('group_trainee');
        Schema::dropIfExists('trainees');
        Schema::dropIfExists('countries');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('types');
        Schema::dropIfExists('agencies');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropColumn('restricted');
            $table->dropColumn('notes');
        });
        Schema::dropIfExists('roles');
    }
};
