<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <!-- Meta Information -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@lang('Login')</title>
    
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/flag-icon.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Load SweetAlert2 -->
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>

    <link href="{{ asset('css/authenication.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<!-- Login Form -->
<div class="auth-box">
    <div class="row g-0">
        <!-- Left Side Image -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
            <img src="{{ asset('images/authentication/login-hero.svg') }}" class="img-fluid" alt="Login Image">
        </div>

        <!-- Login Form Section -->
        <div class="col-md-6 p-4">
            <div class="text-center mb-3 mb-lg-5">
                <div class="auth-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="@lang('Logo')">
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <!-- Success Message -->
                @if (session('success'))
                <script>
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: '@lang('Success')',
                        text: '{{ session('success') }}',
                        position: 'top-start',
                        showConfirmButton: false,
                        timer: 7000,
                        timerProgressBar: true,
                    });
                </script>
                @endif
                @if ($errors->any())
                <script>
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: '@lang('Error')',
                        text: '{{ $errors->first() }}',
                        position: 'top-start',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                </script>
                @endif


                <!-- Email Input -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="identifier" placeholder="@lang('Email')" required>
                    <label for="floatingInput">@lang('Email')</label>
                </div>

                <!-- Password Input -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="@lang('Password')" required>
                    <label for="floatingPassword">@lang('Password')</label>
                </div>

                <!-- Forgot Password -->
                <div class="mb-3 text-end">
                    <a href="{{ route('password.request') }}" class="text-secondary small">@lang('Forgot password?')</a>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-4">
                    <button class="btn btn-primary btn-lg">@lang('Login')</button>
                </div>
            </form>
            <p class="text-center text-secondary">@lang("Don't have an account?") <a href="{{ route('register') }}" class="text-primary">@lang('Create one')</a></p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 @lang('New Mansoura University'). @lang('All Rights Reserved').</p>
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
