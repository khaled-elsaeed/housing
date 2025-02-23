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
                                <img src="{{ asset('images/svg-icon/close.svg') }}" class="img-fluid menu-hamburger-close" alt="close" />
                                <img src="{{ asset('images/svg-icon/collapse.svg') }}" class="img-fluid menu-hamburger-collapse" alt="collapse" />
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="infobar">
                <!-- Use flex-row-reverse for RTL (Arabic) -->
                <ul class="list-inline mb-0 d-flex align-items-center justify-content-center {{ $app->getLocale() == 'ar' ? 'flex-row-reverse' : '' }}">
                    <!-- Language Switcher -->
                    <li class="list-inline-item">
                        <div class="language-switcher">
                            <div class="dropdown">
                                <a class="dropdown-toggle infobar-icon" href="#" role="button" id="languageSwitcher" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="flag-icon flag-icon-{{ $app->getLocale() == 'en' ? 'us' : 'eg' }}"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-{{ $app->getLocale() == 'ar' ? 'start' : 'end' }}" aria-labelledby="languageSwitcher">
                                    <a class="dropdown-item {{ $app->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('localization', ['local' => 'en']) }}"> <i class="flag-icon flag-icon-us"></i> @lang('English') </a>
                                    <a class="dropdown-item {{ $app->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('localization', ['local' => 'ar']) }}"> <i class="flag-icon flag-icon-eg"></i> @lang('العربية') </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Notification Section -->
                    <li class="list-inline-item">
                        <div class="notifybar">
                            <div class="dropdown">
                                <a class="dropdown-toggle infobar-icon" href="#" role="button" id="notificationlink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ asset('images/svg-icon/notifications.svg') }}" class="img-fluid" alt="notifications" />
                                    @if(auth()->user()->unreadNotifications->isNotEmpty())
                                    <span class="live-icon"></span>
                                    <!-- Only show if there are unread notifications -->
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-{{ $app->getLocale() == 'ar' ? 'start' : 'end' }}" aria-labelledby="notificationlink">
                                    <div class="notification-dropdown-title">
                                        <h4><i class="feather icon-bell"></i> @lang('Notifications')</h4>
                                        <a href="javascript:void(0);" class="mark-all-read" onclick="markAllAsRead()">@lang('Mark all as read')</a>
                                    </div>
                                    <ul class="list-unstyled">
                                        @if(auth()->user()->notifications->isEmpty())
                                        <li class="d-flex p-2 mt-1 dropdown-item">
                                            <div class="media-body">
                                                <p>@lang('No new notifications')</p>
                                            </div>
                                        </li>
                                        @else
                                        <!-- Unread Notifications -->
                                        @foreach(auth()->user()->unreadNotifications->take(5) as $notification)
                                        <li class="d-flex p-2 mt-1 dropdown-item unread-notification" data-id="{{ $notification->id }}" onclick="markAsRead('{{ $notification->id }}')">
                                            <span class="action-icon badge badge-info">
                                                <i class="feather icon-check-circle"></i>
                                            </span>
                                            <div class="media-body">
                                                <p>{{ $notification->data['message'] }}</p>
                                                <p><span class="timing">{{ \Carbon\Carbon::parse($notification->created_at)->setTimezone('Africa/Cairo')->diffForHumans() }}</span></p>
                                            </div>
                                        </li>
                                        @endforeach

                                        <!-- Read Notifications -->
                                        @foreach(auth()->user()->readNotifications->take(5) as $notification)
                                        <li class="d-flex p-2 mt-1 dropdown-item read-notification" data-id="{{ $notification->id }}">
                                            <span class="action-icon badge badge-secondary">
                                                <i class="feather icon-check-circle"></i>
                                            </span>
                                            <div class="media-body">
                                                <p>{{ $notification->data['message'] }}</p>
                                                <p><span class="timing">{{ \Carbon\Carbon::parse($notification->created_at)->setTimezone('Africa/Cairo')->diffForHumans() }}</span></p>
                                            </div>
                                        </li>
                                        @endforeach @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- Profile Section -->
                    <li class="list-inline-item">
                        <div class="profilebar">
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" role="button" id="profilelink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ auth()->user()->profilePicture() }}" class="img-fluid" alt="profile" />
                                    <span class="feather icon-chevron-down live-icon"></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-{{ $app->getLocale() == 'ar' ? 'start' : 'end' }}" aria-labelledby="profilelink">
                                    <div class="dropdown-item">
                                        <div class="profilename">
                                            <h5>{{ auth()->user()->getUsername() }}</h5>
                                        </div>
                                    </div>
                                    <div class="userbox">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex p-2 mt-1 dropdown-item">
                                                @if (Auth::user()->hasRole('admin'))
                                                <a href="{{ route('admin.profile') }}" class="profile-icon"> <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="user" /> @lang('My Profile') </a>
                                                @elseif (Auth::user()->hasRole('resident'))
                                                <a href="{{ route('student.profile') }}" class="profile-icon"> <img src="{{ asset('images/svg-icon/user.svg') }}" class="img-fluid" alt="user" /> @lang('My Profile') </a>
                                                @endif
                                            </li>
                                            <li class="d-flex p-2 mt-1 dropdown-item">
                                                <a href="javascript:void(0);" class="profile-icon" onclick="logout();"> <img src="{{ asset('images/svg-icon/logout.svg') }}" class="img-fluid" alt="logout" /> @lang('Logout') </a>
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
    // Mark a single notification as read
    function markAsRead(notificationId) {
        fetch("{{ route('notifications.markAsRead', ['id' => ':notificationId']) }}".replace(":notificationId", notificationId), {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const notificationElement = document.querySelector(`.unread-notification[data-id="${notificationId}"]`);
                    if (notificationElement) {
                        notificationElement.classList.remove("unread-notification");
                        notificationElement.classList.add("read-notification");
                        notificationElement.querySelector(".badge").classList.remove("badge-info");
                        notificationElement.querySelector(".badge").classList.add("badge-secondary");
                    }

                    // Hide the dot if there are no more unread notifications
                    const unreadNotifications = document.querySelectorAll(".unread-notification");
                    if (unreadNotifications.length === 0) {
                        const liveIcon = document.querySelector(".live-icon");
                        if (liveIcon) {
                            liveIcon.style.display = "none";
                        }
                    }
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    // Mark all notifications as read
    function markAllAsRead() {
        fetch("{{ route('notifications.markAllAsRead') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    document.querySelectorAll(".unread-notification").forEach((notificationElement) => {
                        notificationElement.classList.remove("unread-notification");
                        notificationElement.classList.add("read-notification");
                        notificationElement.querySelector(".badge").classList.remove("badge-info");
                        notificationElement.querySelector(".badge").classList.add("badge-secondary");
                    });

                    // Hide the dot after marking all as read
                    const liveIcon = document.querySelector(".live-icon");
                    if (liveIcon) {
                        liveIcon.style.display = "none";
                    }
                }
            })
            .catch((error) => console.error("Error:", error));
    }

    // Logout function
    function logout() {
        swal({
            title: "@lang('Are you sure you want to logout?')",
            text: "@lang('You will be logged out of your account.')",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willLogout) => {
            if (willLogout) {
                // Create and submit the logout form
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('logout') }}";

                var csrfInput = document.createElement("input");
                csrfInput.type = "hidden";
                csrfInput.name = "_token";
                csrfInput.value = "{{ csrf_token() }}";

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    // Prevent navigating back after logout
    window.onpopstate = function () {
        window.history.pushState(null, "", window.location.href);
    };
</script>
