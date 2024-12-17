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
            <span>{{ __('pages.admin.sidebar.dashboard') }}</span>
            </a>
         </li>
         <!-- Applicants Link -->
         <li>
            <a href="{{ route('admin.applicants') }}">
            <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="applicants">
            <span>{{ __('pages.admin.sidebar.applicants') }}</span>
            </a>
         </li>
         <!-- Invoices Link -->
         <li>
            <a href="{{ route('admin.invoices.index') }}">
            <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="invoices">
            <span>{{ __('pages.admin.sidebar.invoices') }}</span>
            </a>
         </li>
         <!-- Reservation Link -->
         <li>
            <a href="javascript:void(0);">
            <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="reservation">
            <span>{{ __('pages.admin.sidebar.reservation') }}</span>
            <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.reservation.index') }}">{{ __('pages.admin.sidebar.view_reservation') }}</a></li>
               <li><a href="{{ route('admin.reservation.relocation.index') }}">{{ __('pages.admin.sidebar.relocation') }}</a></li>
            </ul>
         </li>
         <!-- Residents Section -->
         <li>
            <a href="javascript:void(0);">
            <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="residents">
            <span>{{ __('pages.admin.sidebar.residents') }}</span>
            <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.residents.index') }}">{{ __('pages.admin.sidebar.view_residents') }}</a></li>
               <li><a href="{{ route('admin.residents.create') }}">{{ __('pages.admin.sidebar.add_resident') }}</a></li>
            </ul>
         </li>
         <!-- Housing Section -->
         <li>
            <a href="javascript:void(0);">
            <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="housing">
            <span>{{ __('pages.admin.sidebar.housing') }}</span>
            <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.unit.building') }}">{{ __('pages.admin.sidebar.view_buildings') }}</a></li>
               <li><a href="{{ route('admin.unit.apartment') }}">{{ __('pages.admin.sidebar.view_apartments') }}</a></li>
               <li><a href="{{ route('admin.unit.room') }}">{{ __('pages.admin.sidebar.view_rooms') }}</a></li>
            </ul>
         </li>
         <!-- Maintenance Section -->
         <li>
            <a href="{{ route('admin.maintenance.index') }}">
            <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="maintenance">
            <span>{{ __('pages.admin.sidebar.maintenance') }}</span>
            </a>
         </li>
         <!-- Accounts Management Section -->
         <li>
            <a href="{{ route('admin.account.student.index') }}">
            <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="accounts">
            <span>{{ __('pages.admin.sidebar.accounts_management') }}</span>
            </a>
         </li>
         <!-- Settings Section -->
         <li>
            <a href="{{ route('admin.setting') }}">
            <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
            <span>{{ __('pages.admin.sidebar.settings') }}</span>
            </a>
         </li>
      </ul>
   </div>
</div>
<!-- Logout Script -->
<script>
   function logout() {
       if (confirm("{{ __('pages.admin.sidebar.logout_confirmation') }}")) {
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