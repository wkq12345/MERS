<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('submitter_name');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->unsignedInteger('time_taken_seconds')->nullable()->after('completed_at');
            $table->index('time_taken_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->dropIndex(['time_taken_seconds']);
            $table->dropColumn(['started_at', 'completed_at', 'time_taken_seconds']);
        });
    }
};
