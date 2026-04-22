
                <span class="navbar-toggler-icon"></span>
            </button>


                @auth
                    <div class="dropdown ms-lg-2">
                        <button class="btn btn-light btn-sm user-dropdown-btn dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <span>{{ Auth::user()->name ?? 'User' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.show') }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item d-flex align-items-center">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="d-flex ms-lg-2">
                        <a class="btn btn-light btn-sm" href="{{ route('login') }}">Login</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

        </div>
    </main>
    @include('layouts.partials.footer')
    @include('User.user_demographic')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

