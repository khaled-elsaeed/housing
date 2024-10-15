<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Information -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="New Mansoura University Housing offers comfortable, affordable, and secure accommodation for students. Conveniently located near the campus, our modern facilities provide a supportive environment for academic success.">
    <meta name="keywords" content="New Mansoura University, university housing, student accommodation, New Mansoura, dorms, student apartments, affordable student housing, university residence, student life, student housing, NMU housing, New Mansoura student living, secure housing for students">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <title>NMU Housing - Login</title>

   <!-- Favicon -->
    <!-- Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">


    <!-- CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/authenication.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">
    <!-- End CSS Files -->

</head>

<body class="vertical-layout">
    <!-- Start Containerbar -->
    <div id="containerbar" class="containerbar authenticate-bg">
        <!-- Start Container -->
        <div class="container">
            <div class="auth-box login-box">
                <!-- Start Row -->
                <div class="row no-gutters align-items-center justify-content-center flex-column-reverse">
                    <!-- Start Login Form Column -->
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

                                    <h4 class="text-primary mb-4">Welcome back 👋!</h4> <!-- Reduced margin -->

                                    <form method="POST" action="{{ route('login') }}">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control text-secondary" id="floatingInput" placeholder="name@example.com">
                                            <label for="floatingInput">Email / National ID</label>
                                        </div>
                                        

                                        <div class="form-floating mb-3"> <!-- Added margin to match others -->
                                            <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
                                            <label for="floatingPassword">Password</label>
                                        </div>

                                        <div class="row mb-4">
                                            <div class="col">
                                                <a id="forgot-psw" href="password-reset.html" class="font-14">Forgot Password?</a>
                                            </div>
                                        </div>

                                        <div class="d-grid mb-4">
                                            <button class="btn btn-primary font-18" type="submit">Log in</button>
                                        </div>
                                    </form>

                                    <p class="mb-0 mt-3">Don't have an account? <a href="sign-up.html">Sign up</a></p>
                                </div>
                            </div>
                        </div>
                        <!-- End Auth Box -->
                    </div>
                    <!-- End Login Form Column -->

                    <!-- Start Additional Column -->
                    <div class="col-12 col-md-6 d-md-block d-none">
                        <!-- Start Auth Box -->
                        <div class="auth-box-right">
                            <img src="{{ asset('images/authentication/login-hero.svg') }}" alt="Login Image">
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
