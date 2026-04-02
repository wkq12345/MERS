<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->json('sus_responses')->nullable()->after('time_taken_seconds');
            $table->decimal('sus_score', 5, 2)->nullable()->after('sus_responses');
            $table->timestamp('sus_submitted_at')->nullable()->after('sus_score');
            $table->index('sus_score');
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->dropIndex(['sus_score']);
            $table->dropColumn(['sus_responses', 'sus_score', 'sus_submitted_at']);
        });
    }
};
