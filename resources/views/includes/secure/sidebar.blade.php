<ul class="pc-navbar">

    {{-- Website Dashboard --}}
    @can('view website dashboard')
    <li class="pc-item">
        <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
        </a>
    </li>
    @endcan

    {{-- Order Management --}}
    @canany(['view order', 'view order cancellation request', 'view return request', 'view refund'])
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
            <span class="pc-mtext">Order Management</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            @can('view order')
            <li class="pc-item"><a class="pc-link" href="{{ route('orders.index') }}">All Orders</a></li>
            @endcan
            @can('view order cancellation request')
            <li class="pc-item"><a class="pc-link" href="{{ route('order-cancellation-requests.index') }}">Cancel Requests</a></li>
            @endcan
            @can('view return request')
            <li class="pc-item"><a class="pc-link" href="{{ route('return-requests.index') }}">Return Requests</a></li>
            @endcan
            @can('view refund')
            <li class="pc-item"><a class="pc-link" href="{{ route('refunds.index') }}">Refunds</a></li>
            @endcan
        </ul>
    </li>
    @endcanany

    {{-- Product Catalog --}}
    @canany(['view product', 'view category', 'view brand', 'view offer', 'view size', 'view material', 'view product type', 'view unit type', 'view intended use'])
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="ti ti-box"></i></span>
            <span class="pc-mtext">Product Catalog</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            @can('view product')
            <li class="pc-item"><a class="pc-link" href="{{ route('products.index') }}">Products</a></li>
            @endcan
            @can('view category')
            <li class="pc-item"><a class="pc-link" href="{{ route('categories.index') }}">Categories</a></li>
            @endcan
            @can('view brand')
            <li class="pc-item"><a class="pc-link" href="{{ route('brands.index') }}">Brands</a></li>
            @endcan
            @can('view offer')
            <li class="pc-item"><a class="pc-link" href="{{ route('offers.index') }}">Offers</a></li>
            @endcan

            {{-- Attributes Sub-menu --}}
            @canany(['view size', 'view material', 'view product type', 'view unit type', 'view intended use'])
            <li class="pc-item pc-hasmenu">
                <a href="#!" class="pc-link">Attributes<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                <ul class="pc-submenu">
                    @can('view size')
                    <li class="pc-item"><a class="pc-link" href="{{ route('sizes.index') }}">Sizes</a></li>
                    @endcan
                    @can('view material')
                    <li class="pc-item"><a class="pc-link" href="{{ route('materials.index') }}">Materials</a></li>
                    @endcan
                    @can('view product type')
                    <li class="pc-item"><a class="pc-link" href="{{ route('product-types.index') }}">Product Types</a></li>
                    @endcan
                    @can('view unit type')
                    <li class="pc-item"><a class="pc-link" href="{{ route('unit-types.index') }}">Unit Types</a></li>
                    @endcan
                    @can('view intended use')
                    <li class="pc-item"><a class="pc-link" href="{{ route('intended-uses.index') }}">Intended Uses</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany
        </ul>
    </li>
    @endcanany

    {{-- Website CMS --}}
    @canany(['view page', 'view slider', 'view media', 'view announcement', 'view our partner', 'menu setup'])
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="ti ti-layout"></i></span>
            <span class="pc-mtext">Website CMS</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            @can('view page')
            <li class="pc-item"><a class="pc-link" href="{{ route('pages.index') }}">Pages</a></li>
            @endcan
            @can('view slider')
            <li class="pc-item"><a class="pc-link" href="{{ route('sliders.index') }}">Sliders</a></li>
            @endcan
            @can('view media')
            <li class="pc-item"><a class="pc-link" href="{{ route('medias.index') }}">Media Library</a></li>
            @endcan
            @can('view announcement')
            <li class="pc-item"><a class="pc-link" href="{{ route('announcements.index') }}">Announcements</a></li>
            @endcan
            @can('view our partner')
            <li class="pc-item"><a class="pc-link" href="{{ route('our-partners.index') }}">Our Partners</a></li>
            @endcan
            @can('menu setup')
            <li class="pc-item"><a class="pc-link" href="{{ route('menus.index') }}">Menu Setup</a></li>
            @endcan
        </ul>
    </li>
    @endcanany

    {{-- Customer Interaction --}}
    @canany(['view feedback', 'view contact detail', 'view social media'])
    <li class="pc-item pc-hasmenu">
        <a href="#" class="pc-link">
            <span class="pc-micon"><i class="ti ti-users"></i></span>
            <span class="pc-mtext">Engagements</span>
            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
        </a>
        <ul class="pc-submenu">
            {{-- @can('view feedback')
            <li class="pc-item"><a class="pc-link" href="{{ route('feedbacks.index') }}">Feedbacks</a>
    </li>
    @endcan --}}
    @can('view contact query')
    <li class="pc-item"><a class="pc-link" href="{{ route('contact-queries.index') }}">Contact Queries</a></li>
    @endcan
    @can('view contact detail')
    <li class="pc-item"><a class="pc-link" href="{{ route('contact-details.index') }}">Contact Details</a></li>
    @endcan
    @can('view social media')
    <li class="pc-item"><a class="pc-link" href="{{ route('social-medias.index') }}">Social Media</a></li>
    @endcan
    @can('view newsletter')
    <li class="pc-item"><a class="pc-link" href="{{ route('newsletters.index') }}">Newsletter</a></li>
    @endcan
    @can('view customer review')
    <li class="pc-item"><a class="pc-link" href="{{ route('customer-reviews.index') }}">Customer Reviews</a></li>
    @endcan
