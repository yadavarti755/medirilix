{{-- Menu --}}
<nav class="navbar navbar-expand-lg site-menu">
    <div class="container">
        {{-- Custom Mobile Toggle Button --}}
        <button class="navbar-toggler btn-mobile-menu" type="button" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
                <i class="fa-solid fa-bars"></i>
            </span>
        </button>

        {{-- Desktop Menu (Hidden on Mobile via CSS) --}}
        <div class="collapse navbar-collapse collapse-menu-col" id="navbarTogglerDemo02">
            <span id="btn-menu-close" class="d-none">
                <i class="fas fa-times"></i>
            </span>
            <ul class="navbar-nav mb-2 mb-lg-0 menu-ul position-relative w-100">
                <li class="nav-item">
                    <a class="nav-link active text-light" aria-current="page"
                        href="{{ route('homepage') }}">Home</a>
                </li>

                <x-website.mega-menu :categories="$categories" />

                <li class="nav-item">
                    <a class="nav-link text-light" href="{{ route('shop') }}">Shop</a>
                </li>


                @if (Auth::check())
                <li class="nav-item">
                    <a href="{{ (Auth::user()->role_code == 'USER')? route('user.dashboard') : route('my-account') }}"
                        class="nav-link text-light">
                        <i class="fas fa-user"></i> {{Auth::user()->name}} <i class="fa-solid fa-angle-down"></i>
                    </a>
                    <ul class="submenu-ul">
                        @if(Auth::user()->role_code == 'USER')
                        <li>
                            <a href="{{route('user.dashboard')}}">Dashboard</a>
                        </li>
                        @endif
                        <li>
                            <a href="{{route('user.wishlist')}}">Wishlist</a>
                        </li>
                        <li>
                            <a href="{{route('user.orders')}}">My Orders</a>
                        </li>
                        <li>
                            <a href="{{route('user.profile')}}">My Profile</a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}">Logout</a>
                        </li>
                    </ul>
                    @else
                    <a href="javascript:void(0)" class="nav-link text-light" data-bs-toggle="modal" data-bs-target="#modalLoginForm">
                        <i class="far fa-user"></i>
                        Login
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <div class="mobile-header-search-col">
            <div class="header-search-col">
                <div class="header-search-input-group-wrapper" id="header-search-input-group-wrapper">
                    <div class="input-group">
                        <input type="text" class="form-control header-search-input" placeholder="Search Here..."
                            aria-label="Search Here..." aria-describedby="button-addon2" id="header-search-input">
                        <button class="btn btn-outline-secondary header-search-btn" type="button"
                            id="button-addon2"><span class="fas fa-search"></span></button>
                    </div>
                    <ul id="mobile_search_results"></ul>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Mobile Menu Overlay --}}
<div class="mobile-menu-overlay"></div>

{{-- Mobile Menu Drawer --}}
<div class="mobile-menu-drawer" id="mobileMenuDrawer">
    <div class="mobile-menu-header">
        <span class="mobile-menu-title">Menu</span>
        <button class="btn-close-mobile"><i class="fas fa-times"></i></button>
    </div>
    <div class="mobile-menu-body">
        <ul class="mobile-nav-list">
            <li class="mobile-nav-item">
                <a href="{{ route('homepage') }}" class="mobile-nav-link">Home</a>
            </li>
            <li class="mobile-nav-item">
                <a href="{{ route('shop') }}" class="mobile-nav-link">Shop</a>
            </li>

            {{-- Categories Accordion --}}
            @php
            // Filter top level categories if collection exists, otherwise empty
            $mobileCategories = isset($categories) ? $categories->where('parent_id', null) : collect([]);
            @endphp
            @if($mobileCategories->count() > 0)
            <li class="mobile-nav-item">
                <a href="javascript:void(0)" class="mobile-nav-link has-submenu">
                    Categories <i class="fas fa-chevron-down float-end arrow-icon"></i>
                </a>
                <ul class="mobile-submenu">
                    @foreach($mobileCategories as $category)
                    <li><a href="{{ route('shop', ['slug' => $category->slug]) }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </li>
            @endif

            @if (Auth::check())
            <li class="mobile-nav-item">
                <a href="javascript:void(0)" class="mobile-nav-link has-submenu">
                    <i class="fas fa-user"></i> {{Auth::user()->name}} <i class="fas fa-chevron-down float-end arrow-icon"></i>
                </a>
                <ul class="mobile-submenu">
                    @if(Auth::user()->role_code == 'USER')
                    <li><a href="{{route('user.dashboard')}}">Dashboard</a></li>
                    @endif
                    <li><a href="{{route('user.wishlist')}}">Wishlist</a></li>
                    <li><a href="{{route('user.orders')}}">My Orders</a></li>
                    <li><a href="{{route('user.profile')}}">My Profile</a></li>
                    <li><a href="{{ route('logout') }}">Logout</a></li>
                </ul>
            </li>
            @else
            <li class="mobile-nav-item">
                <a href="javascript:void(0)" class="mobile-nav-link" data-bs-toggle="modal" data-bs-target="#modalLoginForm">Login</a>
            </li>
            @endif
        </ul>
    </div>
</div>