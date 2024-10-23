<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Information -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="New Mansoura University Housing offers comfortable, affordable, and secure accommodation for students. Conveniently located near the campus, our modern facilities provide a supportive environment for academic success.">
    <meta name="keywords" content="New Mansoura University, university housing, student accommodation, New Mansoura, dorms, student apartments, affordable student housing, university residence, student life, student housing, NMU housing, New Mansoura student living, secure housing for students">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>NMU Housing - Set New Password</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/authenication.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <!-- End CSS Files -->
           <!-- Load SweetAlert2 from CDN -->
           <script src="{{ asset('plugins/sweet-alert2/sweetalert2.all.min.js') }}"></script>
</head>

<body class="vertical-layout">
    <!-- Start Containerbar -->
    <div id="containerbar" class="containerbar authenticate-bg">
        <!-- Start Container -->
        <div class="container">
            <div class="auth-box login-box">
                <!-- Start Row -->
                <div class="row no-gutters align-items-center justify-content-center flex-column-reverse">
                    <!-- Start Form Column -->
                    <div class="col-12 col-md-6">
                        <!-- Start Auth Box -->
                        <div class="auth-box-right">
                            <div class="card text-start">
                                <div class="card-body">
                                    <!-- Logo at the top -->
                                    <div class="auth-logo text-center">
                                        <a href="index.html">
                                            <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="logo">
                                        </a>
                                    </div>

                                    <h4 class="text-primary mb-4">Set Your New Password 🔑</h4>

                                   <!-- Password Reset Form -->
<form method="POST" action="{{ route('password.update', $token) }}">
    @csrf
    @if (session('success'))
                                 <script>
                                    Swal.fire({
                                        toast: true,
                                        icon: 'success',
                                        title: 'Success!',
                                        text: '{{ session('success') }}',
                                        position: 'top-start',  // Position the toast at the top-right
                                        showConfirmButton: false,  // No confirmation button
                                        timer: 8000,  // Auto-close after 3 seconds
                                        timerProgressBar: true,  // Show progress bar
                                    });
                                 </script>
                                 @endif
                                 <!-- SweetAlert for Validation Errors -->
                                 @if ($errors->any())
                                 <script>
                                    Swal.fire({
                                        toast: true,
                                        icon: 'error',
                                        title: 'Error!',
                                        text: '{{ $errors->first() }}',
                                        position: 'top-start',  // Position the toast at the top-right
                                        showConfirmButton: false,  // No confirmation button
                                        timer: 3000,  // Auto-close after 3 seconds
                                        timerProgressBar: true,  // Show progress bar
                                    });
                                 </script>
                                 @endif
    <!-- New Password Field -->
    <div class="form-floating mb-3">
        <input type="password" class="form-control text-secondary" id="floatingPassword" placeholder="New Password" name="password" required>
        <label for="floatingPassword">New Password</label>
    </div>

    <!-- Confirm Password Field -->
    <div class="form-floating mb-3">
        <input type="password" class="form-control text-secondary" id="floatingPasswordConfirm" placeholder="Confirm Password" name="password_confirmation" required>
        <label for="floatingPasswordConfirm">Confirm Password</label>
    </div>

    <!-- Hidden Token Field -->
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Submit Button -->
    <div class="d-grid mb-4">
        <button class="btn btn-primary font-18" type="submit">Update Password</button>
    </div>
</form>


                                    <!-- Back to login link -->
                                    <p class="mb-0 mt-3">Remember your password? <a href="{{ route('login') }}">Login</a></p>
                                </div>
                            </div>
                        </div>
                        <!-- End Auth Box -->
                    </div>
                    <!-- End Form Column -->

                    <!-- Start Additional Column -->
                    <div class="col-12 col-md-6 d-md-block d-none">
                        <!-- Start Auth Box -->
                        <div class="auth-box-right">
                            <img src="{{ asset('images/authentication/login-hero.svg') }}" alt="Reset Password Image">
                        </div>
                        <!-- End Auth Box -->
                    </div>
                    <!-- End Additional Column -->
                </div>
                <!-- End Row -->
            </div>
        </div>
        <!-- End Container -->
    </div>
    <!-- End Containerbar -->

    <!-- Footer Section -->
    <footer class="text-center mt-2">
        <div class="container">
            <p class="text-muted">&copy;2024 New Mansoura University. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Start JavaScript Files -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/modernizr.min.js') }}"></script>
    <script src="{{ asset('js/detect.js') }}"></script>
    <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
    <!-- End JavaScript Files -->

</body>
</html>
