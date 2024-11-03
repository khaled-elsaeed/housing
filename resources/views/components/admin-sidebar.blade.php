<!-- Start Sidebar -->
<div class="sidebar">

    <!-- Start Logobar -->
    <div class="logobar">
        <a href="{{ route('admin.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('admin.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>
    <!-- End Logobar -->

    <!-- Start Navigationbar -->
    <div class="navigationbar">
        <ul class="vertical-menu">

            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Dashboard</span>
                    <i class="feather"></i>
                </a>
            </li>

            <!-- Applicants Link -->
            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/layouts.svg') }}" class="img-fluid" alt="applicants">
                    <span>Applicants</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.applicant.view') }}">Applicants</a></li>
                    <li><a href="{{ route('admin.applicant.invoice') }}">Documents</a></li>
                </ul>
            </li>

            <!-- Applicants Link -->
            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/basic.svg') }}" class="img-fluid" alt="applicants">
                    <span>Reservations</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                <li><a href="{{ route('admin.reservation.criteria') }}">Criteria</a></li>
                    <li><a href="#">Reservation</a></li>
                </ul>
            </li>

            <!-- Residents Link -->
            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="residents">
                    <span>Residents</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="#">View Residents</a></li>
                    <li><a href="#">Add Resident</a></li>
                </ul>
            </li>

            <!-- Housing Management Link -->
            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="housing management">
                    <span>Housing</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{route('admin.housing.building')}}">View Buildings</a></li>
                    <li><a href="{{route('admin.housing.apartment')}}">View Apartments</a></li>
                    <li><a href="{{route('admin.housing.room')}}">View Rooms</a></li>
                </ul>
            </li>

            <!-- Profile Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="profile">
                    <span>Profile</span>
                    <i class="feather"></i>
                </a>
            </li>

            <!-- Settings Link -->
            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
                    <span>Settings</span>
                    <i class="feather"></i>
                </a>
            </li>

            <!-- Logout Link -->
            <li>
                <a href="#" onclick="logout()">
                    <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" id="sidebar-logout-btn" alt="logout">
                    <span>Logout</span>
                    <i class="feather"></i>
                </a>
            </li>

        </ul>
    </div>
    <!-- End Navigationbar -->
</div>
<!-- End Sidebar -->
<script>
    function logout() {
        if (confirm("Are you sure you want to logout?")) {
            // Create a form dynamically
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('logout') }}"; // Set the logout route

            // Create a hidden input to include the CSRF token
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token'; // CSRF token field name
            csrfInput.value = '{{ csrf_token() }}'; // CSRF token value

            // Append the CSRF input to the form
            form.appendChild(csrfInput);

            // Append the form to the body and submit it
            document.body.appendChild(form);
            form.submit();

            // Prevent the user from navigating back to the dashboard
            history.pushState(null, document.title, location.href);
        }

        
    }

</script>
