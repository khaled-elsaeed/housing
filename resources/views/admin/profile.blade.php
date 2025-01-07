@extends('layouts.admin')

@section('title', @lang('My Profile'))

@section('content')
<!-- Start row -->
<div class="row">
    <!-- Start col for sidebar -->
    <div class="col-lg-5 col-xl-3">
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('My Account')</h5>
            </div>
            <div class="card-body">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <!-- Profile Tab -->
                    <a class="nav-link mb-2 active" id="v-pills-profile-tab" data-bs-toggle="pill"
                       href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                        <i class="feather icon-user me-2"></i>@lang('My Profile')
                    </a>
                    <!-- Notifications Tab -->
                    <a class="nav-link mb-2" id="v-pills-notifications-tab" data-bs-toggle="pill"
                       href="#v-pills-notifications" role="tab" aria-controls="v-pills-notifications"
                       aria-selected="false">
                        <i class="feather icon-bell me-2"></i>@lang('Notifications')
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End col for sidebar -->

    <!-- Start col for profile content -->
    <div class="col-lg-7 col-xl-9">
        <div class="tab-content" id="v-pills-tabContent">

            <!-- My Profile Tab -->
            <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel"
                 aria-labelledby="v-pills-profile-tab">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('My Profile')</h5>
                    </div>
                    <div class="card-body">
                        <div class="profilebox pt-4 text-center">
                            <!-- Profile Image -->
                            <img src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('images/users/boy.svg') }}" 
                                 class="img-fluid mb-3 rounded-circle" style="width: 150px; height: 150px; object-fit: cover;" alt="user">

                            <!-- Profile Picture Actions -->
                            <ul class="list-inline">
                                <!-- Edit Profile Picture Button (Open file input dialog) -->
                                <li class="list-inline-item">
                                    <a href="#" class="btn btn-success-rgba font-18" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                                        <i class="feather icon-edit"></i> @lang('Change Picture')
                                    </a>
                                </li>
                                
                                <!-- Delete Profile Picture Button -->
                                @if($user->profile_picture)
                                    <li class="list-inline-item">
                                        <form action="{{ route('admin.profile.delete-picture') }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-rgba font-18">
                                                <i class="feather icon-trash"></i> @lang('Delete Picture')
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <!-- Modal for Changing Profile Picture -->
                    <div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="profilePicModalLabel">@lang('Change Profile Picture')</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('admin.profile.update-picture') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="profile_picture" class="form-label">@lang('Select New Profile Picture')</label>
                                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">@lang('Upload New Picture')</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Information -->
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Edit Profile Information')</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-3" method="POST" action="{{ route('admin.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <div class="col-md-6">
                                <label for="first_name">@lang('First Name')</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="{{ $user->first_name_en }}">
                            </div>
                            <div class="col-md-6">
                                <label for="last_name">@lang('Last Name')</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="{{ $user->last_name_en }}">
                            </div>
                            <div class="col-md-6">
                                <label for="email">@lang('Email')</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="{{ $user->email }}">
                            </div>

                            <!-- Password Fields -->
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary mb-3" id="toggle-password-btn">
                                    @lang('Change Password')
                                </button>
                            </div>
                            <div id="password-section" style="display: none;">
                                <div class="col-md-6">
                                    <label for="password">@lang('Password')</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           placeholder="@lang('Enter new password')">
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation">@lang('Confirm Password')</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                           name="password_confirmation" placeholder="@lang('Confirm new password')">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary font-16">
                                <i class="feather icon-save me-2"></i>@lang('Update')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End My Profile Tab -->

            <!-- My Notifications Tab -->
            <div class="tab-pane fade" id="v-pills-notifications" role="tabpanel"
                 aria-labelledby="v-pills-notifications-tab">
                <div class="card m-b-30">
                    <div class="card-header">
                        <h5 class="card-title mb-0">@lang('Notifications')</h5>
                    </div>
                    <div class="card-body">
                        <div class="ecom-notification-box">
                            <ul class="list-unstyled">
                                @forelse ($notifications as $notification)
                                    <li class="d-flex p-2 mt-1">
                                        <span class="me-3 action-icon badge 
                                            {{ $notification->type == 'success' ? 'badge-success-inverse' : 'badge-danger-inverse' }}">
                                            <i class="feather icon-{{ $notification->type == 'success' ? 'check' : 'alert-circle' }}"></i>
                                        </span>
                                        <div class="media-body">
                                            <h5 class="action-title">{{ $notification->title }}</h5>
                                            <p class="my-3">{{ $notification->message }}</p>
                                            <p>
                                                <span class="badge badge-info-inverse me-2">{{ strtoupper($notification->category) }}</span>
                                                <span class="timing">{{ $notification->created_at->format('d M Y, h:i A') }}</span>
                                            </p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-2">@lang('No notifications available.')</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End My Notifications Tab -->
        </div>
    </div>
    <!-- End col for profile content -->
</div>
<!-- End row -->

<script>
    document.getElementById('toggle-password-btn').addEventListener('click', function() {
        const passwordSection = document.getElementById('password-section');
        if (passwordSection.style.display === 'none' || passwordSection.style.display === '') {
            passwordSection.style.display = 'block';
            this.textContent = 'Cancel Password Change';
        } else {
            passwordSection.style.display = 'none';
            this.textContent = 'Change Password';
        }
    });
</script>
@endsection
