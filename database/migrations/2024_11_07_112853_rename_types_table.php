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
        Schema::rename('types', 'group_types');

        Schema::table('groups', function(Blueprint $table) {
            $table->renameColumn('type_id', 'group_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('group_types', 'types');

        Schema::table('groups', function(Blueprint $table) {
            $table->renameColumn('group_type_id', 'type_id');
        });
    }
};
