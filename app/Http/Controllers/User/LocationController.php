<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\TouristSpot;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::query()
            ->select('id', 'name', 'image')
            ->withCount([
                'touristSpots as spots_count' => function ($query) {
                    $query->where('status', true);
                },
            ])
            ->with([
                'touristSpots' => function ($query) {
                    $query->select('id', 'location_id', 'name')
                        ->where('status', true)
                        ->orderBy('name')
                        ->limit(3);
                },
            ])
            ->orderBy('name')
            ->get()
            ->map(function (Location $location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'image' => $location->image,
                    'spots_count' => (int) ($location->spots_count ?? 0),
                    'popular_spots' => $location->touristSpots->pluck('name')->values()->all(),
                ];
            })
            ->values();

        $totalSpots = TouristSpot::query()->where('status', true)->count();

        return view('User.viewLocation', [
            'locations' => $locations,
            'totalStates' => $locations->count(),
            'totalSpots' => $totalSpots,
        ]);
    }
}
