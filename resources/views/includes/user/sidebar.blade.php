<nav class="sidebar" id="sidebar">
    <div class="w-100 text-center sidebar-brand-wrapper">
        <img
            src="{{ $siteSettings->header_logo_full_path }}"
            alt="{{ $siteSettings->site_name }}"
            class="sidebar-brand-logo" />
    </div>

    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('user.dashboard') }}"
                    class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('user.orders') }}"
                    class="nav-link {{ request()->routeIs('user.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag"></i>
                    All Orders
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('user.wishlist') }}"
                    class="nav-link {{ request()->routeIs('user.wishlist') ? 'active' : '' }}">
                    <i class="fas fa-heart"></i>
                    Wishlist
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('user.profile') }}"
                    class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('logout') }}"
                    class="nav-link">
                    <i class="fas fa-user"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>