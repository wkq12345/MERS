@extends('layouts.welcome')

@section('title', 'Welcome to MERS')

@section('fullpage', true)


@section('content')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

<!-- Snap Section 1: snap1-->
<section class="snap-section" id="snap1">
    <!-- Full-Width Image Carousel/Slider -->
    <div id="ecotourismCarousel" class="carousel slide snap1" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#ecotourismCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1">
            <i class="bi bi-circle-fill"></i>
        </button>
        <button type="button" data-bs-target="#ecotourismCarousel" data-bs-slide-to="1" aria-label="Slide 2">
            <i class="bi bi-circle-fill"></i>
        </button>
        <button type="button" data-bs-target="#ecotourismCarousel" data-bs-slide-to="2" aria-label="Slide 3">
            <i class="bi bi-circle-fill"></i>
        </button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="{{ asset('images/Pahang/pulau_tioman.png') }}"
                 class="d-block w-100" alt="Beaches">
            <div class="carousel-caption">
                <h3>Discover Malaysia's Beaches</h3>
                <p>Experience the beauty of Malaysia's pristine beaches and islands</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="{{ asset('images/Terengganu/terengganu state museum.png') }}"
                 class="d-block w-100" alt="Museum">
            <div class="carousel-caption">
                <h3>Explore Malaysia's Museums</h3>
                <p>Discover the rich cultural heritage and history preserved in Malaysia's museums</p>
            </div>
        </div>

        <div class="carousel-item">
            <img src="{{ asset('images/Negeri_Sembilan/ns hiking.png') }}"
                 class="d-block w-100" alt="Hiking Mountain">
            <div class="carousel-caption">
                <h3>Majestic Mountain Ranges</h3>
                <p>Trek through stunning highlands and breathtaking peaks</p>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#ecotourismCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#ecotourismCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

</section>

<!-- Snap Section 2: Welcome -->
<section class="snap-section" id="welcome">
    <div class="welcome-section text-center">
        <div class="container">
            <h1 class="display-2 fw-bold mb-4">Welcome to MERS</h1>
            <p class="lead mb-5 text-muted" style="font-size: 1.5rem; max-width: 800px; margin: 0 auto;">
                Malaysia Ecotourism Recommendation System - Your gateway to discovering and exploring the best sustainable ecotourism destinations across Malaysia.
            </p>

            <div class="d-flex gap-3 justify-content-center flex-wrap mb-5">
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg px-5 py-3 cta-button">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Get Started
                </a>
                <a href="#why-choose-mers" class="btn btn-outline-primary btn-lg px-5 py-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Learn More
                </a>
            </div>

        </div>
    </div>
</section>

<!-- Snap Section 3: Why Choose MERS -->
<section class="snap-section" id="why-choose-mers">
    <!-- Scroll Reveal Section: Why Choose MERS -->
    <div class="scroll-reveal-section">
        <div class="container">
            <div class="scroll-reveal-content">
                <h2 class="text-center mb-5 display-5 fw-bold section-title-reveal" style="background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    Why Choose MERS?
                </h2>

                <div class="row g-4 mb-5">
                    <div class="col-md-4 feature-item">
                        <div class="feature-card text-center">
                            <i class="bi bi-geo-alt-fill text-primary" style="font-size: 3.5rem;"></i>
                            <h5 class="mt-3 mb-3 fw-bold">Discover Locations</h5>
                            <p class="text-muted">Explore amazing ecotourism spots across Malaysia with detailed information and stunning visuals</p>
                        </div>
                    </div>
                    <div class="col-md-4 feature-item">
                        <div class="feature-card text-center">
                            <i class="bi bi-star-fill" style="font-size: 3.5rem; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>
                            <h5 class="mt-3 mb-3 fw-bold">Smart Recommendations</h5>
                            <p class="text-muted">Get personalized suggestions based on your preferences, interests, and travel style</p>
                        </div>
                    </div>
                    <div class="col-md-4 feature-item">
                        <div class="feature-card text-center">
                            <i class="bi bi-tree-fill text-success" style="font-size: 3.5rem;"></i>
                            <h5 class="mt-3 mb-3 fw-bold">Ecotourism Focus</h5>
                            <p class="text-muted">Sustainable travel experiences for nature lovers with minimal environmental impact</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Features -->
                <div class="row g-4 mb-5">
                    <div class="col-md-3 feature-item">
                        <div class="text-center p-3">
                            <i class="bi bi-shield-check text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3 fw-bold">Verified Reviews</h6>
                            <p class="text-muted small">Real experiences from real travelers</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item">
                        <div class="text-center p-3">
                            <i class="bi bi-compass text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3 fw-bold">Easy Navigation</h6>
                            <p class="text-muted small">Detailed maps and directions</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item">
                        <div class="text-center p-3">
                            <i class="bi bi-calendar-event text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3 fw-bold">Trip Planning</h6>
                            <p class="text-muted small">Plan your perfect eco-adventure</p>
                        </div>
                    </div>
                    <div class="col-md-3 feature-item">
                        <div class="text-center p-3">
                            <i class="bi bi-chat-dots text-primary" style="font-size: 2.5rem;"></i>
                            <h6 class="mt-3 fw-bold">24/7 Support</h6>
                            <p class="text-muted small">We're here to help anytime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Auto-play carousel with 5 second intervals
    var myCarousel = document.querySelector('#ecotourismCarousel');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 5000,
        wrap: true,
        pause: 'hover'
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Scroll Reveal Animation for "Why Choose MERS" section
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Reveal main content with 3D turn effect
                const content = entry.target.querySelector('.scroll-reveal-content');
                if (content) {
                    content.classList.add('revealed');
                }

                // Reveal title
                const title = entry.target.querySelector('.section-title-reveal');
                if (title) {
                    title.classList.add('revealed');
                }

                // Reveal feature items one by one
                const features = entry.target.querySelectorAll('.feature-item');
                features.forEach((feature, index) => {
                    setTimeout(() => {
                        feature.classList.add('revealed');
                    }, index * 100);
                });
            }
        });
    }, {
        threshold: 0.2,
        rootMargin: '0px'
    });

    // Observe the scroll reveal section
    const scrollRevealSection = document.querySelector('.scroll-reveal-section');
    if (scrollRevealSection) {
        revealObserver.observe(scrollRevealSection);
    }
</script>
@endpush
