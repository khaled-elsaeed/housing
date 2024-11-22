<div class="sidebar">

    <div class="logobar">
        <a href="{{ route('admin.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('admin.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>

    <div class="navigationbar">
        <ul class="vertical-menu">

            <li>
                <a href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/layouts.svg') }}" class="img-fluid" alt="applicants">
                    <span>Applicants</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.applicant.view') }}">View Applicants</a></li>
                    <li><a href="{{ route('admin.applicant.documents.view') }}">Documents</a></li>
                </ul>
            </li>


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

            <li>
                <a href="javaScript:void();">
                    <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="housing">
                    <span>Housing</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.unit.building') }}">Buildings</a></li>
                    <li><a href="{{ route('admin.unit.apartment') }}">Apartments</a></li>
                    <li><a href="{{ route('admin.unit.room') }}">Rooms</a></li>
                </ul>
            </li>


            

            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="profile">
                    <span>Profile</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
                    <span>Settings</span>
                </a>
            </li>

            <li>
                <a href="#" onclick="logout()">
                    <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" alt="logout">
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </div>
</div>

<script>
    function logout() {
        if (confirm("Are you sure you want to logout?")) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('logout') }}";

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
            history.pushState(null, document.title, location.href);
        }
    }
</script>
