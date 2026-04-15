<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use App\Models\Location;
use App\Models\TouristSpot;

class ViewTouristSpot extends Controller
{
    public function index()
    {
        $reviewCriteriaId = Criteria::query()
            ->where('name', 'C7')
            ->value('id');

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

        return view('User.viewTouristSpot', compact('touristSpots', 'locations'));
    }
}
