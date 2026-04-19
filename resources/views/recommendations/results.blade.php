@extends('layouts.user')

@section('title', 'Your Personalized Recommendations')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            prefix: 'tw-',
            important: '#tw-root',
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
            ['bg' => 'tw-bg-purple-50', 'border' => 'tw-border-purple-500', 'text' => 'tw-text-purple-600'],
            ['bg' => 'tw-bg-blue-50', 'border' => 'tw-border-blue-500', 'text' => 'tw-text-blue-600'],
            ['bg' => 'tw-bg-green-50', 'border' => 'tw-border-green-500', 'text' => 'tw-text-green-600'],
            ['bg' => 'tw-bg-orange-50', 'border' => 'tw-border-orange-500', 'text' => 'tw-text-orange-600'],
            ['bg' => 'tw-bg-pink-50', 'border' => 'tw-border-pink-500', 'text' => 'tw-text-pink-600'],
            ['bg' => 'tw-bg-cyan-50', 'border' => 'tw-border-cyan-500', 'text' => 'tw-text-cyan-600'],
            ['bg' => 'tw-bg-amber-50', 'border' => 'tw-border-amber-500', 'text' => 'tw-text-amber-600'],
            ['bg' => 'tw-bg-indigo-50', 'border' => 'tw-border-indigo-500', 'text' => 'tw-text-indigo-600'],
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

    <div id="tw-root" x-data="rankingData()" x-init="init()" class="tw-min-h-screen tw-bg-gray-50">
        <div class="tw-max-w-7xl tw-mx-auto tw-px-6 tw-py-8">
            <div class="tw-mb-8">
                <h2 class="tw-text-3xl tw-mb-2 tw-text-gray-900 tw-font-semibold">Ranked Tourist Spots</h2>
                <p class="tw-text-gray-600">Based on your preferences, here are the top recommended ecotourism destinations</p>
            </div>

            <!-- Notification Banner -->
            <div x-show="selectedFavorite === null" x-transition.duration.500ms
                class="tw-mb-6 tw-bg-blue-50 tw-border-l-4 tw-border-blue-500 tw-rounded-r-lg tw-p-4 tw-shadow-sm tw-flex tw-items-start tw-gap-3">
                <i class="bi bi-info-circle-fill tw-text-blue-500 tw-text-xl tw-mt-0.5"></i>
                <div class="tw-flex-1">
                    <h3 class="tw-text-sm tw-font-semibold tw-text-blue-900">Don't forget to pick a favorite!</h3>
                    <p class="tw-text-sm tw-text-blue-700 tw-mt-1">Review the results below and click <b>"Mark as Favorite"</b> on
                        the spot you'd most like to visit. Your choice will be saved with this recommendation result.</p>
                </div>
            </div>

            <div class="tw-grid tw-grid-cols-1 lg:tw-grid-cols-4 tw-gap-6">
                <div class="lg:tw-col-span-1">
                    <div class="tw-sticky tw-top-6 tw-space-y-6">
                        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-border tw-border-gray-200 tw-p-6">
                            <button type="button"
                                class="tw-w-full tw-flex tw-items-center tw-justify-between tw-text-left tw-border-none tw-outline-none tw-p-0 focus:tw-outline-none"
                                @click="showFilters = !showFilters">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <i class="bi bi-funnel tw-text-blue-600"></i>
                                    <h3 class="tw-text-lg tw-font-semibold tw-text-gray-900">Filter by Location</h3>
                                </div>
                                <i class="bi" :class="showFilters ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            </button>

                            <div class="tw-space-y-2 tw-mt-4" x-show="showFilters" x-transition>
                                <template x-for="location in locations" :key="location">
                                    <label
                                        class="tw-flex tw-items-center tw-gap-3 tw-p-2 tw-rounded hover:tw-bg-gray-50 tw-cursor-pointer tw-transition-colors">
                                        <input type="radio" name="location" :value="location"
                                            x-model="selectedLocation" class="tw-w-4 tw-h-4 tw-text-blue-600 focus:tw-ring-blue-500">
                                        <span class="tw-text-sm tw-text-gray-700" x-text="location"></span>
                                        <span class="tw-ml-auto tw-text-xs tw-text-gray-500"
                                            x-text="getLocationCount(location)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <div class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-border tw-border-gray-200 tw-p-6">
                            <div class="tw-flex tw-items-center tw-gap-2 tw-mb-4">
                                <i class="bi bi-bar-chart-line tw-text-emerald-600"></i>
                                <h3 class="tw-text-base tw-font-semibold tw-text-gray-900">Compare Methods</h3>
                            </div>

                            <div class="tw-space-y-2 tw-mb-4">
                                @foreach ($methodStatuses ?? [] as $code => $status)
                                    <div class="tw-flex tw-items-center tw-justify-between tw-text-sm">
                                        <span class="tw-text-gray-700">{{ $status['name'] }}</span>
                                        @if ($status['has_run'])
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-semibold tw-bg-green-100 tw-text-green-700">Ready</span>
                                        @else
                                            <span
                                                class="tw-px-2 tw-py-0.5 tw-rounded-full tw-text-xs tw-font-semibold tw-bg-gray-100 tw-text-gray-600">Not
                                                Run</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ route('recommendations.compare') }}"
                                class="tw-block tw-text-center tw-w-full tw-px-4 tw-py-2 tw-bg-emerald-600 tw-text-white tw-rounded-lg hover:tw-bg-emerald-700 tw-transition-colors tw-no-underline hover:tw-no-underline focus:tw-no-underline">
                                Open Comparison
                            </a>
                        </div>

                        <div class="tw-bg-gradient-to-br tw-from-indigo-500 tw-to-purple-600 tw-rounded-xl tw-p-1 tw-shadow-lg">
                            <div class="tw-bg-white tw-rounded-lg tw-p-5">
                                <button type="button"
                                    class="tw-w-full tw-flex tw-items-center tw-justify-between tw-text-left tw-border-none tw-outline-none tw-p-0 focus:tw-outline-none"
                                    @click="showMethods = !showMethods">
                                    <div class="tw-flex tw-items-center tw-gap-2">
                                        <i class="bi bi-arrow-repeat tw-text-indigo-600"></i>
                                        <h3 class="tw-text-base tw-font-semibold tw-text-gray-900">Try Another Method</h3>
                                    </div>
                                    <i class="bi" :class="showMethods ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                </button>

                                <div class="tw-mt-3 tw-space-y-2" x-show="showMethods" x-transition>
                                    @foreach ($methodStatuses ?? [] as $methodCode => $status)
                                        @php
                                            $isCompleted = (bool) ($status['has_run'] ?? false);
                                            $isCurrentMethod = ($currentMethodCode ?? '') === $methodCode;
                                        @endphp

                                        @if (!$isCompleted)
                                            <a href="{{ $methodRouteMap[$methodCode] ?? '#' }}"
                                                class="tw-block tw-w-full tw-px-4 tw-py-2 tw-bg-indigo-600 tw-text-white tw-rounded-lg hover:tw-bg-indigo-700 tw-transition-colors tw-no-underline hover:tw-no-underline focus:tw-no-underline">
                                                {{ $status['name'] }}
                                            </a>
                                        @else
                                            <div
                                                class="tw-w-full tw-px-4 tw-py-2 tw-bg-gray-100 tw-text-gray-500 tw-rounded-lg tw-cursor-not-allowed tw-flex tw-items-center tw-justify-between">
                                                <span>{{ $status['name'] }}</span>
                                                <span class="tw-text-xs tw-font-semibold">Completed</span>
                                            </div>

                                            <form id="clear-form-{{ $methodCode }}" method="POST"
                                                action="{{ route('recommendations.clear_method') }}">
                                                @csrf
                                                <input type="hidden" name="method_code" value="{{ $methodCode }}">
                                                <input type="hidden" name="criteria_signature"
                                                    value="{{ $criteriaSignature ?? '' }}">
                                                <button type="button" data-clear-form="clear-form-{{ $methodCode }}"
                                                    data-method-name="{{ $status['name'] }}"
                                                    class="clear-method-btn tw-w-full tw-px-4 tw-py-2 tw-bg-white tw-border tw-border-indigo-300 tw-text-indigo-700 tw-rounded-lg hover:tw-bg-indigo-50 tw-transition-colors">
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

                <div class="lg:tw-col-span-3">
                    <template x-if="filteredSpots.length === 0">
                        <div class="tw-bg-white tw-rounded-lg tw-p-12 tw-text-center">
                            <i class="bi bi-funnel tw-text-gray-300 tw-text-4xl"></i>
                            <p class="tw-text-gray-500 tw-mt-4">No tourist spots found for the selected location.</p>
                            <button @click="selectedLocation = 'All Locations'"
                                class="tw-mt-4 tw-px-4 tw-py-2 tw-bg-blue-600 tw-text-white tw-rounded-lg hover:tw-bg-blue-700 tw-transition-colors">
                                View All Locations
                            </button>
                        </div>
                    </template>

                    <div class="tw-space-y-4" x-show="filteredSpots.length > 0">
                        <template x-for="spot in filteredSpots" :key="spot.id">
                            <div
                                class="tw-bg-white tw-rounded-lg tw-shadow-sm tw-border tw-border-gray-200 tw-overflow-hidden hover:tw-shadow-md tw-transition-all">
                                <div class="tw-grid tw-grid-cols-1 md:tw-grid-cols-3 tw-gap-4 tw-p-5">
                                    <div class="md:tw-col-span-1">
                                        <div class="tw-aspect-video md:tw-aspect-square tw-rounded-lg tw-overflow-hidden tw-bg-gray-200">
                                            <img :src="spot.image" :alt="spot.name"
                                                class="tw-w-full tw-h-full tw-object-cover">
                                        </div>
                                    </div>

                                    <div class="md:tw-col-span-2">
                                        <div class="tw-flex tw-items-start tw-justify-between tw-mb-3">
                                            <div class="tw-flex tw-items-center tw-gap-3">
                                                <div class="tw-px-3 tw-py-1.5 tw-rounded-lg tw-flex tw-items-center tw-gap-2 tw-shadow-md"
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
                                                    <span class="tw-font-bold tw-text-lg" x-text="'#' + spot.rank"></span>
                                                </div>
                                                <div>
                                                    <h3 class="tw-text-xl tw-font-semibold tw-text-gray-900" x-text="spot.name">
                                                    </h3>
                                                    <div class="tw-flex tw-items-center tw-gap-4 tw-mt-1">
                                                        <div class="tw-flex tw-items-center tw-gap-1 tw-text-gray-600">
                                                            <i class="bi bi-geo-alt"></i>
                                                            <span class="tw-text-sm" x-text="spot.location"></span>
                                                        </div>
                                                        <div class="tw-flex tw-items-center tw-gap-1 tw-text-yellow-500">
                                                            <i class="bi bi-star-fill"></i>
                                                            <span class="tw-text-sm" x-text="spot.rating"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tw-text-right">
                                                <p class="tw-text-xs tw-text-gray-500">TOPSIS Score</p>
                                                <p class="tw-text-lg tw-text-blue-600 tw-font-semibold"
                                                    x-text="formatScore(spot.topsisScore)"></p>
                                            </div>
                                        </div>

                                        <p class="tw-text-sm tw-text-gray-600 tw-mb-4" x-text="spot.description"></p>

                                        <div
                                            class="tw-grid tw-grid-cols-1 sm:tw-grid-cols-2 md:tw-grid-cols-3 lg:tw-grid-cols-4 tw-gap-3 tw-text-sm">
                                            <template x-for="criterion in spot.criteriaValues" :key="criterion.id">
                                                <div class="tw-px-3 tw-py-2 tw-rounded tw-border-l-2"
                                                    :class="[criterion.theme.bg, criterion.theme.border]">
                                                    <p class="tw-text-xs" :class="criterion.theme.text"
                                                        x-text="criterion.name"></p>
                                                    <p class="tw-text-gray-900 tw-font-semibold"
                                                        x-text="formatValue(criterion.value, criterion.suffix)"></p>
                                                </div>
                                            </template>
                                        </div>

                                        <template x-if="spot.review_links && spot.review_links.length > 0">
                                            <div class="tw-mt-4 tw-pt-4 tw-border-t tw-border-gray-100">
                                                <p
                                                    class="tw-text-xs tw-text-gray-500 tw-font-semibold tw-uppercase tw-tracking-wide tw-mb-2">
                                                    Read Reviews &amp; Book</p>
                                                <div class="tw-flex tw-flex-wrap tw-gap-2">
                                                    <template x-for="(link, index) in spot.review_links"
                                                        :key="index">
                                                        <a :href="link.url" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="tw-inline-flex tw-items-center tw-gap-1.5 tw-px-3 tw-py-1.5 tw-text-xs tw-font-medium tw-text-indigo-600 tw-bg-indigo-50 tw-border tw-border-indigo-200 tw-rounded-full hover:tw-bg-indigo-100 tw-transition-colors tw-no-underline hover:tw-no-underline">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                            <span x-text="link.name"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <div class="tw-mt-4 tw-pt-4 tw-border-t tw-border-gray-100 tw-flex tw-justify-end">
                                            <button type="button" @click="saveFavorite(spot.id)" :disabled="isSaving"
                                                :class="selectedFavorite === spot.id ?
                                                    'tw-bg-green-600 tw-text-white tw-border-green-600 hover:tw-bg-green-700' :
                                                    'tw-bg-white tw-text-gray-700 tw-border-gray-300 hover:tw-bg-gray-50 focus:tw-ring-2 focus:tw-ring-blue-500'"
                                                class="tw-inline-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-border tw-rounded-lg tw-transition-colors tw-font-medium tw-text-sm disabled:tw-opacity-50">

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

        </div>

        <div id="clear-method-modal" class="tw-fixed tw-inset-0 tw-bg-black/40 tw-z-50 tw-hidden tw-items-center tw-justify-center tw-p-4">
            <div class="tw-w-full tw-max-w-md tw-bg-white tw-rounded-xl tw-shadow-xl tw-border tw-border-gray-200">
                <div class="tw-p-5 tw-border-b tw-border-gray-100">
                    <h4 class="tw-text-lg tw-font-semibold tw-text-gray-900">Clear Saved Result?</h4>
                    <p class="tw-text-sm tw-text-gray-600 tw-mt-1" id="clear-method-modal-message">
                    This will clear the selected method result and allow you to redo it.
                    </p>
                </div>
                <div class="tw-p-5 tw-flex tw-items-center tw-justify-end tw-gap-3">
                    <button type="button" id="clear-method-cancel"
                        class="tw-px-4 tw-py-2 tw-rounded-lg tw-border tw-border-gray-300 tw-text-gray-700 hover:tw-bg-gray-50">
                        Cancel
                    </button>
                    <button type="button" id="clear-method-confirm"
                        class="tw-px-4 tw-py-2 tw-rounded-lg tw-bg-red-600 tw-text-white hover:tw-bg-red-700">
                        Yes, Clear Result
                    </button>
                </div>
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
                        if (rank === 1) return 'tw-bg-gradient-to-r tw-from-yellow-400 tw-to-yellow-500 tw-text-white';
                        if (rank === 2) return 'tw-bg-gradient-to-r tw-from-gray-300 tw-to-gray-400 tw-text-white';
                        if (rank === 3) return 'tw-bg-gradient-to-r tw-from-amber-500 tw-to-amber-600 tw-text-white';
                        return 'tw-bg-gradient-to-r tw-from-blue-500 tw-to-blue-600 tw-text-white';
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
                    modal.classList.remove('tw-hidden');
                    modal.classList.add('tw-flex');
                }

                function closeModal() {
                    pendingFormId = null;
                    modal.classList.add('tw-hidden');
                    modal.classList.remove('tw-flex');
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
