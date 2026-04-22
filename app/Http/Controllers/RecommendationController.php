<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\TouristSpot;
use App\Models\TouristSpotCriteriaRating;
use App\Models\CriteriaType;
use App\Models\WeightingMethod;
use App\Models\RecommendationRun;

use App\Models\SusSubmission;
use App\Models\UserDemographic;




use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RecommendationController extends Controller
{
    public function dashboard(Request $request)
    {
        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $latestRunQuery = RecommendationRun::query()->latest();
        $this->applyActorScope($latestRunQuery, $userId, $guestKey);
        $latestRun = $latestRunQuery->first();

        return view('User.dashboard', [
            'latestRunId' => $latestRun?->id,
            'latestRunMethodName' => optional($latestRun?->weightingMethod)->name,
            'existingSusScore' => $latestRun?->sus_score,
            'existingSusResponses' => $latestRun?->sus_responses,
            'latestRunCreatedAt' => $latestRun?->created_at,
        ]);
    }

    public function directRatingMethod(Request $request)
    {
        $this->startMethodTimer($request, 'drm');
        $criteriaTypes = CriteriaType::with('criteria')->orderBy('name')->get();
        $userWeights = [];
        $userId = \Illuminate\Support\Facades\Auth::id();
        if ($userId) {
            $userWeights = \App\Models\UserCriteriaWeight::where('user_id', $userId)
                ->pluck('weight', 'criteria_id')
                ->toArray();
        }
        return view('recommendations.drm', compact('criteriaTypes', 'userWeights'));
    }

    public function susPage(Request $request)
    {
        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $methods = WeightingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $methodCode = (string) $request->query('method_code', '');

        if ($methodCode === '') {
            $latestRunQuery = RecommendationRun::with('weightingMethod')->latest();
            $this->applyActorScope($latestRunQuery, $userId, $guestKey);
            $latestRun = $latestRunQuery->first();
            $methodCode = (string) optional(optional($latestRun)->weightingMethod)->code;
        }

        $selectedMethod = $methods->firstWhere('code', $methodCode);

        if (!$selectedMethod && $request->filled('method_code')) {
            return redirect()
                ->route('recommendations.compare')
                ->with('error', 'Selected method is invalid for SUS survey.');
        }

        $submission = null;
        if ($selectedMethod) {
            $runQuery = RecommendationRun::query()
                ->where('weighting_method_id', $selectedMethod->id)
                ->latest();
            $this->applyActorScope($runQuery, $userId, $guestKey);
            $submission = $runQuery->first();
        }

        return view('User.sus', [
            'submission' => $submission,
            'selectedMethod' => $selectedMethod,
            'methods' => $methods,
        ]);
    }

    public function hundredDollarMethod(Request $request)
    {
        $this->startMethodTimer($request, 'hdm');
        $criteriaTypes = CriteriaType::with('criteria')->orderBy('name')->get();
        $userWeights = [];
        $userId = \Illuminate\Support\Facades\Auth::id();
        if ($userId) {
            $userWeights = \App\Models\UserCriteriaWeight::where('user_id', $userId)
                ->pluck('weight', 'criteria_id')
                ->toArray();
        }
        return view('recommendations.hdm', compact('criteriaTypes', 'userWeights'));
    }

    public function kanoMethod(Request $request)
    {
        $this->startMethodTimer($request, 'kano');
        $criteriaTypes = CriteriaType::with('criteria')->orderBy('name')->get();
        return view('recommendations.kano', compact('criteriaTypes'));
    }

    public function calculateRecommendations(Request $request)
    {
        $request->validate([
            'weighting_method' => [
                'required',
                'string',
                Rule::exists('weighting_methods', 'code')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],

        ]);

        $weightingMethod = WeightingMethod::where('code', $request->input('weighting_method'))
            ->where('is_active', true)
            ->first();

        /* -----------------------------
         STEP 1: Load criteria + collect weights from request
        ------------------------------ */
        $allCriteria = Criteria::with('criteriaType')->get();
        if ($allCriteria->isEmpty()) {
            return back()->with('error', 'No criteria available.');
        }

        // Collect raw weights from the form or fallback to DB if available
        $weightsRaw = [];
        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        foreach ($allCriteria as $criterion) {
            $field = 'score_' . $criterion->id;
            if ($request->has($field)) {
                $val = (float) $request->input($field);
                if ($val > 0) {
                    $weightsRaw[$criterion->id] = $val;

                    // Save to user criteria weight table
                    if ($userId) {
                        \App\Models\UserCriteriaWeight::updateOrCreate(
                            ['user_id' => $userId, 'criteria_id' => $criterion->id],
                            ['weight' => $val]
                        );
                    }
                } else if ($userId) {
                    \App\Models\UserCriteriaWeight::where('user_id', $userId)
                        ->where('criteria_id', $criterion->id)
                        ->delete();
                }
            } elseif ($userId) {
                // If not in request, try to get from DB
                $dbWeight = \App\Models\UserCriteriaWeight::where('user_id', $userId)
                    ->where('criteria_id', $criterion->id)
                    ->first();
                if ($dbWeight && $dbWeight->weight > 0) {
                    $weightsRaw[$criterion->id] = $dbWeight->weight;
                }
            }
        }

        if (empty($weightsRaw)) {
            return back()->with('error', 'Weights not found.');
        }

        // Filter the criteria collection to ONLY include the user's selected criteria
        $criteria = $allCriteria->filter(function ($c) use ($weightsRaw) {
            return isset($weightsRaw[$c->id]) && $weightsRaw[$c->id] > 0;
        })->values();

        $criteriaIds = $criteria->pluck('id')->toArray();



        /* -----------------------------
         STEP 2: Active tourist spots
        ------------------------------ */
        $touristSpots = TouristSpot::with(['location', 'ratings'])
            ->where('status', true)
            ->get();
        if ($touristSpots->isEmpty()) {
            return back()->with('error', 'No active tourist spots.');
        }

        /* -----------------------------
         STEP 4: Decision matrix
        ------------------------------ */
        $ratings = TouristSpotCriteriaRating::whereIn('tourist_spot_id', $touristSpots->pluck('id'))
            ->whereIn('criteria_id', $criteriaIds)
            ->get();

        $decisionMatrix = [];
        foreach ($touristSpots as $spot) {
            foreach ($criteria as $criterion) {
                $decisionMatrix[$spot->id][$criterion->id] = 0;
            }
        }

        foreach ($ratings as $rating) {
            $decisionMatrix[$rating->tourist_spot_id][$rating->criteria_id] = $rating->raw_value;
        }

        /* -----------------------------
         STEP 5: Normalize matrix
         Transforms values like 150km and 4.5 stars into a common 0-1 scale.
        ------------------------------ */
        $normalizedMatrix = [];

        foreach ($criteria as $criterion) {
            // 1. Calculate the Vector Magnitude (Denominator) for this specific criterion
            $sumSquares = 0;
            foreach ($touristSpots as $spot) {
                // Get the raw value (e.g., 100 for distance, 5 for review)
                $val = $decisionMatrix[$spot->id][$criterion->id] ?? 0;
                $sumSquares += pow($val, 2);
            }
            // Sqrt of sum of squares
            $denominator = sqrt($sumSquares);

            // 2. Divide every spot's value by this denominator
            foreach ($touristSpots as $spot) {
                $originalValue = $decisionMatrix[$spot->id][$criterion->id] ?? 0;

                // Avoid division by zero if all values are 0
                $normalizedVal = $denominator > 0
                    ? $originalValue / $denominator
                    : 0;

                $normalizedMatrix[$spot->id][$criterion->id] = $normalizedVal;
            }
        }

        /* -----------------------------
         STEP 6: Normalize weights
        ------------------------------ */
        $totalWeight = array_sum($weightsRaw);
        $normalizedWeights = [];

        foreach ($criteria as $criterion) {
            $normalizedWeights[$criterion->id] =
                $totalWeight > 0
                ? ($weightsRaw[$criterion->id] ?? 0) / $totalWeight
                : 1 / count(
                    $criteria,
                    4
                );
        }

        /* -----------------------------
         STEP 7: Weighted matrix
        ------------------------------ */
        $weightedMatrix = [];
        foreach ($touristSpots as $spot) {
            foreach ($criteria as $criterion) {
                $weightedMatrix[$spot->id][$criterion->id] =
                    $normalizedMatrix[$spot->id][$criterion->id] *
                    $normalizedWeights[$criterion->id];
            }
        }

        /* -----------------------------
         STEP 8: Ideal best & worst
        ------------------------------ */
        $idealBest = [];
        $idealWorst = [];

        foreach ($criteria as $criterion) {
            $values = [];
            foreach ($touristSpots as $spot) {
                $values[] = $weightedMatrix[$spot->id][$criterion->id];
            }

            if ($criterion->criteriaType->ideal_preference === 'min') {
                $idealBest[$criterion->id]  = min($values);
                $idealWorst[$criterion->id] = max($values);
            } else if ($criterion->criteriaType->ideal_preference === 'max') {
                $idealBest[$criterion->id]  = max($values);
                $idealWorst[$criterion->id] = min($values);
            }
        }

        /* -----------------------------
         STEP 9: Separation measures
        ------------------------------ */
        $separations = [];
        foreach ($touristSpots as $spot) {
            $plus = 0;
            $minus = 0;

            foreach ($criteria as $criterion) {
                $v = $weightedMatrix[$spot->id][$criterion->id];
                $plus  += pow($v - $idealBest[$criterion->id], 2);
                $minus += pow($v - $idealWorst[$criterion->id], 2);
            }

            $separations[$spot->id] = [
                'positive' => sqrt($plus),
                'negative' => sqrt($minus),
            ];
        }

        /* -----------------------------
         STEP 10: Relative closeness
        ------------------------------ */
        $relativeCloseness = [];
        foreach ($touristSpots as $spot) {
            $sp = $separations[$spot->id]['positive'];
            $sn = $separations[$spot->id]['negative'];

            $relativeCloseness[$spot->id] =
                ($sp + $sn) > 0 ? $sn / ($sp + $sn) : 0;
        }

        arsort($relativeCloseness);

        /* -----------------------------
         STEP 11: Ranking output
        ------------------------------ */
        $rank = 1;
        $results = [];

        foreach ($relativeCloseness as $spotId => $ci) {

            $spot = $touristSpots->firstWhere('id', $spotId);

            $results[] = [
                'rank' => $rank++,
                'tourist_spot_id' => $spot->id,
                'tourist_spot' => $spot->name,
                'score' => round($ci, 4),
            ];
        }

        $selectedCriteriaPayload = $criteria->map(function ($criterion) {
            return [
                'id' => $criterion->id,
                'name' => $criterion->name,
            ];
        })->values();

        $criteriaIdsSorted = $selectedCriteriaPayload->pluck('id')->map(function ($id) {
            return (int) $id;
        })->sort()->values()->all();

        $criteriaSignature = implode('-', $criteriaIdsSorted);

        if ($weightingMethod && ($userId || $guestKey)) {
            $completedAt = now();
            $startedAt = $this->resolveMethodStartTime($request, $weightingMethod->code);
            $timeTakenSeconds = $startedAt
                ? max($startedAt->diffInSeconds($completedAt), 0)
                : null;
            $submitterName = \Illuminate\Support\Facades\Auth::user()?->name ?? 'Guest';
            RecommendationRun::create([
                'user_id' => $userId,
                'guest_key' => $guestKey,
                'weighting_method_id' => $weightingMethod->id,
                'criteria_id' => $selectedCriteriaPayload->pluck('id')->values()->all(),
                'criteria_weight' => $normalizedWeights,
                'criteria_signature' => $criteriaSignature,
                'ranked_results' => $results,
                'submitted_to_admin' => true,
                'submitted_at' => now(),
                'submitter_name' => $submitterName,
                'ip_address' => $request->ip(),
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'time_taken_seconds' => $timeTakenSeconds,
            ]);

            $request->session()->forget($this->methodTimerSessionKey($weightingMethod->code));
        }

        $methodStatuses = $this->buildMethodStatuses($userId, $guestKey);

        return redirect()->route('recommendations.results')->with('resultDataRunId', $weightingMethod->id);
    }

    public function showResults(Request $request)
    {
        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        // Fetch the most recent RecommendationRun regardless of session data
        $runQuery = RecommendationRun::query()->latest();
        $this->applyActorScope($runQuery, $userId, $guestKey);
        $run = $runQuery->first();

        if (!$run) {
            return redirect()->route('recommendations.drm')
                ->with('error', 'No recent calculation found. Please run the process again.');
        }

        // Fetch fresh data from DB for display
        $touristSpots = TouristSpot::with(['location', 'ratings'])
            ->where('status', true)
            ->get();

        $selectedCriteria = Criteria::whereIn('id', collect($run->criteria_id)->toArray())
            ->get()
            ->map(function ($criterion) {
                return [
                    'id' => $criterion->id,
                    'name' => $criterion->name,
                ];
            })
            ->values();

        $methodStatuses = $this->buildMethodStatuses($actor['user_id'], $actor['guest_key']);

        $selectedFavorite = $run->favorite_tourist_spot_id;

        return view('recommendations.results', [
            'results' => $run->ranked_results ?? [],
            'touristSpots' => $touristSpots,
            'selectedCriteria' => $selectedCriteria,
            'normalizedWeights' => $run->criteria_weight ?? [],
            'criteriaSignature' => $run->criteria_signature,
            'currentMethodCode' => optional($run->weightingMethod)->code,
            'methodStatuses' => $methodStatuses,
            'separations' => [],
            'selectedFavorite' => $selectedFavorite,
        ]);
    }

    public function showPreviousResult(Request $request)
    {
        $methodCode = $request->query('method_code');

        if (!$methodCode) {
            return redirect()->route('recommendations.compare')
                ->with('error', 'Method code is required.');
        }

        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $weightingMethod = WeightingMethod::where('code', $methodCode)
            ->where('is_active', true)
            ->first();

        if (!$weightingMethod) {
            return redirect()->route('recommendations.compare')
                ->with('error', 'Weighting method not found.');
        }

        $runQuery = \App\Models\RecommendationRun::query()
            ->where('weighting_method_id', $weightingMethod->id)
            ->latest();

        $this->applyActorScope($runQuery, $userId, $guestKey);
        $run = $runQuery->first();

        if (!$run) {
            return redirect()->route('recommendations.compare')
                ->with('error', 'No previous result found for this method.');
        }

        $touristSpots = TouristSpot::with(['location', 'ratings'])
            ->where('status', true)
            ->get();

        $selectedCriteria = Criteria::whereIn('id', collect($run->criteria_id)->toArray())
            ->get()
            ->map(function ($criterion) {
                return [
                    'id' => $criterion->id,
                    'name' => $criterion->name,
                ];
            })
            ->values();

        $methodStatuses = $this->buildMethodStatuses($userId, $guestKey);

        return view('recommendations.results', [
            'results' => $run->ranked_results ?? [],
            'touristSpots' => $touristSpots,
            'selectedCriteria' => $selectedCriteria,
            'normalizedWeights' => $run->criteria_weight ?? [],
            'criteriaSignature' => $run->criteria_signature,
            'currentMethodCode' => $weightingMethod->code,
            'methodStatuses' => $methodStatuses,
            'separations' => [],
            'selectedFavorite' => $run->favorite_tourist_spot_id,
        ]);
    }

    public function submitSystemUsabilityScale(Request $request)
    {
        $validated = $request->validate([
            'method_code' => [
                'required',
                'string',
                Rule::exists('weighting_methods', 'code')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'sus_q1' => ['required', 'integer', 'between:1,5'],
            'sus_q2' => ['required', 'integer', 'between:1,5'],
            'sus_q3' => ['required', 'integer', 'between:1,5'],
            'sus_q4' => ['required', 'integer', 'between:1,5'],
            'sus_q5' => ['required', 'integer', 'between:1,5'],
            'sus_q6' => ['required', 'integer', 'between:1,5'],
            'sus_q7' => ['required', 'integer', 'between:1,5'],
            'sus_q8' => ['required', 'integer', 'between:1,5'],
            'sus_q9' => ['required', 'integer', 'between:1,5'],
            'sus_q10' => ['required', 'integer', 'between:1,5'],
        ]);

        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $weightingMethod = WeightingMethod::where('code', $validated['method_code'])
            ->where('is_active', true)
            ->first();

        if (!$weightingMethod) {
            return redirect()
                ->route('recommendations.compare')
                ->with('error', 'Weighting method not found for SUS submission.');
        }

        $runQuery = RecommendationRun::query()
            ->where('weighting_method_id', $weightingMethod->id)
            ->latest();
        $this->applyActorScope($runQuery, $userId, $guestKey);
        $run = $runQuery->first();

        if (!$run) {
            return redirect()
                ->route('recommendations.compare')
                ->with('error', 'Please complete the selected method before submitting SUS feedback.');
        }

        if (!is_null($run->sus_submitted_at)) {
            return redirect()
                ->route('recommendations.sus.index', ['method_code' => $weightingMethod->code])
                ->with('info', 'You have already submitted SUS feedback for this method.');
        }

        $responses = [];
        for ($i = 1; $i <= 10; $i++) {
            $responses['q' . $i] = (int) $validated['sus_q' . $i];
        }

        $susSum = 0;
        for ($i = 1; $i <= 10; $i++) {
            $answer = $responses['q' . $i];
            $susSum += ($i % 2 !== 0)
                ? max($answer - 1, 0)
                : max(5 - $answer, 0);
        }

        $susScore = round($susSum * 2.5, 2);

        SusSubmission::updateOrCreate(
            ['recommendation_run_id' => $run->id],
            [
                'guest_key' => $guestKey,
                'sus_responses' => $responses,
                'sus_score' => $susScore,
                'submitted_at' => now(),
            ]
        );

        $run->update([
            'sus_responses' => $responses,
            'sus_score' => $susScore,
            'sus_submitted_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('recommendations.sus.index', ['method_code' => $weightingMethod->code])
            ->with('success', 'SUS feedback for ' . $weightingMethod->name . ' submitted successfully. Score: ' . number_format($susScore, 2) . '/100.');
    }

    public function compareRecommendations(Request $request)
    {
        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $methods = WeightingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $criteriaSignature = (string) $request->query('criteria_signature', '');

        $allRunsQuery = RecommendationRun::with('weightingMethod');
        $this->applyActorScope($allRunsQuery, $userId, $guestKey);

        $allRuns = $allRunsQuery
            ->latest()
            ->get();

        $methodRuns = [];
        foreach ($methods as $method) {
            $methodRuns[$method->code] = $allRuns->first(function ($run) use ($method) {
                return optional($run->weightingMethod)->code === $method->code;
            });
        }

        $availableSignatures = collect($methodRuns)
            ->filter()
            ->map(function ($run) {
                return $run->criteria_signature ?: $this->criteriaSignatureFromArray($run->criteria_id);
            })
            ->filter()
            ->unique()
            ->values();

        if ($criteriaSignature === '' && $availableSignatures->isNotEmpty()) {
            $criteriaSignature = (string) $availableSignatures->first();
        }

        $hasMixedCriteriaSignatures = $availableSignatures->count() > 1;

        $allSelectedCriteriaIds = collect($methodRuns)
            ->filter()
            ->flatMap(function ($run) {
                return is_array($run->criteria_id) ? $run->criteria_id : [];
            })
            ->map(function ($id) {
                return (int) $id;
            })
            ->filter()
            ->unique()
            ->values();

        $criteriaNameMap = [];
        if ($allSelectedCriteriaIds->isNotEmpty()) {
            $criteriaNameMap = Criteria::whereIn('id', $allSelectedCriteriaIds->all())
                ->pluck('name', 'id')
                ->toArray();
        }

        $methodSelectedCriteria = [];
        foreach ($methods as $method) {
            $run = $methodRuns[$method->code] ?? null;
            $criteriaIdsForMethod = ($run && is_array($run->criteria_id))
                ? array_map('intval', $run->criteria_id)
                : [];

            $criteriaNamesForMethod = [];
            foreach ($criteriaIdsForMethod as $criteriaId) {
                if (isset($criteriaNameMap[$criteriaId])) {
                    $criteriaNamesForMethod[] = $criteriaNameMap[$criteriaId];
                }
            }

            $methodSelectedCriteria[$method->code] = $criteriaNamesForMethod;
        }

        $spotRows = [];
        foreach ($methodRuns as $methodCode => $run) {
            $rankedResults = $run && is_array($run->ranked_results) ? $run->ranked_results : [];

            foreach ($rankedResults as $result) {
                $spotId = $result['tourist_spot_id'] ?? null;
                if (!$spotId) {
                    continue;
                }

                if (!isset($spotRows[$spotId])) {
                    $spotRows[$spotId] = [
                        'tourist_spot_id' => $spotId,
                        'tourist_spot' => $result['tourist_spot'] ?? ('Spot #' . $spotId),
                        'ranks' => [],
                        'scores' => [],
                    ];
                }

                $spotRows[$spotId]['ranks'][$methodCode] = isset($result['rank']) ? (int) $result['rank'] : null;
                $spotRows[$spotId]['scores'][$methodCode] = isset($result['score']) ? (float) $result['score'] : null;
            }
        }

        foreach ($spotRows as &$row) {
            $rankValues = array_filter($row['ranks'], function ($rank) {
                return is_int($rank) && $rank > 0;
            });

            $row['avg_rank'] = !empty($rankValues)
                ? round(array_sum($rankValues) / count($rankValues), 2)
                : null;
        }
        unset($row);

        $compareRows = collect($spotRows)
            ->sortBy(function ($row) {
                return $row['avg_rank'] ?? PHP_INT_MAX;
            })
            ->values();

        return view('recommendations.compare', [
            'methods' => $methods,
            'methodRuns' => $methodRuns,
            'methodSelectedCriteria' => $methodSelectedCriteria,
            'compareRows' => $compareRows,
            'criteriaSignature' => $criteriaSignature,
            'hasMixedCriteriaSignatures' => $hasMixedCriteriaSignatures,
        ]);
    }

    public function sendResultsToAdmin(Request $request)
    {
        $request->validate([
            'run_id' => ['required', 'integer', 'min:1'],
        ]);

        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $query = RecommendationRun::where('id', (int) $request->input('run_id'));
        $this->applyActorScope($query, $userId, $guestKey);

        $run = $query->first();

        if (!$run) {
            return redirect()->route('recommendations.compare')
                ->with('error', 'Result not found or access denied.');
        }

        if ($run->submitted_to_admin) {
            return redirect()->route('recommendations.compare')
                ->with('success', 'This result has already been submitted to the admin.');
        }

        $senderName = \Illuminate\Support\Facades\Auth::user()?->name ?? 'Guest';

        $run->update([
            'submitted_to_admin' => true,
            'submitted_at' => now(),
            'submitter_name' => $senderName,
            'ip_address' => $request->ip(),

        ]);

        return redirect()->route('recommendations.compare')
            ->with('success', 'Your result has been submitted to the admin for review!');
    }

    public function clearMethodResult(Request $request)
    {
        $request->validate([
            'method_code' => [
                'required',
                'string',
                Rule::exists('weighting_methods', 'code')->where(function ($query) {
                    $query->where('is_active', true);
                }),
            ],
            'criteria_signature' => ['nullable', 'string'],
        ]);

        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $weightingMethod = WeightingMethod::where('code', $request->input('method_code'))
            ->where('is_active', true)
            ->first();

        if (!$weightingMethod) {
            return back()->with('error', 'Weighting method not found.');
        }

        $query = RecommendationRun::query()
            ->where('weighting_method_id', $weightingMethod->id);

        $this->applyActorScope($query, $userId, $guestKey);
        $deletedCount = $query->delete();

        if ($deletedCount > 0) {
            return back()->with('success', $weightingMethod->name . ' result has been cleared. You can redo it now.');
        }

        return back()->with('error', 'No saved result found to clear for this method.');
    }

    private function buildMethodStatuses(?int $userId, ?string $guestKey): array
    {
        $methods = WeightingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $statuses = [];
        $runsQuery = RecommendationRun::with('weightingMethod');
        $this->applyActorScope($runsQuery, $userId, $guestKey);

        $runs = $runsQuery
            ->latest()
            ->get();

        foreach ($methods as $method) {
            $run = $runs->first(function ($item) use ($method) {
                return optional($item->weightingMethod)->code === $method->code;
            });

            $statuses[$method->code] = [
                'name' => $method->name,
                'has_run' => (bool) $run,
            ];
        }

        return $statuses;
    }

    public function saveFavorite(Request $request)
    {
        $request->validate([
            'tourist_spot_id' => 'required|exists:tourist_spots,id',
            'method_code' => 'required|string',
        ]);

        if ($request->session()->has('resultData')) {
            $request->session()->keep(['resultData']);
        }

        $actor = $this->resolveActorContext($request);
        $userId = $actor['user_id'];
        $guestKey = $actor['guest_key'];

        $method = WeightingMethod::where('code', $request->input('method_code'))->first();

        if (!$method) {
            return response()->json(['success' => false, 'message' => 'Method not found.'], 404);
        }

        $query = RecommendationRun::where('weighting_method_id', $method->id);
        $this->applyActorScope($query, $userId, $guestKey);

        $run = $query->latest()->first();

        if ($run) {
            $run->update(['favorite_tourist_spot_id' => $request->input('tourist_spot_id')]);
            return response()->json(['success' => true, 'message' => 'Favorite spot saved.']);
        }

        return response()->json(['success' => false, 'message' => 'Run not found.'], 404);
    }

    private function resolveActorContext(Request $request): array
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $guestKey = null;

        if (!$userId) {
            $guestKey = (string) $request->session()->get('recommendation_guest_key', '');

            if ($guestKey === '') {
                $guestKey = (string) Str::uuid();
                $request->session()->put('recommendation_guest_key', $guestKey);
            }
        }

        return [
            'user_id' => $userId,
            'guest_key' => $guestKey,
        ];
    }

    private function applyActorScope($query, ?int $userId, ?string $guestKey): void
    {
        if ($userId) {
            $query->where('user_id', $userId);
            return;
        }

        if (!empty($guestKey)) {
            $query->whereNull('user_id')->where('guest_key', $guestKey);
            return;
        }

        $query->whereRaw('1 = 0');
    }

    private function criteriaSignatureFromArray($criteriaIds): string
    {
        if (!is_array($criteriaIds) || empty($criteriaIds)) {
            return '';
        }

        $ids = array_map('intval', $criteriaIds);
        sort($ids);
        return implode('-', $ids);
    }

    private function startMethodTimer(Request $request, string $methodCode): void
    {
        $request->session()->put($this->methodTimerSessionKey($methodCode), now()->toIso8601String());
    }

    private function resolveMethodStartTime(Request $request, string $methodCode): ?\Illuminate\Support\Carbon
    {
        $startedAt = $request->session()->get($this->methodTimerSessionKey($methodCode));

        if (empty($startedAt)) {
            return null;
        }

        try {
            return now()->parse($startedAt);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function methodTimerSessionKey(string $methodCode): string
    {
        return 'recommendation_method_started_at.' . $methodCode;
    }
}
