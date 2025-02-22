<div class="sidebar">
   <!-- Logo Section -->
   <div class="logobar">
      <a href="{{ route('staff.home') }}" class="logo logo-large">
         <img src="{{ asset('images/logo-wide.png') }}" class="img-fluid" alt="logo">
      </a>
      <a href="{{ route('staff.home') }}" class="logo logo-small">
         <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
      </a>
   </div>
   <!-- Navigation Section -->
   <div class="navigationbar">
      <ul class="vertical-menu">
         
         <!-- Maintenance Section -->
         <li>
            <a href="{{ route('staff.maintenance.index') }}">
               <img src="{{ asset('images/svg-icon/tables.svg') }}" class="img-fluid" alt="maintenance">
               <span>@lang('Maintenance')</span>
            </a>
         </li>


<li>
            
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