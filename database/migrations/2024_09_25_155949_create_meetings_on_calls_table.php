<?php

use App\Enums\MeetingsOnCallsMeetingStatus;
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
        Schema::create('meetings_on_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('meeting_link', 64);
            $table->string('meeting_status', 32)
                ->nullable()
                ->default(MeetingsOnCallsMeetingStatus::PENDING);
            $table->date('meeting_date');
            $table->dateTime('start_time')
                ->nullable();
            $table->dateTime('end_time')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings_on_calls');
    }
};
