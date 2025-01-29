<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="description" content="New Mansoura University Housing offers comfortable, affordable, and secure accommodation for students. Conveniently located near the campus, our modern facilities provide a supportive environment for academic success.">
      <meta name="keywords" content="New Mansoura University, university housing, student accommodation, New Mansoura, dorms, student apartments, affordable student housing, university residence, student life, student housing, NMU housing, New Mansoura student living, secure housing for students">
      <meta name="author" content="Themesbox">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <!-- Dynamic Page Title -->
      <title>@lang('NMU Housing') - @yield('title', __('Default Title'))</title>

      <!-- Icons -->
      <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}?v={{ config('app.version') }}">
      <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}?v={{ config('app.version') }}">
      <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}?v={{ config('app.version') }}">

      <!-- Global CSS -->
      @if(app()->isLocale('en'))
        <link href="{{ asset('css/bootstrap.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/style.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
      @else
        <link href="{{ asset('css/bootstrap.rtl.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/style.rtl.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
      @endif
      <link href="{{ asset('css/icons.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('css/flag-icon.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css">
      <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />

      <!-- Page-Specific CSS -->
      @yield('links')

   </head>
   <body class="vertical-layout">
      <!-- Start Containerbar -->
      <div id="containerbar">
         <!-- Start Leftbar -->
         <div class="leftbar">
            <!-- Start Sidebar -->
            <x-student-sidebar />
            <!-- End Sidebar -->
         </div>
         <!-- End Leftbar -->

         <!-- Start Rightbar -->
         <div class="rightbar">
            <!-- Start Topbar Mobile -->
            <x-mobile-topbar />
            <!-- Start Topbar -->
            <x-topbar />
            <!-- End Topbar -->

            <!-- Start Contentbar -->    
            <div class="contentbar">
               @yield('content') <!-- Page content will be injected here -->
            </div>
            <!-- End Contentbar -->

            <!-- Start Footerbar -->
            <x-footbar />
            <!-- End Footerbar -->
         </div>
         <!-- End Rightbar -->
      </div>
      <!-- End Containerbar -->

      <!-- Global JS -->
      <script src="{{ asset('js/jquery.min.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/popper.min.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/bootstrap.bundle.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/modernizr.min.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/detect.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/jquery.slimscroll.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/vertical-menu.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}?v={{ config('app.version') }}"></script>

      <!-- Page-Specific JS -->
      @yield('scripts')
      <!-- Core JS -->
      <script src="{{ asset('js/core.js') }}?v={{ config('app.version') }}"></script>
      <script src="{{ asset('js/session-handler.js') }}?v={{ config('app.version') }}"></script>
      <script>
         const sessionHandler = new SessionHandler({
            sessionTimeout: {{ config('session.lifetime') * 60 * 1000 }},
            warningTime: 5 * 60 * 1000, // warning 5 minutes before session time out
            checkInterval: 60 * 1000 // check every minute
         });
      </script>
   </body>
</html>
