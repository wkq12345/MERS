<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TouristSpot;

class TouristSpotController extends Controller
{
    public function index()
    {
        $touristSpots = TouristSpot::with(['location', 'ratings'])->get();
        return view('user.tourist-spots.index', compact('touristSpots'));
    }
}
