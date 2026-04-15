@extends('layouts.user')

@section('title', 'Your Personalized Recommendations')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            important: true,
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    <style>
        button {
            background-color: transparent;
            background-image: none;
            cursor: pointer;
            border: none;
            outline: none;
        }
    </style>
@endpush

@section('content')
    @php
        $rankedSpots = [];
        $methodRouteMap = [
            'drm' => route('recommendations.drm'),
            'hdm' => route('recommendations.hdm'),
            'kano' => route('recommendations.kano'),
        ];
        $selectedCriteriaMeta = collect($selectedCriteria ?? [])->values();
        $criteriaThemes = [
            ['bg' => 'bg-purple-50', 'border' => 'border-purple-500', 'text' => 'text-purple-600'],
            ['bg' => 'bg-blue-50', 'border' => 'border-blue-500', 'text' => 'text-blue-600'],
            ['bg' => 'bg-green-50', 'border' => 'border-green-500', 'text' => 'text-green-600'],
            ['bg' => 'bg-orange-50', 'border' => 'border-orange-500', 'text' => 'text-orange-600'],
            ['bg' => 'bg-pink-50', 'border' => 'border-pink-500', 'text' => 'text-pink-600'],
            ['bg' => 'bg-cyan-50', 'border' => 'border-cyan-500', 'text' => 'text-cyan-600'],
            ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-600'],
            ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-500', 'text' => 'text-indigo-600'],
        ];

        foreach ($results as $result) {
            $spot = $touristSpots->firstWhere('id', $result['tourist_spot_id']);
            if (!$spot) {
                continue;
            }

            $image = $spot->image;
            if ($image && !str_starts_with($image, 'http')) {
                $image = asset($image);
            } elseif (!$image) {
                $image = 'https://placehold.co/800x600?text=' . urlencode($spot->name);
            }

            $ratingsById = $spot->ratings->keyBy('criteria_id');

            $criteriaValues = [];

            // Get star rating specifically from criteria_id = 7
            $starRatingRecord = $ratingsById[7] ?? null;
            $reviewScore = $starRatingRecord ? (float) $starRatingRecord->raw_value : null;

            foreach ($selectedCriteriaMeta as $index => $criterionMeta) {
                $criterionId = (int) ($criterionMeta['id'] ?? 0);
                if ($criterionId <= 0) {
                    continue;
                }

                $criterionName = (string) ($criterionMeta['name'] ?? 'Criterion ' . $criterionId);
                $criterionNameLower = strtolower($criterionName);
                $rawValue = $ratingsById[$criterionId]->raw_value ?? null;

                $suffix = '';
                if (str_contains($criterionNameLower, 'distance')) {
                    $suffix = ' km';
                } elseif ($criterionId === 7) {
                    $suffix = ' / 5.0';
                }

                $criteriaValues[] = [
                    'id' => $criterionId,
                    'name' => $criterionName,
                    'value' => $rawValue,
                    'suffix' => $suffix,
                    'theme' => $criteriaThemes[$index % count($criteriaThemes)],
                ];
            }

            if ($reviewScore === null) {
                $reviewScore = collect($criteriaValues)->pluck('value')->first(fn($v) => $v !== null && $v !== '');
                $reviewScore = $reviewScore ? (float) $reviewScore : null;
            }

            $parsedLinks = [];
            if (!empty($spot->review_link)) {
                foreach (explode('|||||', $spot->review_link) as $url) {
                    $cleanUrl = trim($url);
                    if (!empty($cleanUrl)) {
                        $host = parse_url($cleanUrl, PHP_URL_HOST) ?? '';
                        $siteName = 'View Details';
                        if (stripos($host, 'tripadvisor') !== false) {
                            $siteName = 'TripAdvisor';
                        } elseif (stripos($host, 'klook') !== false) {
                            $siteName = 'Klook';
                        } elseif (stripos($host, 'trip.com') !== false) {
                            $siteName = 'Trip.com';
                        } elseif (stripos($host, 'agoda') !== false) {
                            $siteName = 'Agoda';
                        } elseif (stripos($host, 'traveloka') !== false) {
                            $siteName = 'Traveloka';
                        }
                        $parsedLinks[] = ['url' => $cleanUrl, 'name' => $siteName];
                    }
                }
            }

            $rankedSpots[] = [
                'id' => $spot->id,
                'name' => $spot->name,
                'location' => $spot->location ? $spot->location->name : 'Unknown',
                'description' => $spot->description,
                'rating' => $reviewScore ? round((float) $reviewScore, 1) : 0,
                'image' => $image,
                'criteriaValues' => $criteriaValues,
                'review_links' => $parsedLinks,
                'topsisScore' => $result['score'],
                'rank' => $result['rank'],
            ];
        }

        $locations = collect($rankedSpots)->pluck('location')->filter()->unique()->sort()->values()->all();
        array_unshift($locations, 'All Locations');
    @endphp

    <div x-data="rankingData()" x-init="init()" class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="mb-8">
                <h2 class="text-3xl mb-2 text-gray-900 font-semibold">Ranked Tourist Spots</h2>
                <p class="text-gray-600">Based on your preferences, here are the top recommended ecotourism destinations</p>
            </div>

            <!-- Notification Banner -->
            <div x-show="selectedFavorite === null" x-transition.duration.500ms
                class="mb-6 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-4 shadow-sm flex items-start gap-3">
                <i class="bi bi-info-circle-fill text-blue-500 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-blue-900">Don't forget to pick a favorite!</h3>
                    <p class="text-sm text-blue-700 mt-1">Review the results below and click <b>"Mark as Favorite"</b> on
                        the spot you'd most like to visit. Your choice will be saved with this recommendation result.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <button type="button"
                                class="w-full flex items-center justify-between text-left border-none outline-none p-0 focus:outline-none"
                                @click="showFilters = !showFilters">
                                <div class="flex items-center gap-2">
                                    <i class="bi bi-funnel text-blue-600"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Filter by Location</h3>
                                </div>
                                <i class="bi" :class="showFilters ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </button>

                            <div class="space-y-2 mt-4" x-show="showFilters" x-transition>
                                <template x-for="location in locations" :key="location">
                                    <label
                                        class="flex items-center gap-3 p-2 rounded hover:bg-gray-50 cursor-pointer transition-colors">
                                        <input type="radio" name="location" :value="location"
                                            x-model="selectedLocation" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700" x-text="location"></span>
                                        <span class="ml-auto text-xs text-gray-500"
                                            x-text="getLocationCount(location)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-bar-chart-line text-emerald-600"></i>
                                <h3 class="text-base font-semibold text-gray-900">Compare Methods</h3>
                            </div>

                            <div class="space-y-2 mb-4">
                                @foreach ($methodStatuses ?? [] as $code => $status)
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-700">{{ $status['name'] }}</span>
                                        @if ($status['has_run'])
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Ready</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Not
                                                Run</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ route('recommendations.compare') }}"
                                class="block text-center w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors no-underline hover:no-underline focus:no-underline">
                                Open Comparison
                            </a>
                        </div>

                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-1 shadow-lg">
                            <div class="bg-white rounded-lg p-5">
                                <button type="button"
                                    class="w-full flex items-center justify-between text-left border-none outline-none p-0 focus:outline-none"
                                    @click="showMethods = !showMethods">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-arrow-repeat text-indigo-600"></i>
                                        <h3 class="text-base font-semibold text-gray-900">Try Another Method</h3>
                                    </div>
                                    <i class="bi" :class="showMethods ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                </button>

                                <div class="mt-3 space-y-2" x-show="showMethods" x-transition>
                                    @foreach ($methodStatuses ?? [] as $methodCode => $status)
                                        @php
                                            $isCompleted = (bool) ($status['has_run'] ?? false);
                                            $isCurrentMethod = ($currentMethodCode ?? '') === $methodCode;
                                        @endphp

                                        @if (!$isCompleted)
                                            <a href="{{ $methodRouteMap[$methodCode] ?? '#' }}"
                                                class="block w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors no-underline hover:no-underline focus:no-underline">
                                                {{ $status['name'] }}
                                            </a>
                                        @else
                                            <div
                                                class="w-full px-4 py-2 bg-gray-100 text-gray-500 rounded-lg cursor-not-allowed flex items-center justify-between">
                                                <span>{{ $status['name'] }}</span>
                                                <span class="text-xs font-semibold">Completed</span>
                                            </div>

                                            <form id="clear-form-{{ $methodCode }}" method="POST"
                                                action="{{ route('recommendations.clear_method') }}">
                                                @csrf
                                                <input type="hidden" name="method_code" value="{{ $methodCode }}">
                                                <input type="hidden" name="criteria_signature"
                                                    value="{{ $criteriaSignature ?? '' }}">
                                                <button type="button" data-clear-form="clear-form-{{ $methodCode }}"
                                                    data-method-name="{{ $status['name'] }}"
                                                    class="clear-method-btn w-full px-4 py-2 bg-white border border-indigo-300 text-indigo-700 rounded-lg hover:bg-indigo-50 transition-colors">
                                                    {{ $isCurrentMethod ? 'Clear and Redo This Method' : 'Clear and Enable This Method' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <template x-if="filteredSpots.length === 0">
                        <div class="bg-white rounded-lg p-12 text-center">
                            <i class="bi bi-funnel text-gray-300 text-4xl"></i>
                            <p class="text-gray-500 mt-4">No tourist spots found for the selected location.</p>
                            <button @click="selectedLocation = 'All Locations'"
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                View All Locations
                            </button>
                        </div>
                    </template>

                    <div class="space-y-4" x-show="filteredSpots.length > 0">
                        <template x-for="spot in filteredSpots" :key="spot.id">
                            <div
                                class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5">
                                    <div class="md:col-span-1">
                                        <div class="aspect-video md:aspect-square rounded-lg overflow-hidden bg-gray-200">
                                            <img :src="spot.image" :alt="spot.name"
                                                class="w-full h-full object-cover">
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="flex items-center gap-3">
                                                <div class="px-3 py-1.5 rounded-lg flex items-center gap-2 shadow-md"
                                                    :class="getRankBadgeClass(spot.rank)">
                                                    <template x-if="spot.rank === 1">
                                                        <i class="bi bi-trophy-fill"></i>
                                                    </template>
                                                    <template x-if="spot.rank === 2">
                                                        <i class="bi bi-award-fill"></i>
                                                    </template>
                                                    <template x-if="spot.rank === 3">
                                                        <i class="bi bi-award"></i>
                                                    </template>
                                                    <span class="font-bold text-lg" x-text="'#' + spot.rank"></span>
                                                </div>
                                                <div>
                                                    <h3 class="text-xl font-semibold text-gray-900" x-text="spot.name">
                                                    </h3>
                                                    <div class="flex items-center gap-4 mt-1">
                                                        <div class="flex items-center gap-1 text-gray-600">
                                                            <i class="bi bi-geo-alt"></i>
                                                            <span class="text-sm" x-text="spot.location"></span>
                                                        </div>
                                                        <div class="flex items-center gap-1 text-yellow-500">
                                                            <i class="bi bi-star-fill"></i>
                                                            <span class="text-sm" x-text="spot.rating"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">TOPSIS Score</p>
                                                <p class="text-lg text-blue-600 font-semibold"
                                                    x-text="formatScore(spot.topsisScore)"></p>
                                            </div>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-4" x-text="spot.description"></p>

                                        <div
                                            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-sm">
                                            <template x-for="criterion in spot.criteriaValues" :key="criterion.id">
                                                <div class="px-3 py-2 rounded border-l-2"
                                                    :class="[criterion.theme.bg, criterion.theme.border]">
                                                    <p class="text-xs" :class="criterion.theme.text"
                                                        x-text="criterion.name"></p>
                                                    <p class="text-gray-900 font-semibold"
                                                        x-text="formatValue(criterion.value, criterion.suffix)"></p>
                                                </div>
                                            </template>
                                        </div>

                                        <template x-if="spot.review_links && spot.review_links.length > 0">
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <p
                                                    class="text-xs text-gray-500 font-semibold uppercase tracking-wide mb-2">
                                                    Read Reviews &amp; Book</p>
                                                <div class="flex flex-wrap gap-2">
                                                    <template x-for="(link, index) in spot.review_links"
                                                        :key="index">
                                                        <a :href="link.url" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-full hover:bg-indigo-100 transition-colors no-underline hover:no-underline">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                            <span x-text="link.name"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                                            <button type="button" @click="saveFavorite(spot.id)" :disabled="isSaving"
                                                :class="selectedFavorite === spot.id ?
                                                    'bg-green-600 text-white border-green-600 hover:bg-green-700' :
                                                    'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500'"
                                                class="inline-flex items-center gap-2 px-4 py-2 border rounded-lg transition-colors font-medium text-sm disabled:opacity-50">

                                                <template x-if="isSaving && selectedFavorite !== spot.id">
                                                    <i class="bi bi-hourglass-split"></i>
                                                </template>

                                                <template x-if="!isSaving || selectedFavorite === spot.id">
                                                    <i class="bi"
                                                        :class="selectedFavorite === spot.id ? 'bi-check-circle-fill' :
                                                            'bi-heart'"></i>
                                                </template>

                                                <span
                                                    x-text="selectedFavorite === spot.id ? 'Selected as Favorite' : 'Mark as Favorite'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Debug Data Box -->
            <div class="mt-12 bg-gray-900 rounded-lg p-6 shadow-lg text-left overflow-x-auto">
                <h3 class="text-white font-mono text-lg mb-4 border-b border-gray-700 pb-2 flex items-center gap-2">
                    <i class="bi bi-bug-fill text-green-400"></i> TOPSIS Full Debug Data Log
                </h3>

                <div class="space-y-6 font-mono text-xs text-green-400">
                    <div>
                        <h4 class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                            Step 1: Raw Decision Matrix (Values)
                        </h4>
                        <pre class="bg-black p-3 rounded rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['decisionMatrix'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div>
                        <h4 class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                            Step 2: Normalized Decision Matrix
                        </h4>
                        <pre class="bg-black p-3 rounded rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['normalizedMatrix'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div>
                        <h4 class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                            Step 3: Normalized User Weights
                        </h4>
                        <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['normalizedWeights'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div>
                        <h4 class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                            Step 4: Weighted Normalized Decision Matrix
                        </h4>
                        <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['weightedMatrix'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4
                                class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                                Step 5a: Positive Ideal Solution (A*)
                            </h4>
                            <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['idealBest'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <h4
                                class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                                Step 5b: Negative Ideal Solution (A-)
                            </h4>
                            <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['idealWorst'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                            Step 6: Separation Measures (Distance to Ideal Solutions)
                        </h4>
                        <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['separations'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4
                                class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                                Step 7: Relative Closeness to Ideal Solution
                            </h4>
                            <pre class="bg-black p-3 rounded border border-gray-800 overflow-x-auto">{{ json_encode($debug['relativeCloseness'] ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                        <div>
                            <h4
                                class="text-white mb-2 font-bold uppercase tracking-wider text-[11px] bg-gray-800 p-2 rounded">
                                Step 8: Final Ranked Results
                            </h4>
                            <pre class="bg-black p-3 rounded border border-gray-800 max-h-[400px] overflow-y-auto">{{ json_encode($results ?? [], JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="clear-method-modal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-white rounded-xl shadow-xl border border-gray-200">
            <div class="p-5 border-b border-gray-100">
                <h4 class="text-lg font-semibold text-gray-900">Clear Saved Result?</h4>
                <p class="text-sm text-gray-600 mt-1" id="clear-method-modal-message">
                    This will clear the selected method result and allow you to redo it.
                </p>
            </div>
            <div class="p-5 flex items-center justify-end gap-3">
                <button type="button" id="clear-method-cancel"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="button" id="clear-method-confirm"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                    Yes, Clear Result
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>
        <script>
            function rankingData() {
                return {
                    rankedSpots: @json($rankedSpots),
                    locations: @json($locations),
                    selectedLocation: 'All Locations',
                    showFilters: true,
                    showMethods: true,
                    selectedFavorite: {{ $selectedFavorite ?? 'null' }},
                    isSaving: false,
                    currentMethod: '{{ $currentMethodCode ?? '' }}',

                    init() {
                        if (!this.locations.includes(this.selectedLocation)) {
                            this.selectedLocation = this.locations[0] || 'All Locations';
                        }
                    },

                    async saveFavorite(spotId) {
                        if (this.isSaving) return;

                        this.isSaving = true;

                        try {
                            const response = await fetch('{{ route('recommendations.save_favorite') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    tourist_spot_id: spotId,
                                    method_code: this.currentMethod
                                })
                            });

                            if (response.ok) {
                                this.selectedFavorite = spotId;
                            } else {
                                console.error('Failed to save selection.');
                            }
                        } catch (error) {
                            console.error('Error saving favorite:', error);
                        } finally {
                            this.isSaving = false;
                        }
                    },

                    get filteredSpots() {
                        if (this.selectedLocation === 'All Locations') {
                            return this.rankedSpots;
                        }
                        return this.rankedSpots.filter(spot => spot.location === this.selectedLocation);
                    },

                    getLocationCount(location) {
                        if (location === 'All Locations') {
                            return `(${this.rankedSpots.length})`;
                        }
                        return `(${this.rankedSpots.filter(spot => spot.location === location).length})`;
                    },

                    formatScore(score) {
                        return typeof score === 'number' ? score.toFixed(3) : 'N/A';
                    },

                    formatValue(value, suffix) {
                        if (value === null || value === undefined || value === '') {
                            return 'N/A';
                        }
                        return `${value}${suffix}`;
                    },

                    getRankBadgeClass(rank) {
                        if (rank === 1) return 'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white';
                        if (rank === 2) return 'bg-gradient-to-r from-gray-300 to-gray-400 text-white';
                        if (rank === 3) return 'bg-gradient-to-r from-amber-500 to-amber-600 text-white';
                        return 'bg-gradient-to-r from-blue-500 to-blue-600 text-white';
                    }
                };
            }

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('clear-method-modal');
                const messageEl = document.getElementById('clear-method-modal-message');
                const cancelBtn = document.getElementById('clear-method-cancel');
                const confirmBtn = document.getElementById('clear-method-confirm');
                let pendingFormId = null;

                function openModal(methodName, formId) {
                    pendingFormId = formId;
                    messageEl.textContent = 'This will clear the saved result for ' + methodName +
                        ' and allow you to redo it.';
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }

                function closeModal() {
                    pendingFormId = null;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                document.querySelectorAll('.clear-method-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const formId = btn.getAttribute('data-clear-form');
                        const methodName = btn.getAttribute('data-method-name') || 'this method';
                        openModal(methodName, formId);
                    });
                });

                cancelBtn.addEventListener('click', closeModal);

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        closeModal();
                    }
                });

                confirmBtn.addEventListener('click', function() {
                    if (!pendingFormId) {
                        return;
                    }

                    const form = document.getElementById(pendingFormId);
                    if (form) {
                        form.submit();
                    }
                });
            });
        </script>
    @endpush

@endsection
