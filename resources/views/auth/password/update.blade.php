<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <!-- Meta Information -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ __('auth.update_password.page_title') }}</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet" type="text/css">
    @if(app()->isLocale('en'))
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    @else
    <link href="{{ asset('css/bootstrap.rtl.min.css') }}" rel="stylesheet" type="text/css">
    @endif

       <!-- Load SweetAlert2 -->

<script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>

<link href="{{ asset('css/authenication.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<div class="language-switcher-container">
    <div class="language-switcher">
        <span class="language-option @if(app()->isLocale('en')) active @endif" data-lang="en">
            <i class="flag-icon flag-icon-us"></i> EN
        </span>
        <span class="language-option @if(app()->isLocale('ar')) active @endif" data-lang="ar">
            <i class="flag-icon flag-icon-eg"></i> AR
        </span>
    </div>
</div>

<!-- Update Password Form -->
<div class="auth-box">
    <div class="row g-0">
        <!-- Left Side Image -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
            <img src="{{ asset('images/authentication/update_password.svg') }}" class="img-fluid" alt="{{ __('auth.update_password.image_alt') }}">
        </div>

        <!-- Update Password Form Section -->
        <div class="col-md-6 p-4">
            <div class="text-center">
                <div class="auth-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </a>
                </div>
                <h4 class="text-primary mb-4">{{ __('auth.update_password.title') }}</h4>
            </div>
            <form method="POST" action="{{ route('password.update', $token) }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Success Message -->
                @if (session('status'))
                <script>
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: '{{ __('auth.update_password.success') }}',
                        text: '{{ session('status') }}',
                        position: 'top-start',
                        showConfirmButton: false,
                        timer: 8000,
                        timerProgressBar: true,
                    });
                </script>
                @endif

                <!-- Validation Errors -->
                @if ($errors->any())
                <script>
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: '{{ __('auth.update_password.error') }}',
                        text: '{{ $errors->first() }}',
                        position: 'top-start',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                </script>
                @endif

             
                <!-- New Password Input -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('auth.update_password.new_password_placeholder') }}" required>
                    <label for="password">{{ __('auth.update_password.new_password_label') }}</label>
                </div>

                <!-- Confirm Password Input -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('auth.update_password.confirm_password_placeholder') }}" required>
                    <label for="password_confirmation">{{ __('auth.update_password.confirm_password_label') }}</label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-4">
                    <button class="btn btn-primary btn-lg">{{ __('auth.update_password.submit_button') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 {{ __('auth.update_password.university_name') }}. {{ __('auth.update_password.rights_reserved') }}</p>
    </div>
</footer>

<!-- Scripts -->
<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
<script>
    // Language Switcher
    document.querySelectorAll('.language-option').forEach(option => {
        option.addEventListener('click', function () {
            const selectedLang = this.getAttribute('data-lang');
            const routeUrl = `{{ route('localization', ':lang') }}`.replace(':lang', selectedLang);
            window.location.href = routeUrl;
        });
    });
</script>
</body>

</html>
