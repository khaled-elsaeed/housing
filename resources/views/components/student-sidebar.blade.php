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
                    <span>{{ __('pages.student.sidebar.home') }}</span>
                </a>
            </li>

            <li>
                <a href="{{ route('student.maintenance.form') }}">
                    <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="questionnaires">
                    <span>{{ __('pages.student.sidebar.maintenance') }}</span>
                </a>
            </li>

            <!-- <li>
                <a href="{{ route('student.permission.form') }}">
                    <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="results">
                    <span>{{ __('pages.student.sidebar.permissions') }}</span>
                </a>
            </li> -->

            <li>
                <a href="{{ route('student.profile') }}">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="profile">
                    <span>{{ __('pages.student.sidebar.my_profile') }}</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
<!-- End Sidebar -->
