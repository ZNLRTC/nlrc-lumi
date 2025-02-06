<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planner_weeks', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->integer('year');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('finalized')->default(false);
            $table->timestamps();
        });
       
        Schema::create('planner_curricula', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('planner_group_curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups');
            $table->foreignId('planner_curriculum_id')->constrained('planner_curricula');
            $table->integer('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('planner_curriculum_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_curriculum_id')->constrained('planner_curricula');
            $table->string('content_type')->default('default');
            $table->string('custom_content')->nullable();
            $table->boolean('show_custom_content')->default(false);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('planner_curriculum_content_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_curriculum_content_id')
                ->constrained('planner_curriculum_contents')
                ->onDelete('cascade')
                ->name('fk_pcc_unit_pcc_id'); // Or else the identifier name will be "planner_curriculum_content_unit_planner_curriculum_content_id_foreign" etc., which is too long for the database
            $table->foreignId('unit_id')
                ->constrained('units')
                ->onDelete('cascade')
                ->name('fk_pcc_unit_unit_id');
            $table->timestamps();
        });

        Schema::create('planner_curriculum_content_meeting', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planner_curriculum_content_id')
                ->constrained('planner_curriculum_contents')
                ->onDelete('cascade')
                ->name('fk_pcc_meeting_pcc_id');
            $table->foreignId('meeting_id')
                ->constrained('meetings')
                ->onDelete('cascade')
                ->name('fk_pcc_meeting_meeting_id');
            $table->timestamps();
        });

        Schema::create('planner_weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups');
            $table->foreignId('planner_week_id')->constrained('planner_weeks');
            $table->foreignId('planner_curriculum_contents_id')
                ->nullable()
                ->constrained('planner_curriculum_contents');
            $table->json('units')->nullable();
            $table->json('meetings')->nullable();
            $table->decimal('trainees', 7, 1)->nullable();
            $table->string('content_type')->default('default');
            $table->string('custom_content')->nullable();
            $table->boolean('show_custom_content')->default(false);
            $table->timestamps();
        });

        // For automatically flagging uncompleted meetings
        Schema::table('flag_trainees', function (Blueprint $table) {
            $table->foreignId('meeting_id')
                ->after('flag_id')
                ->nullable()
                ->constrained('meetings')
                ->onDelete('cascade');
            $table->boolean('flagged_by_system')->default(false);
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flag_trainees', function (Blueprint $table) {
            $table->dropForeign(['meeting_id']);
            $table->dropColumn('meeting_id');
            $table->dropColumn('flagged_by_system');
        });

        Schema::dropIfExists('planner_weekly_schedules');
        Schema::dropIfExists('planner_curriculum_content_meeting');
        Schema::dropIfExists('planner_curriculum_content_unit');
        Schema::dropIfExists('planner_curriculum_contents');
        Schema::dropIfExists('planner_group_curricula');
        Schema::dropIfExists('planner_curricula');
        Schema::dropIfExists('planner_weeks');
    }
};
