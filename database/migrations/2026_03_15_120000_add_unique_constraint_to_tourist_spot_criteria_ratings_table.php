<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $duplicateGroups = DB::table('tourist_spot_criteria_ratings')
            ->select('tourist_spot_id', 'criteria_id', DB::raw('MAX(id) as keep_id'))
            ->groupBy('tourist_spot_id', 'criteria_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            DB::table('tourist_spot_criteria_ratings')
                ->where('tourist_spot_id', $group->tourist_spot_id)
                ->where('criteria_id', $group->criteria_id)
                ->where('id', '!=', $group->keep_id)
                ->delete();
        }

        Schema::table('tourist_spot_criteria_ratings', function (Blueprint $table) {
            $table->unique(
                ['tourist_spot_id', 'criteria_id'],
                'tscr_tourist_spot_id_criteria_id_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tourist_spot_criteria_ratings', function (Blueprint $table) {
            $table->dropUnique('tscr_tourist_spot_id_criteria_id_unique');
        });
    }
};