</ul>
</li>
@endcanany

{{-- Configuration --}}
<li class="pc-item pc-caption">
    <label>Configuration</label>
</li>

{{-- Sales Settings --}}
@canany(['view coupon', 'view return policy', 'view return reason', 'view cancel reason'])
<li class="pc-item pc-hasmenu">
    <a href="#" class="pc-link">
        <span class="pc-micon"><i class="ti ti-ticket"></i></span>
        <span class="pc-mtext">Sales Settings</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @can('view coupon')
        <li class="pc-item"><a class="pc-link" href="{{ route('coupons.index') }}">Coupons</a></li>
        @endcan
        @can('view return policy')
        <li class="pc-item"><a class="pc-link" href="{{ route('return-policies.index') }}">Return Policies</a></li>
        @endcan
        @can('view return reason')
        <li class="pc-item"><a class="pc-link" href="{{ route('return-reasons.index') }}">Return Reasons</a></li>
        @endcan
        @can('view cancel reason')
        <li class="pc-item"><a class="pc-link" href="{{ route('cancel-reasons.index') }}">Cancel Reasons</a></li>
        @endcan
    </ul>
</li>
@endcanany

{{-- Localization & Payment --}}
@canany(['view payment gateway', 'view currency', 'view country', 'view state'])
<li class="pc-item pc-hasmenu">
    <a href="#" class="pc-link">
        <span class="pc-micon"><i class="ti ti-world"></i></span>
        <span class="pc-mtext">Localization & Payment</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @can('view payment gateway')
        <li class="pc-item"><a class="pc-link" href="{{ route('payment-gateways.index') }}">Payment Gateways</a></li>
        @endcan
        @can('view payment method')
        <li class="pc-item"><a class="pc-link" href="{{ route('payment-methods.index') }}">Payment Methods</a></li>
        @endcan
        @can('view currency')
        <li class="pc-item"><a class="pc-link" href="{{ route('currencies.index') }}">Currencies</a></li>
        @endcan
        @can('view country')
        <li class="pc-item"><a class="pc-link" href="{{ route('countries.index') }}">Countries</a></li>
        @endcan
        @can('view state')
        <li class="pc-item"><a class="pc-link" href="{{ route('states.index') }}">States</a></li>
        @endcan
    </ul>
</li>
@endcanany

{{-- System Administration --}}
<li class="pc-item pc-caption">
    <label>Administration</label>
</li>

{{-- Access Control --}}
@canany(['view user', 'view role'])
<li class="pc-item pc-hasmenu">
    <a href="#" class="pc-link">
        <span class="pc-micon"><i class="ti ti-lock"></i></span>
        <span class="pc-mtext">Access Control</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @can('view user')
        <li class="pc-item"><a class="pc-link" href="{{ route('users.index') }}">Users</a></li>
        @endcan
        @can('view role')
        <li class="pc-item"><a class="pc-link" href="{{ route('roles.index') }}">Roles</a></li>
        @endcan
    </ul>
</li>
@endcanany

{{-- System Logs --}}
@canany(['view audit log', 'view authentication log', 'view email log', 'view sms log'])
<li class="pc-item pc-hasmenu">
    <a href="#" class="pc-link">
        <span class="pc-micon"><i class="ti ti-activity"></i></span>
        <span class="pc-mtext">System Logs</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        @can('view audit log')
        <li class="pc-item"><a class="pc-link" href="{{ route('audit-logs.index') }}">Audit Log</a></li>
        @endcan
        @can('view authentication log')
        <li class="pc-item"><a class="pc-link" href="{{ route('authentication-logs.index') }}">Authentication Log</a></li>
        @endcan
        @can('view email log')
        <li class="pc-item"><a class="pc-link" href="{{ route('email-logs.index') }}">Email Log</a></li>
        @endcan
        @can('view sms log')
        <li class="pc-item"><a class="pc-link" href="{{ route('sms-logs.index') }}">SMS Log</a></li>
        @endcan
    </ul>
</li>
@endcanany

{{-- Site Settings --}}
@can('edit site setting')
<li class="pc-item">
    <a href="{{ route('site-settings.index') }}" class="pc-link">
        <span class="pc-micon"><i class="ti ti-settings"></i></span>
        <span class="pc-mtext">Global Settings</span>
    </a>
</li>
@endcan

</ul>