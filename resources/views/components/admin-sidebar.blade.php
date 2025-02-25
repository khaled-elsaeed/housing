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
         <!-- Show for Both Admin & housing_manager -->
         @hasanyrole('admin|housing_manager')
         <li>
            <a href="{{ route('admin.home') }}">
               <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="dashboard">
               <span>@lang('Dashboard')</span>
            </a>
         </li>
         @endhasanyrole

         @hasanyrole('admin|housing_manager')
         <li>
            <a href="{{ route('admin.residents.index') }}">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="residents">
               <span>@lang('Residents')</span>
            </a>
         </li>
         @endhasanyrole

         @hasrole('admin')
         <li>
            <a href="javascript:void(0);">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="reservation">
               <span>@lang('Reservation')</span>
               <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.reservation-requests.index') }}">@lang('Requests')</a></li>
               <li><a href="{{ route('admin.reservation.index') }}">@lang('Reservations')</a></li>
               <li><a href="{{ route('admin.reservation.relocation.index') }}">@lang('Relocation')</a></li>
            </ul>
         </li>
         @endhasrole

         <!-- Show for Both Admin & housing_manager -->
         @hasanyrole('admin|housing_manager')
         <li>
            <a href="{{ route('admin.invoices.index') }}">
               <img src="{{ asset('images/svg-icon/dashboard.svg') }}" class="img-fluid" alt="invoices">
               <span>@lang('Invoices')</span>
            </a>
         </li>
         @endhasanyrole

         @hasanyrole('admin|housing_manager')
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
         @endhasanyrole

         @hasanyrole('admin|housing_manager')
         <li>
            <a href="{{ route('admin.maintenance.index') }}">
               <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="maintenance">
               <span>@lang('Maintenance')</span>
            </a>
         </li>
         @endhasanyrole

         @hasrole('admin')
         <li>
            <a href="javascript:void(0);">
               <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="accounts">
               <span>@lang('Accounts')</span>
               <i class="feather icon-chevron-right pull-right"></i>
            </a>
            <ul class="vertical-submenu">
               <li><a href="{{ route('admin.account.resident.index') }}">@lang('Residents')</a></li>
               <li><a href="{{ route('admin.account.staff.index') }}">@lang('Staff')</a></li>
            </ul>
         </li>
         @endhasrole

         @hasrole('admin')
         <li>
            <a href="{{ route('admin.setting') }}">
               <img src="{{ asset('images/svg-icon/settings.svg') }}" class="img-fluid" alt="settings">
               <span>@lang('Settings')</span>
            </a>
         </li>
         @endhasrole
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
