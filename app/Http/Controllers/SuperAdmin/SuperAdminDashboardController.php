<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\TouristSpot;
use App\Models\Criteria;
use App\Models\RecommendationRun;
use App\Models\SusSubmission;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'locations' => Location::count(),
            'spots' => TouristSpot::count(),
            'criteria' => Criteria::count(),
            'submissions' => RecommendationRun::where('submitted_to_admin', true)->count(),
            'sus_submissions' => SusSubmission::count(),
        ];
        return view('SuperAdmin.dashboard', compact('stats'));
    }

    public function submissions()
    {
        $submissions = RecommendationRun::with(['weightingMethod', 'user'])
            ->where('submitted_to_admin', true)
            ->latest('submitted_at')
            ->paginate(20);

        return view('SuperAdmin.submissions', compact('submissions'));
    }

    public function susSubmissions()
    {
        $submissions = SusSubmission::with('user')
            ->latest('submitted_at')
            ->paginate(20);

        return view('SuperAdmin.sus-submissions', compact('submissions'));
    }
}
