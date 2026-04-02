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
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->string('criteria_signature')->nullable()->after('criteria_weight');
            $table->json('ranked_results')->nullable()->after('criteria_signature');
            $table->index(['user_id', 'criteria_signature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'criteria_signature']);
            $table->dropColumn(['criteria_signature', 'ranked_results']);
        });
    }
};
