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
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('guest_key', 64)->nullable()->after('user_id');
            $table->index(['guest_key', 'criteria_signature']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recommendation_runs', function (Blueprint $table) {
            $table->dropIndex(['guest_key', 'criteria_signature']);
            $table->dropColumn('guest_key');
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
