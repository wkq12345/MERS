@extends('layouts.user')

@section('content')
    {{-- Alpine.js Component --}}
    <div x-data="{
        search: '',
        selectedLocations: [],
        minRating: 0,
        spots: {{ json_encode($touristSpots) }},
        selectedSpot: null,
        showModal: false,

        get filteredSpots() {
            return this.spots.filter(spot => {
                const matchesSearch = spot.name.toLowerCase().includes(this.search.toLowerCase());
                const matchesLocation = this.selectedLocations.length === 0 || this.selectedLocations.includes(spot.location);
                const matchesRating = spot.rating >= this.minRating;
                return matchesSearch && matchesLocation && matchesRating;
            });
        },

        openModal(spot) {
            this.selectedSpot = {
                ...spot,
                review_links: this.parseReviewLinks(spot.review_link)
            };
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            this.selectedSpot = null;
            document.body.style.overflow = 'auto';
        },

        getImageUrl(image) {
            if (!image) return 'https://placehold.co/600x400?text=No+Image';
            if (image.startsWith('http') || image.startsWith('data:')) return image;
            return '/storage/' + image;
        },

        parseReviewLinks(rawLinks) {
            if (!rawLinks || typeof rawLinks !== 'string') return [];

            return rawLinks
                .split('|||||')
                .map(link => link.trim())
                .filter(link => link.length > 0)
                .map(link => ({
                    url: link,
                    name: this.getProviderName(link)
                }));
        },

        getProviderName(url) {
            const lower = url.toLowerCase();

            if (lower.includes('tripadvisor.')) return 'Tripadvisor';
            if (lower.includes('klook.')) return 'Klook';
            if (lower.includes('trip.com')) return 'Trip.com';

            try {
                const hostname = new URL(url).hostname.replace('www.', '');
                return hostname || 'Open Link';
            } catch (_) {
                return 'Open Link';
            }
        }
    }" class="container-fluid py-4">

        <div class="row g-4">

            {{-- Sidebar Filters --}}
            <div class="col-lg-3">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px; z-index: 100;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 fw-bold"><i class="bi bi-sliders me-2"></i>Filters</h5>
                            <button @click="search = ''; selectedLocations = []; minRating = 0;"
                                class="btn btn-sm btn-outline-secondary"
                                x-show="search || selectedLocations.length > 0 || minRating > 0" x-transition>
                                Reset
                            </button>
                        </div>

                        <hr>

                        {{-- Location Filter --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Location</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($locations as $loc)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $loc }}"
                                            id="loc-{{ $loop->index }}" x-model="selectedLocations">
                                        <label class="form-check-label" for="loc-{{ $loop->index }}">
                                            {{ $loc }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        {{-- Rating Filter --}}
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3">Minimum Rating</h6>
                            <div class="d-flex flex-column gap-2">
                                <label class="form-check-label d-flex align-items-center" style="cursor: pointer">
                                    <input type="radio" class="form-check-input me-2" name="rating"
                                        :value="0" x-model.number="minRating">
                                    <span>Any</span>
                                </label>
                                <label class="form-check-label d-flex align-items-center" style="cursor: pointer">
                                    <input type="radio" class="form-check-input me-2" name="rating"
                                        :value="4" x-model.number="minRating">
                                    <span>4.0+</span>
                                    <i class="bi bi-star-fill text-warning ms-1" style="font-size: 0.8rem"></i>
                                </label>
                                <label class="form-check-label d-flex align-items-center" style="cursor: pointer">
                                    <input type="radio" class="form-check-input me-2" name="rating"
                                        :value="4.5" x-model.number="minRating">
                                    <span>4.5+</span>
                                    <i class="bi bi-star-fill text-warning ms-1" style="font-size: 0.8rem"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9">

                {{-- Search Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 fw-bold mb-0">Explore Tourist Spots</h2>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" placeholder="Search places..."
                            x-model="search">
                    </div>
                </div>

                {{-- Empty State --}}
                <div x-show="filteredSpots.length === 0" class="text-center py-5" x-cloak>
                    <div class="mb-3">
                        <i class="bi bi-geo-alt display-1 text-muted opacity-25"></i>
                    </div>
                    <h4 class="text-muted">No spots found</h4>
                    <p class="text-muted">Try adjusting your filters or search.</p>
                </div>

                {{-- Grid --}}
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <template x-for="spot in filteredSpots" :key="spot.id">
                        <div class="col">
                            <div class="card h-100 shadow-sm border-0 hover-lift transition-all" style="cursor: pointer;"
                                @click="openModal(spot)">
                                <div class="position-relative">
                                    <img :src="getImageUrl(spot.image)" class="card-img-top object-fit-cover"
                                        :alt="spot.name" style="height: 200px;">
                                    <!-- Number of reviews might not be accurately count, but we keep it or change it -->
                                    <span
                                        class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 rounded-pill">
                                        <i class="bi bi-star-fill text-warning me-1"></i>
                                        <span x-text="spot.rating.toFixed(1)"></span>
                                    </span>
                                    <!-- Use PHP ternary logic or a default for category since DB might not have it -->
                                    <span class="position-absolute bottom-0 start-0 m-2 badge bg-primary"
                                        x-text="spot.category || 'General'"></span>
                                </div>

                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title fw-bold mb-0 text-truncate" x-text="spot.name"></h5>
                                    </div>

                                    <div class="mb-3 text-muted small">
                                        <i class="bi bi-geo-alt-fill me-1 text-danger"></i>
                                        <span x-text="spot.location"></span>
                                    </div>

                                    <p class="card-text text-muted small line-clamp-2" x-text="spot.description"
                                        style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    </p>
                                </div>

                                <div class="card-footer bg-white border-0 pt-0 pb-3">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            Explore
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Modal for Tourist Spot Details --}}
        <div x-show="showModal" x-cloak @click.self="closeModal()" class="modal show" tabindex="-1"
            style="background-color: rgba(0,0,0,0.5);" :class="{ 'd-block': showModal }"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" @click.stop
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="transform scale-95 opacity-0"
                    x-transition:enter-end="transform scale-100 opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="transform scale-100 opacity-100"
                    x-transition:leave-end="transform scale-95 opacity-0">
                    <template x-if="selectedSpot">
                        <div>
                            <div class="modal-header border-0 pb-0">
                                <h4 class="modal-title fw-bold" x-text="selectedSpot.name"></h4>
                                <button type="button" class="btn-close" @click="closeModal()"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <img :src="getImageUrl(selectedSpot.image)" class="w-100 rounded object-fit-cover"
                                            :alt="selectedSpot.name" style="max-height: 400px;">
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex flex-wrap gap-3 mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                                <span class="text-muted" x-text="selectedSpot.location"></span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                <span class="fw-semibold" x-text="selectedSpot.rating.toFixed(1)"></span>
                                                <span class="text-muted ms-1"
                                                    x-text="selectedSpot.total_reviews > 0 ? '(' + selectedSpot.total_reviews.toLocaleString() + ' reviews)' : '(No reviews yet)'"></span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary" x-text="selectedSpot.category"></span>
                                            </div>
                                        </div>

                                        <h5 class="fw-semibold mb-3">Description</h5>
                                        <p class="text-muted mb-4"
                                            x-text="selectedSpot.description || 'No description available'"></p>

                                        <!-- Detailed Criteria Ratings -->
                                        <template
                                            x-if="selectedSpot.criteria_ratings && selectedSpot.criteria_ratings.length > 0">
                                            <div class="mb-4">
                                                <h5 class="fw-semibold mb-3">Detailed Ratings</h5>
                                                <div class="row g-2">
                                                    <template x-for="rating in selectedSpot.criteria_ratings"
                                                        :key="rating.name">
                                                        <div class="col-md-6 col-lg-4">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                                <span class="text-muted small"
                                                                    x-text="rating.name"></span>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fw-semibold me-1"
                                                                        x-text="rating.score"></span>
                                                                    <i class="bi bi-star-fill text-warning"
                                                                        style="font-size: 0.8rem;"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <template x-if="selectedSpot.review_links && selectedSpot.review_links.length > 0">
                                            <div>
                                                <h5 class="fw-semibold mb-3">Read Reviews & Book</h5>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <template x-for="(link, index) in selectedSpot.review_links"
                                                        :key="index">
                                                        <a :href="link.url" target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                                                            <i class="bi bi-box-arrow-up-right"></i>
                                                            <span x-text="link.name"></span>
                                                        </a>
                                                    </template>
                                                </div>
                                                <p class="small text-muted mt-2 mb-0">
                                                    <i class="bi bi-info-circle me-1"></i>
                                                    Links will open in a new tab
                                                </p>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-secondary" @click="closeModal()">Close</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="//unpkg.com/alpinejs" defer></script>
        <style>
            [x-cloak] {
                display: none !important;
            }

            .hover-lift:hover {
                transform: translateY(-5px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }

            .transition-all {
                transition: all 0.3s ease;
            }
        </style>
    @endpush
@endsection
