<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sus_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommendation_run_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('guest_key', 64)->nullable();
            $table->foreign('guest_key')->references('guest_key')->on('user_demographics')->nullOnDelete();
            $table->json('sus_responses');
            $table->decimal('sus_score', 5, 2);
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique('recommendation_run_id');
            $table->index('sus_score');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sus_submissions');
    }
};
