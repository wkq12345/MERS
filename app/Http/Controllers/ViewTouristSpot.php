<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Location;
use App\Models\TouristSpot;
use Illuminate\Http\Request;

class ViewTouristSpot extends Controller
{
    public function index(Request $request)
    {
        // Use criteria_id = 7 for displayed rating; fallback by name for compatibility.
        $reviewCriteriaId = 7;
        $criteriaExists = Criteria::query()->whereKey($reviewCriteriaId)->exists();

        if (! $criteriaExists) {
            $reviewCriteriaId = Criteria::query()
                ->whereIn('name', ['C7', 'Rating'])
                ->value('id');
        }

        $touristSpots = TouristSpot::with([
            'location:id,name',
            'ratings.criteria:id,name',
        ])
            ->where('status', true)
            ->get()
            ->map(function (TouristSpot $spot) use ($reviewCriteriaId) {
                $c7Rating = $spot->ratings
                    ->firstWhere('criteria_id', $reviewCriteriaId)?->raw_value;

                return [
                    'id' => $spot->id,
                    'name' => $spot->name,
                    'description' => $spot->description,
                    'review_link' => $spot->review_link,
                    'image' => $spot->image,
                    'location' => $spot->location?->name,
                    'rating' => (float) ($c7Rating ?? 0),
                    'criteria_ratings' => $spot->ratings->map(function ($rating) {
                        return [
                            'name' => $rating->criteria?->name ?? ('C' . $rating->criteria_id),
                            'score' => (float) $rating->raw_value,
                        ];
                    })->values()->all(),
                ];
            })
            ->values();

        $locations = Location::query()
            ->orderBy('name')
            ->pluck('name')
            ->all();

        $initialLocation = $request->query('location');
        if (! in_array($initialLocation, $locations, true)) {
            $initialLocation = null;
        }

        return view('User.viewTouristSpot', compact('touristSpots', 'locations', 'initialLocation'));
    }
}
