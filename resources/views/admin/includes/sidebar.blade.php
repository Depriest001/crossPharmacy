<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand pt-6">
        <a href="#" class="app-brand-link">
            <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Logo" width="150px">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>

    @php
        $role = auth('staff')->user()->role->role_type ?? '';
    @endphp

    <ul class="menu-inner py-1">

        <!-- ADMIN ONLY -->
        @if($role === 'Admin')
            <li class="menu-item {{ request()->routeIs('role.*') ? 'active' : '' }}">
                <a href="{{ route('role.index') }}" class="menu-link">
                    <i class="menu-icon icon-base bx bx-check-shield"></i>
                    <div class="text-truncate">Roles</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('branch.*') ? 'active' : '' }}">
                <a href="{{ route('branch.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-git-branch"></i>
                    <div class="text-truncate">Branches</div>
                </a>
            </li>
        @endif

        <!-- ADMIN & STAFF shared items -->
        @if(in_array($role, ['Admin', 'Staff']))

            <!-- DASHBOARD (everyone can see) -->
            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-smile"></i>
                    <div class="text-truncate">Dashboard</div>
                </a>
            </li>
            
            <li class="menu-item {{ request()->routeIs('staffs.*') ? 'active' : '' }}">
                <a href="{{ route('staffs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-bxs-user-account"></i>
                    <div class="text-truncate">Staff</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('category.*') ? 'active' : '' }}">
                <a href="{{ route('category.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-grid-alt"></i>
                    <div class="text-truncate">Product Categories</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('product.*') ? 'active' : '' }}">
                <a href="{{ route('product.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-cube-alt"></i>
                    <div class="text-truncate">Products</div>
                </a>
            </li>
        @endif

        <!-- CASHIER + STAFF + ADMIN (POS shared) -->
        <li class="menu-item {{ request()->routeIs('admin.stock.*') ? 'active' : '' }}">
            <a href="{{ route('admin.stock.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bxs-layer"></i>
                <div class="text-truncate">Stock Management</div>
            </a>
        </li>

        @if(in_array($role, ['Admin', 'Staff', 'Seller']))
        <li class="menu-item {{ request()->routeIs('pos.entry') ? 'active' : '' }}">
            <a href="{{ route('pos.entry') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bxs-cart-alt"></i>
                <div class="text-truncate">Point of Sale (POS)</div>
            </a>
        </li>
        @endif

        @if(in_array($role, ['Admin', 'Staff', 'Cashier']))
        <li class="menu-item {{ request()->routeIs('pos.checkout') ? 'active' : '' }}">
            <a href="{{ route('pos.checkout') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bxs-cart-add"></i>
                <div class="text-truncate">Point of Sale (POS)</div>
            </a>
        </li>
        @endif

        <li class="menu-item {{ request()->routeIs('report.sale') ? 'active' : '' }}">
            <a href="{{ route('report.sale') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i>
                <div class="text-truncate">Sale Report</div>
            </a>
        </li>

        <!-- ADMIN ONLY -->
        @if($role === 'Admin')
        <li class="menu-item {{ request()->routeIs('admin.setting') ? 'active' : '' }}">
            <a href="{{ route('admin.setting') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div class="text-truncate">System Setting</div>
            </a>
        </li>
        @endif

        <!-- LOGOUT -->
        <li class="menu-item">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
            <a href="javascript:void(0);" class="menu-link"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="menu-icon tf-icons bx bx-power-off text-danger"></i>
                <div class="text-truncate">Log Out</div>
            </a>
        </li>
    </ul>
</aside>