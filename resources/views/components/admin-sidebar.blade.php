<div class="sidebar">

    <!-- Logo Section -->
    <div class="logobar">
        <a href="{{ route('admin.home') }}" class="logo logo-large">
            <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
        </a>
        <a href="{{ route('admin.home') }}" class="logo logo-small">
            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
        </a>
    </div>

    <!-- Navigation Section -->
    <div class="navigationbar">
        <ul class="vertical-menu">

            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('admin.home') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Dashboard Link -->
            <li>
                <a href="{{ route('admin.applicants') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Applicants</span>
                </a>
            </li>

             <!-- Dashboard Link -->
             <li>
                <a href="{{ route('admin.invoices.index') }}">
                    <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
                    <span>Invoices</span>
                </a>
            </li>

           
            <li>
                <a href="javascript:void(0);">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="reservation">
                    <span>Reservation</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                <li><a href="{{ route('admin.reservation.index') }}">Reservation</a></li>

                    <li><a href="{{ route('admin.reservation.relocation.index') }}">Relocation</a></li>
                </ul>
            </li>


            <!-- Residents Section -->
            <li>
                <a href="javascript:void(0);">
                    <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="residents">
                    <span>Residents</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.residents.index') }}">View Residents</a></li>
                    <li><a href="#">Add Resident</a></li>
                </ul>
            </li>

            <!-- Housing Section -->
            <li>
                <a href="javascript:void(0);">
                    <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="housing">
                    <span>Housing</span>
                    <i class="feather icon-chevron-right pull-right"></i>
                </a>
                <ul class="vertical-submenu">
                    <li><a href="{{ route('admin.unit.building') }}">View Buildings</a></li>
                    <li><a href="{{ route('admin.unit.apartment') }}">View Apartments</a></li>
                    <li><a href="{{ route('admin.unit.room') }}">View Rooms</a></li>
                </ul>
            </li>

            <!-- Maintenance Section -->
            <li>
                <a href="{{ route('admin.maintenance.index') }}">
                    <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="maintenance">
                    <span>Maintenance</span>
                </a>
            </li>

            <!-- Permissions Section -->
            <li>
                <a href="{{ route('admin.permissions.index') }}">
                    <img src="{{ asset('images/svg-icon/widgets.svg') }}" class="img-fluid" alt="permissions">
                    <span>Permissions</span>
                </a>
            </li>

           

            <!-- Settings Section -->
            <li>
                <a href="{{ route('admin.setting') }}">
                    <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
                    <span>Settings</span>
                </a>
            </li>

        </ul>
    </div>
</div>

<!-- Logout Script -->
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
