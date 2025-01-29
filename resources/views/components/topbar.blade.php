<!-- Start Topbar -->
<div class="topbar">
    <!-- Start row -->
    <div class="row align-items-center">
        <!-- Start col -->
        <div class="col-md-12 align-self-center">
            <div class="togglebar">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <div class="menubar">
                            <a class="menu-hamburger" href="javascript:void();">
                                <img src="{{ asset('images/svg-icon/close.svg') }}" class="img-fluid menu-hamburger-close" alt="close">
                                <img src="{{ asset('images/svg-icon/collapse.svg') }}" class="img-fluid menu-hamburger-collapse" alt="collapse">
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            
            <div class="infobar">
                <ul class="list-inline mb-0">
                    <li class="list-inline-item">
                        <div class="language-switcher">
                            <div class="dropdown">
                                <a class="dropdown-toggle infobar-icon" href="#" role="button" id="languageSwitcher" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="flag-icon flag-icon-{{ $app->getLocale() == 'en' ? 'us' : 'eg' }}"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languageSwitcher">
                                    <a class="dropdown-item {{ $app->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('localization', ['local' => 'en']) }}">
                                        <i class="flag-icon flag-icon-us"></i> @lang('English')
                                    </a>
                                    <a class="dropdown-item {{ $app->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('localization', ['local' => 'ar']) }}">
                                        <i class="flag-icon flag-icon-eg"></i> @lang('العربية')
                                    </a>
                                </div>

                            </div>
                        </div>
                    </li>

                    <li class="list-inline-item">
                        <div class="notifybar">
                            <div class="dropdown">
                                <a class="dropdown-toggle infobar-icon" href="#" role="button" id="notificationlink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('images/svg-icon/notifications.svg') }}" class="img-fluid" alt="notifications">
                                    <span class="live-icon"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationlink">
                                    <div class="notification-dropdown-title">
                                        <h4>
                                            <i class="feather icon-bell"></i> @lang('Notifications')
                                        </h4>
                                    </div>
                                    <ul class="list-unstyled">
                                        @if(auth()->user()->notifications->isEmpty())
                                            <li class="d-flex p-2 mt-1 dropdown-item">
                                                <div class="media-body">
                                                    <p>@lang('No new notifications')</p>
                                                </div>
                                            </li>
                                        @else
                                            @foreach(auth()->user()->notifications as $notification)
                                                <li class="d-flex p-2 mt-1 dropdown-item">
                                                    <span class="action-icon badge badge-info">
                                                        <i class="feather icon-check-circle"></i>
                                                    </span>
                                                    <div class="media-body">
                                                        <h5 class="action-title">{{ $notification->data['title'] }}</h5>
                                                        <p>{{ $notification->data['message'] }}</p>
                                                        <p><span class="timing">{{ \Carbon\Carbon::parse($notification->created_at)->setTimezone('Africa/Cairo')->diffForHumans() }}</span></p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="list-inline-item">
                        <div class="profilebar">
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="profilelink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ auth()->user()->profilePicture() }}" class="img-fluid" alt="profile">
                                    <span class="feather icon-chevron-down live-icon"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profilelink">
                                    <div class="dropdown-item">
                                        <div class="profilename">
                                            <h5>{{ auth()->user()->username_en }}</h5>
                                        </div>
                                    </div>
                                    <div class="userbox">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex p-2 mt-1 dropdown-item">
                                                @if (Auth::user()->hasRole('admin'))
                                                    <a href="{{ route('admin.profile') }}" class="profile-icon">
                                                        <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="user"> @lang('My Profile')
                                                    </a>
                                                @elseif (Auth::user()->hasRole('resident'))
                                                    <a href="{{ route('student.profile') }}" class="profile-icon">
                                                        <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="user"> @lang('My Profile')
                                                    </a>
                                                @endif
                                            </li>
                                            <li class="d-flex p-2 mt-1 dropdown-item">
                                                <a href="javascript:void(0);" class="profile-icon" onclick="logout();">
                                                    <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" alt="logout"> @lang('Logout')
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End col -->
    </div>
    <!-- End row -->
</div>
<!-- End Topbar -->

<script>
function logout() {
    swal({
        title: "@lang('Are you sure you want to logout?')",
        text: "@lang('You will be logged out of your account.')",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "@lang('Yes, logout')",
        cancelButtonText: "@lang('Cancel')"
    }).then((result) => {
        // Create and submit the logout form
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('logout') }}";

        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    });
}

// Prevent navigating back after logout
window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href);
};
</script>
