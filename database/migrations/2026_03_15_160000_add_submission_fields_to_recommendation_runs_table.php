<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->boolean('submitted_to_admin')->default(false)->after('ranked_results');
            $table->timestamp('submitted_at')->nullable()->after('submitted_to_admin');
            $table->string('submitter_name', 100)->nullable()->after('submitted_at');
            $table->index('submitted_to_admin');
        });
    }

    public function down(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->dropIndex(['submitted_to_admin']);
            $table->dropColumn(['submitted_to_admin', 'submitted_at', 'submitter_name']);
        });
    }
};
