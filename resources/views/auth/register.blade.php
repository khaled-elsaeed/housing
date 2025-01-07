<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>@lang('Register')</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Load SweetAlert2 -->
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>

    <link href="{{ asset('css/authenication.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<div class="auth-box">
    <div class="row g-0">
        <!-- Left Side Image -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
            <img src="{{ asset('images/authentication/sgin-up-hero.svg') }}" class="img-fluid" alt="@lang('Register Image')">
        </div>

        <!-- Registration Form Section -->
        <div class="col-md-6 p-4">
            <div class="text-center">
                <div class="auth-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="@lang('Logo')">
                    </a>
                </div>
                <h4 class="text-primary mb-4">@lang('Create Account') 👋</h4>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <!-- Validation Errors -->
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

                <!-- National ID -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nationalId" name="national_id" placeholder="@lang('National ID')" required>
                    <label for="nationalId">@lang('National ID')</label>
                </div>

                <!-- Email -->
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="@lang('Email')" required>
                    <label for="email">@lang('Email')</label>
                </div>

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="@lang('Password')" required>
                    <label for="password">@lang('Password')</label>
                </div>

                <!-- Confirm Password -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="@lang('Confirm Password')" required>
                    <label for="confirmPassword">@lang('Confirm Password')</label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-4">
                    <button class="btn btn-primary btn-lg">@lang('Register')</button>
                </div>
            </form>
            <p class="text-center text-secondary">@lang('Already have an account?') <a href="{{ route('login') }}" class="text-primary">@lang('Login')</a></p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 @lang('New Mansoura University'). @lang('All Rights Reserved').</p>
    </div>
</footer>

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
