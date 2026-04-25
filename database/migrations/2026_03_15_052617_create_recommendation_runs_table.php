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
        Schema::create('recommendation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_key', 64)->nullable();
            $table->foreign('guest_key')->references('guest_key')->on('user_demographics')->nullOnDelete();
            $table->index(['guest_key', 'criteria_signature']);
            $table->foreignId('weighting_method_id')->constrained()->onDelete('cascade');
            $table->json('criteria_id')->nullable();
            $table->json('criteria_weight')->nullable();
            $table->index(['user_id', 'weighting_method_id']);
            // use in comparison field (e.g. compare.blade.php)
            $table->string('criteria_signature')->nullable();
            $table->json('ranked_results')->nullable();
            $table->index(['user_id', 'criteria_signature']);
            $table->unsignedBigInteger('favorite_tourist_spot_id')->nullable();
            $table->foreign('favorite_tourist_spot_id')->references('id')->on('tourist_spots')->onDelete('set null');

            // send the compare result to admin
            $table->boolean('submitted_to_admin')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->string('submitter_name', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->index('submitted_to_admin');
            // time tracking fields
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('time_taken_seconds')->nullable();
            $table->index('time_taken_seconds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_runs');
    }
};
