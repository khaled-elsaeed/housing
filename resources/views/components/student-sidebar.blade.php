<!-- Start Sidebar -->
<div class="sidebar">
    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('student.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('student.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>

    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">
            <li>
                <a href="{{ route('student.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>@lang('Home')</span>
                </a>
            </li>

            <li class="{{ request()->is('student/maintenance*') ? 'active' : '' }}">
    <a href="javascript:void(0)" class="{{ request()->is('student/maintenance*') ? 'active' : '' }}">
        <img src="{{ asset('images/svg-icon/error.svg') }}" class="img-fluid" alt="maintenance">
        <span>@lang('Maintenance')</span>
        <i class="feather icon-chevron-right"></i>
    </a>
    <ul class="vertical-submenu">
        <li class="{{ request()->routeIs('student.maintenance.index') ? 'active' : '' }}">
            <a href="{{ route('student.maintenance.index') }}" class="{{ request()->routeIs('student.maintenance.index') ? 'active' : '' }}">
                @lang('All Requests')
            </a>
        </li>
        <li class="{{ request()->routeIs('student.maintenance.create') ? 'active' : '' }}">
            <a href="{{ route('student.maintenance.create') }}" class="{{ request()->routeIs('student.maintenance.create') ? 'active' : '' }}">
                @lang('Submit New Request')
            </a>
        </li>
    </ul>
</li>

            <li>
                <a href="{{ route('student.profile') }}" class="{{ request()->routeIs('student.profile*') ? 'active' : '' }}">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="profile">
                    <span>@lang('My Profile')</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
<!-- End Sidebar -->