@extends('layouts.user')

@section('title', 'Explore Locations')

@section('content')
	<div x-data="locationExplorer()" class="location-page">
		<section class="mb-4">
			<div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
				<div>
					<h1 class="h2 fw-bold mb-1 d-flex align-items-center gap-2">
						<span class="title-icon"><i class="bi bi-map"></i></span>
						Explore Malaysia
					</h1>
					<p class="text-secondary mb-0">Discover diverse states and regions across the country</p>
				</div>
			</div>

			<div class="stat-card mt-4">
				<div>
					<div class="text-muted small">Total States</div>
					<div class="stat-value">{{ $totalStates }}</div>
				</div>
				<div class="stat-icon-wrap">
					<i class="bi bi-geo-alt"></i>
				</div>
			</div>
		</section>

		<section class="search-panel mb-4">
			<div class="input-group custom-search">
				<span class="input-group-text bg-white border-end-0">
					<i class="bi bi-search text-muted"></i>
				</span>
				<input type="text" class="form-control border-start-0" placeholder="Search locations..." x-model="search">
			</div>
			<div class="small text-muted mt-2">
				Showing <span x-text="filteredLocations.length"></span> of {{ $totalStates }} locations
			</div>
		</section>

		<section>
			<div class="row g-3" x-show="filteredLocations.length > 0" x-cloak>
				<template x-for="location in filteredLocations" :key="location.id">
					<div class="col-12 col-md-6 col-xl-4">
						<article class="card location-card h-100 border-0 shadow-sm">
							<img :src="getImageUrl(location.image)" class="card-img-top location-image" :alt="location.name">

							<div class="card-body d-flex flex-column">
								<h5 class="fw-bold mb-2">
									<a :href="exploreUrl(location.name)" class="location-name-link" x-text="location.name"></a>
								</h5>

								<p class="text-secondary small mb-2">Discover attractions, food, and hidden gems in this location.</p>

								<div class="meta small text-muted mb-3 d-flex align-items-center gap-3 flex-wrap">
									<span><i class="bi bi-geo-alt me-1"></i><span x-text="location.spots_count"></span> spots</span>
									<span><i class="bi bi-people me-1"></i>Popular</span>
								</div>

								<div class="d-flex flex-wrap gap-2 mb-3">
									<template x-for="(spot, i) in location.popular_spots" :key="i">
										<span class="badge rounded-pill text-bg-light border" x-text="spot"></span>
									</template>
									<template x-if="location.spots_count > location.popular_spots.length">
										<span class="badge rounded-pill text-bg-light border" x-text="'+' + (location.spots_count - location.popular_spots.length) + ' more'"></span>
									</template>
								</div>

								<a :href="exploreUrl(location.name)" class="btn btn-primary mt-auto location-btn">
									<span>Explore </span><span x-text="location.name"></span>
									<i class="bi bi-arrow-right ms-1"></i>
								</a>
							</div>
						</article>
					</div>
				</template>
			</div>

			<div class="text-center py-5" x-show="filteredLocations.length === 0" x-cloak>
				<i class="bi bi-search display-5 text-muted"></i>
				<h4 class="mt-3">No matching location found</h4>
				<p class="text-secondary mb-0">Try a different keyword.</p>
			</div>
		</section>
	</div>

	@push('styles')
		<style>
			[x-cloak] {
				display: none !important;
			}

			.location-page {
				padding-bottom: 0.5rem;
			}

			.title-icon {
				width: 34px;
				height: 34px;
				border-radius: 10px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				background: linear-gradient(135deg, #38bdf8 0%, #0d6efd 100%);
				color: #fff;
				font-size: 1rem;
			}

			.stat-card {
				display: flex;
				justify-content: space-between;
				align-items: center;
				max-width: 360px;
				border-radius: 12px;
				border: 2px solid #0d6efd;
				background: #fff;
				padding: 1rem 1.1rem;
				box-shadow: 0 12px 22px rgba(13, 110, 253, 0.18);
			}

			.stat-value {
				font-size: 2rem;
				font-weight: 700;
				line-height: 1;
			}

			.stat-icon-wrap {
				width: 44px;
				height: 44px;
				border-radius: 50%;
				background: #e7f0ff;
				color: #0d6efd;
				display: flex;
				align-items: center;
				justify-content: center;
				font-size: 1.2rem;
			}

			.search-panel {
				background: #f8f9fb;
				border: 1px solid #e2e6ee;
				border-radius: 10px;
				padding: 1rem;
			}

			.custom-search .form-control,
			.custom-search .input-group-text {
				border-color: #d4dae5;
				background: #fff;
			}

			.location-card {
				border-radius: 12px;
				overflow: hidden;
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}

			.location-card:hover {
				transform: translateY(-3px);
				box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12) !important;
			}

			.location-image {
				height: 165px;
				object-fit: cover;
				background: #eaeef5;
			}

			.location-name-link {
				color: #1f2937;
				text-decoration: none;
			}

			.location-name-link:hover {
				color: #0d6efd;
				text-decoration: underline;
			}

			.location-btn {
				border-radius: 8px;
				font-weight: 600;
			}

			@media (max-width: 576px) {
				.stat-card {
					max-width: 100%;
				}
			}
		</style>
	@endpush

	@push('scripts')
		<script src="//unpkg.com/alpinejs" defer></script>
		<script>
			function locationExplorer() {
				return {
					search: '',
					locations: @json($locations),

					get filteredLocations() {
						const term = this.search.toLowerCase().trim();
						if (!term) return this.locations;

						return this.locations.filter((location) => {
							return location.name.toLowerCase().includes(term);
						});
					},

					getImageUrl(image) {
						if (!image) return 'https://placehold.co/800x450?text=No+Image';
						if (image.startsWith('http') || image.startsWith('data:')) return image;
						return '/storage/' + image;
					},

					exploreUrl(locationName) {
						return `{{ route('viewTouristSpot') }}?location=${encodeURIComponent(locationName)}`;
					}
				};
			}
		</script>
	@endpush
@endsection

