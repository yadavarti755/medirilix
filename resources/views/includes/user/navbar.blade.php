<nav
    class="navbar navbar-expand-lg navbar-light bg-white navbar-dashboard py-0">
    <div class="container-fluid">
        <button
            class="btn btn-outline-primary me-2"
            id="sidebarToggle"
            aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>

        <div class="d-flex align-items-center ms-auto">
            <div>
                <div class="flex items-center space-x-2 bg-gray-100 px-3 py-1 rounded-lg">
                    <i id="timer-icon" class="fas fa-clock text-success"></i>
                    <span class="text-sm font-medium text-gray-600">Session:</span>
                    <span id="session-timer" class="text-sm font-mono text-success"></span>
                </div>
            </div>
            <div class="dropdown ms-3">
                <button
                    class="btn d-flex align-items-center gap-2"
                    id="userDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="d-none d-md-block text-end">
                        <div class="fw-medium">{{ auth()->user()->name }}</div>
                    </div>
                    <img
                        src="{{ auth()->user()->profile_image_full_path }}"
                        alt="{{ auth()->user()->name }}"
                        class="rounded-circle"
                        width="40"
                        height="40" />
                </button>
                <ul
                    class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 elevation-3"
                    aria-labelledby="userDropdown">
                    <li>
                        <a
                            class="dropdown-item py-2"
                            href="{{ route('homepage') }}">
                            <i class="fas fa-web me-2 text-primary"></i>
                            Visit Website
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>