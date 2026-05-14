<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\TouristSpot;
use App\Models\Criteria;
use App\Models\RecommendationRun;
use App\Models\SusSubmission;
use Illuminate\Support\Facades\DB;
use App\Exports\SubmissionsExport;
use App\Exports\SusSubmissionsExport;
use Maatwebsite\Excel\Facades\Excel;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $recentThreshold = now()->subDays(30)->timestamp;

        $authenticatedVisitors = DB::table('sessions')
            ->where('last_activity', '>=', $recentThreshold)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $guestVisitors = DB::table('sessions')
            ->where('last_activity', '>=', $recentThreshold)
            ->whereNull('user_id')
            ->whereNotNull('ip_address')
            ->distinct('ip_address')
            ->count('ip_address');

        $stats = [
            'locations' => Location::count(),
            'spots' => TouristSpot::count(),
            'criteria' => Criteria::count(),
            'people_visit' => $authenticatedVisitors + $guestVisitors,
            'submissions' => RecommendationRun::where('submitted_to_admin', true)->count(),
            'sus_submissions' => SusSubmission::count(),
        ];

        $chartData = [
            'labels' => ['People Visit (30 days)', 'Submissions', 'SUS Submissions'],
            'values' => [
                $stats['people_visit'],
                $stats['submissions'],
                $stats['sus_submissions'],
            ],
        ];

        return view('SuperAdmin.dashboard', compact('stats', 'chartData'));
    }

    public function submissions()
    {
        $submissions = RecommendationRun::with(['weightingMethod', 'user', 'favoriteTouristSpot'])
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

    public function exportSubmissions()
    {
        return Excel::download(new SubmissionsExport(), 'submissions_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    public function exportSusSubmissions()
    {
        return Excel::download(new SusSubmissionsExport(), 'sus_submissions_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
