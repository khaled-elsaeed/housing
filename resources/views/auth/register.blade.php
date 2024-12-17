<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ __('auth.register.page_title') }}</title>
    
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    @if(app()->isLocale('en'))
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    @else
    <link href="{{ asset('css/bootstrap.rtl.min.css') }}" rel="stylesheet" type="text/css">
    @endif

    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/4.1.7/css/flag-icon.min.css" rel="stylesheet">
    <link href="{{ asset('css/authenication.css') }}" rel="stylesheet" type="text/css">
</head>

<body>
<div class="auth-box">
    <div class="row g-0">
        <!-- Left Side Image -->
        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center">
            <img src="{{ asset('images/authentication/sgin-up-hero.svg') }}" class="img-fluid" alt="{{ __('auth.register.register_image_alt') }}">
        </div>

        <!-- Registration Form Section -->
        <div class="col-md-6 p-4">
            <div class="text-center">
                <div class="auth-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </a>
                </div>
                <h4 class="text-primary mb-4">{{ __('auth.register.create_account') }} 👋</h4>
            </div>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <!-- Validation Errors -->
                @if ($errors->any())
                <script>
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: '{{ __('auth.register.error') }}',
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
                    <input type="text" class="form-control" id="nationalId" name="national_id" placeholder="{{ __('auth.register.national_id_placeholder') }}" required>
                    <label for="nationalId">{{ __('auth.register.national_id_label') }}</label>
                </div>

                <!-- Email -->
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('auth.register.email_placeholder') }}" required>
                    <label for="email">{{ __('auth.register.email_label') }}</label>
                </div>

                <!-- Password -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('auth.register.password_placeholder') }}" required>
                    <label for="password">{{ __('auth.register.password_label') }}</label>
                </div>

                <!-- Confirm Password -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="{{ __('auth.register.confirm_password_placeholder') }}" required>
                    <label for="confirmPassword">{{ __('auth.register.confirm_password_label') }}</label>
                </div>

                <!-- Submit Button -->
                <div class="d-grid mb-4">
                    <button class="btn btn-primary btn-lg">{{ __('auth.register.register_button') }}</button>
                </div>
            </form>
            <p class="text-center text-secondary ">{{ __('auth.register.already_have_account') }} <a href="{{ route('login') }}" class="text-primary">{{ __('auth.register.login') }}</a></p>
        </div>
    </div>
</div>

<footer class="text-center">
    <div class="container">
        <p>&copy; 2024 {{ __('auth.register.university_name') }}. {{ __('auth.register.rights_reserved') }}</p>
    </div>
</footer>

<script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
</body>

</html>
