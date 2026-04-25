@extends('layouts.user')

@section('title', 'Dashboard - MERS')

@push('styles')
    <style>
        /* Custom Gradient Background for Left Panel */
        .gradient-panel {
            background: linear-gradient(to right, #2563eb, #3b82f6, #22d3ee);
            /* blue-600 via blue-500 to cyan-400 */
            border-radius: 0.5rem;
            /* rounded-lg */
            padding: 2rem;
            color: white;
            height: 100%;
        }

        /* Hoverable Method Button Container */
        .method-item {
            position: relative;
            margin-bottom: 1rem;
        }

        /* The Button Itself */
        .method-btn {
            width: 100%;
            background: white;
            color: #1f2937;
            /* gray-800 */
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-align: left;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .method-btn:hover {
            background-color: #f9fafb;
            /* gray-50 */
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .method-icon {
            color: #2563eb;
            /* blue-600 */
            font-size: 1.25rem;
        }

        /* The Tooltip/Explanation Box (Hidden by default) */
        .method-explanation {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 0.5rem;
            background: white;
            color: #1f2937;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            z-index: 50;
            font-size: 0.875rem;
            line-height: 1.5;
            animation: fadeIn 0.2s ease-in-out;
        }

        /* Show Explanation on Hover */
        .method-item:hover .method-explanation {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Right Side Cards */
        .action-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            transition: box-shadow 0.2s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .action-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .icon-circle {
            width: 4rem;
            height: 4rem;
            background-color: #dbeafe;
            /* blue-100 */
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: #2563eb;
            /* blue-600 */
            font-size: 1.5rem;
        }
    </style>
@endpush


@section('content')
    @include('User.user_demographic')


    <div class="row g-4">
        <!-- Left Side - Personalized Recommendations -->
        <div class="col-12 col-lg-7">
            <div class="gradient-panel">
                <div class="mb-4">
                    <h3 class="h4 fw-bold mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-stars"></i>
                        Pick one method that you like
                    </h3>
                    <p class="opacity-75 small">
                            This is a research used system to evaluate
                            different weightage method based on TOPSIS algorithm to
                            recommend the best ecotourism spots.
                    </p>
                </div>

                <div class="d-flex flex-column gap-3">
                    <!-- Method 1 -->
                    <div class="method-item">
                        <a href="{{ route('recommendations.drm') }}" class="method-btn">
                            <i class="bi bi-stars method-icon"></i>
                            <div>
                                <div class="fw-semibold">Direct Rating Method</div>
                                <div class="small text-secondary">Start from rating scale based on your preferences</div>
                            </div>
                        </a>
                        <div class="method-explanation">
                                A direct rating method is a technique used in decision-making and assessment to assign numerical
                                or categorical values (e.g., 1-10 scale) directly to items or criteria based on their perceived
                                importance, quality, or value. It is a simple, quick approach for weighting factors or measuring
                                subjective opinions in marketing, psychology, and performance evaluations.
                        </div>
                    </div>

                    <!-- Method 2 -->
                    <div class="method-item">
                        <a href="{{ route('recommendations.hdm') }}" class="method-btn">
                            <i class="bi bi-graph-up-arrow method-icon"></i>
                            <div>
                                <div class="fw-semibold">Hundred Dollar Method</div>
                                <div class="small text-secondary">Rate by assuming your budget is $100</div>
                            </div>
                        </a>
                        <div class="method-explanation">
                            The hundred dollar method (or 100-point method) is a simple, democratic prioritization
                            technique where stakeholders are given 100 "dollars" or points to distribute across various options,
                            features, or tasks. It forces participants to make trade-offs, highlighting top priorities by showing
                            where they invest their limited budget.
                        </div>
                    </div>

                    <!-- Method 3 -->
                    <div class="method-item">
                        <a href="{{ route('recommendations.kano') }}" class="method-btn">
                            <i class="bi bi-heart-fill method-icon"></i>
                            <div>
                                <div class="fw-semibold">Kano Model</div>
                                <div class="small text-secondary">Choose this when you deeply understand your needs</div>
                            </div>
                        </a>
                        <div class="method-explanation">
                            The Kano Model* is a way to classify customer preferences by exploring how customers react to
                            certain product capabilities or features. It can help Scrum Teams priortize experiments and features
                            based on how likely they are to satisfy customers.
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap gap-2">
                    <a href="{{ route('recommendations.compare') }}"
                        class="btn btn-light fw-semibold px-4 py-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-bar-chart-line"></i>
                        Compare Methods
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side - Tourist Spots, Locations, Profile -->
        <div class="col-12 col-lg-5">
            <div class="d-flex flex-column gap-4">
                <!-- Tourist Spots -->
                <div class="action-card">
                    <div class="icon-circle">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tourist Spots</h5>
                    <p class="text-secondary small mb-3">
                        Browse and explore ecotourism destinations across Malaysia
                    </p>
                    <a href="{{ route('viewTouristSpot') }}" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-geo-alt"></i> View Spots
                    </a>
                </div>

                <!-- Locations -->
                <div class="action-card">
                    <div class="icon-circle">
                        <i class="bi bi-map-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Locations</h5>
                    <p class="text-secondary small mb-3">
                        Discover different states and regions in Malaysia
                    </p>
                    <a href="{{ route('viewLocation') }}" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                        <i class="bi bi-map"></i> View Locations
                    </a>
                </div>


            </div>
        </div>
    </div>



@endsection
