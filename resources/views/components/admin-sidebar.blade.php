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
               <span>@lang('Dashboard')</span>
            </a>
         </li>
          <!-- Residents Section -->
         <li>
            <a href="{{ route('admin.residents.index') }}">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="invoices">
               <span>@lang('Residents')</span>
            </a>
         </li>
        <!-- Reservation Link -->
        <li>
            <a href="javascript:void(0);">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="reservation">
               <span>@lang('Reservation')</span>
               <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.reservation.index') }}">@lang('View Reservations')</a></li>
               <li><a href="{{ route('admin.reservation.relocation.index') }}">@lang('Relocation')</a></li>
            </ul>
         </li>
         <!-- Invoices Link -->
         <li>
            <a href="{{ route('admin.invoices.index') }}">
               <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="invoices">
               <span>@lang('Invoices')</span>
            </a>
         </li>
         
        
         <!-- Housing Section -->
         <li>
            <a href="javascript:void(0);">
               <img src="{{ asset('images/svg-icon/components.svg') }}" class="img-fluid" alt="housing">
               <span>@lang('Housing')</span>
               <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.unit.building') }}">@lang('View Buildings')</a></li>
               <li><a href="{{ route('admin.unit.apartment') }}">@lang('View Apartments')</a></li>
               <li><a href="{{ route('admin.unit.room') }}">@lang('View Rooms')</a></li>
            </ul>
         </li>
         <!-- Maintenance Section -->
         <li>
            <a href="{{ route('admin.maintenance.index') }}">
               <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="maintenance">
               <span>@lang('Maintenance')</span>
            </a>
         </li>
         <!-- Accounts Management Section -->
         <li>
            <a href="{{ route('admin.account.student.index') }}">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="accounts">
               <span>@lang('Accounts Management')</span>
            </a>
         </li>
        
         <!-- Settings Section -->
         <li>
            <a href="{{ route('admin.setting') }}">
               <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
               <span>@lang('Settings')</span>
            </a>
         </li>
      </ul>
   </div>
</div>
<!-- Logout Script -->
<script>
   function logout() {
       if (confirm("@lang('Are you sure you want to log out?')")) {
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