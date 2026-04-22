<style>
/* Footer */
        .footer {
            background: black;
            color: rgba(246, 246, 246, 0.7);
            padding: 2rem 0;
            margin-top: 4rem;
        }

        .footer a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }
</style>

 <!-- Footer (hidden in fullpage mode) -->
        @if (!View::hasSection('fullpage'))
            <footer class="footer">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-white mb-3">
                                <i class="bi bi-geo-alt-fill me-2"></i>
                                {{ config('app.name', 'MERS') }}
                            </h5>
                            <p class="mb-0">Malaysia Ecotourism Recommenndation System</p>
                            <small class="text-muted">© {{ date('Y') }} All rights reserved.</small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-white mb-3">Quick Links</h6>
                            <div class="d-flex flex-column flex-md-row justify-content-md-end gap-3">
                                <a href="{{ route('welcome') }}">Home</a>
                                <a href="{{ route('login') }}">Login</a>

                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        @endif
