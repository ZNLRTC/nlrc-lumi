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
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->unsignedInteger('helpful_count')->after('view_count')->default(0);
            $table->unsignedInteger('not_helpful_count')->after('helpful_count')->default(0);
            $table->datetime('last_reset_at')->after('not_helpful_count')->nullable();
        });

        Schema::create('kb_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('kb_articles')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('feedback', 255);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kb_feedback');

        Schema::table('kb_articles', function (Blueprint $table) {
            $table->dropColumn('helpful_count');
            $table->dropColumn('not_helpful_count');
            $table->dropColumn('last_reset_at');
        });
    }
};
