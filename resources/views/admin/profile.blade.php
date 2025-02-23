@extends('layouts.admin')

@section('title', __('My Profile'))

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
                </div>
            </div>
        </div>
    </div>
    <!-- End col for sidebar -->

    <!-- Start col for profile content -->
    <div class="col-lg-7 col-xl-9">
        <div class="tab-content" id="v-pills-tabContent">

            <!-- My Profile Tab -->
            <div class="tab-pane active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white p-4 position-relative">
                        <div class="position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-user-circle me-2"></i> {{ __('My Profile') }}
                            </h4>
                        </div>
                        <!-- Decorative Pattern -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Profile Picture Section -->
                        <div class="text-center mb-5">
                            <img src="{{ $user->profilePicture() }}" 
                                 class="img-fluid mb-3 rounded-circle shadow-sm" 
                                 style="width: 150px; height: 150px; object-fit: cover;" 
                                 alt="{{ __('user') }}">
                            <!-- Action Buttons -->
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="#" class="btn btn-success btn-sm font-16" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                                        <i class="fa fa-edit me-2"></i> {{ __('Change Picture') }}
                                    </a>
                                </li>
                                @if($user->hasProfilePicture())
                                <li class="list-inline-item">
                                    <form action="{{ route('admin.profile.delete-picture') }}" method="POST" style="display:inline;" id="deleteProfilePictureForm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm font-16" id="deleteProfilePictureBtn">
                                            <i class="fa fa-trash-alt"></i> <!-- Font Awesome trash icon -->
                                            <span class="button-text">{{ __('Delete Picture') }}</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <!-- Edit Profile Information Form -->
                        <form class="row g-3" method="POST" action="{{ route('admin.profile.update') }}" id="updateProfileForm">
                            @csrf
                            @method('PUT')
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="{{ old('first_name', $user->first_name_en) }}" required>
                            </div>
                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="{{ old('last_name', $user->last_name_en) }}" required>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            <!-- Password Section -->
                            <div class="col-md-12">
                                <button type="button" class="btn btn-outline-secondary mb-3" id="toggle-password-btn">
                                    <i class="fa fa-lock me-2"></i> {{ __('Change Password') }}
                                </button>
                            </div>
                            <div id="password-section" class="row g-3" style="display: none;">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="{{ __('Enter New Password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" class="form-control" id="password_confirmation" 
                                           name="password_confirmation" placeholder="{{ __('Confirm New Password') }}">
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-secondary w-50" id="updateProfileBtn">
                                    <span class="button-text"> <i class="fa fa-save me-2"></i> {{ __('Update Profile') }}</span>
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Profile Picture Modal -->
                <div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="profilePicModalLabel">{{ __('Change Profile Picture') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="profilePictureForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">{{ __('Select New Picture') }}</label>
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                {{ __('Allowed formats') }}: JPG, PNG. {{ __('Max size') }}: 4MB
                                            </small>
                                        </div>
                                        <!-- Preview container -->
                                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                            <img src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="uploadProfilePicBtn">
                                        <span class="button-text">{{ __('Upload New Picture') }}</span>
                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
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
@endSection
@section('scripts')
<script>
    // === File Configuration ===
    const fileConfig = {
        maxSize: 4 * 1024 * 1024,
        allowedTypes: ['image/jpeg', 'image/png']
    };

    // === File Validation ===
    function validateImageFile(file) {
        if (!file) return false;
        if (file.size > fileConfig.maxSize) {
            swal({
                type: 'error',
                title: "{{ __('File Too Large') }}",
                text: "{{ __('Image size must be less than 4MB') }}"
            });
            return false;
        }
        if (!fileConfig.allowedTypes.includes(file.type)) {
            swal({
                type: 'error',
                title: "{{ __('Invalid File Type') }}",
                text: "{{ __('Please upload only JPG or PNG images') }}"
            });
            return false;
        }
        return true;
    }

    $('input[type="file"]').on('change', function() {
        if (this.files && this.files.length > 1) {
            Array.from(this.files).forEach(file => {
                validateImageFile(file);
            });
        } else if (this.files && this.files.length === 1) {
            validateImageFile(this.files[0]);
        }
    });

    // === Photo Preview ===
    const $photoPreviewContainer = $('#photoPreviewContainer');
    const $fileInput = $('#uploadInvoiceReceipt');
    const $fileLabel = $('.custom-file-label');

    $fileInput.on('change', function() {
        $photoPreviewContainer.empty();

        if (this.files && this.files.length > 0) {
            for (let i = 0; i < Math.min(this.files.length, 3); i++) {
                const file = this.files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Convert file size to human-readable format
                    const fileSize = file.size < 1024 * 1024 
                        ? `${(file.size / 1024).toFixed(2)} KB` 
                        : `${(file.size / (1024 * 1024)).toFixed(2)} MB`;

                    const $preview = $(`
                        <div class="photo-preview-item mb-2 position-relative">
                            <img src="${e.target.result}" alt="Preview" class="img-thumbnail">
                            <button type="button" class="btn btn-sm btn-danger position-absolute remove-preview-btn">
                                <i class="fa fa-times"></i>
                            </button>
                            <div class="file-size text-center mt-1">
                                <small class="text-muted">${fileSize}</small>
                            </div>
                        </div>
                    `);
                    $photoPreviewContainer.append($preview);
                };
                reader.readAsDataURL(file);
            }

            const fileCount = this.files.length;
            $fileLabel.text(fileCount > 1 ? `${fileCount} files selected` : this.files[0].name);
        } else {
            $fileLabel.text('{{ __("Choose files") }}');
        }
    });

    $photoPreviewContainer.on('click', '.remove-preview-btn', function() {
        $(this).parent().remove();
        if ($photoPreviewContainer.children().length === 0) {
            $fileInput.val('');
            $fileLabel.text('{{ __("Choose files") }}');
        }
    });

    // === Profile Management ===
    $('#toggle-password-btn').click(function() {
        $('#password-section').toggle();
        $(this).text(
            $('#password-section').is(':visible') 
            ? "{{ __('Cancel Password Change') }}" 
            : "{{ __('Change Password') }}"
        );
    });

    $('#profile_picture').on('change', function() {
        const file = this.files[0];
        if (file && validateImageFile(file)) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview')
                    .show()
                    .find('img')
                    .attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $(this).val('');
            $('#imagePreview').hide();
        }
    });

    $('#profilePictureForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#uploadProfilePicBtn');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.button-text');
        const formData = new FormData(this);

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text("{{ __('Uploading...') }}");

        $.ajax({
            url: "{{ route('admin.profile.update-picture') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.image_url) {
                    $('.profile-picture').attr('src', response.image_url);
                }
                swal({
                    type: 'success',
                    title: "{{ __('Success') }}",
                    text: "{{ __('Profile picture updated successfully!') }}"
                }).then(() => {
                    $('#profilePicModal').modal('hide');
                    window.location.reload();
                });
            },
            error: function(xhr) {
                swal({
                    type: 'error',
                    title: "{{ __('Error') }}",
                    text: xhr.responseJSON?.message || "{{ __('Failed to update profile picture') }}"
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text("{{ __('Upload New Picture') }}");
            }
        });
    });

    $('#updateProfileForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#updateProfileBtn');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.button-text');
        const formData = new FormData(this);

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text("{{ __('Updating...') }}");

        $.ajax({
            url: "{{ route('admin.profile.update') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                swal({
                    type: 'success',
                    title: "{{ __('Success') }}",
                    text: "{{ __('Profile data updated successfully!') }}"
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                swal({
                    type: 'error',
                    title: "{{ __('Error') }}",
                    text: xhr.responseJSON?.message || "{{ __('Failed to update profile data') }}"
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text("{{ __('Update Profile') }}");
            }
        });
    });

    $('#deleteProfilePictureForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $('#deleteProfilePictureBtn');
        const $spinner = $btn.find('.spinner-border');
        const $btnText = $btn.find('.button-text');
        const formData = new FormData(this);

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text("{{ __('Deleting...') }}");

        $.ajax({
            url: "{{ route('admin.profile.delete-picture') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                swal({
                    type: 'success',
                    title: "{{ __('Success') }}",
                    text: "{{ __('Profile picture deleted successfully!') }}"
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                swal({
                    type: 'error',
                    title: "{{ __('Error') }}",
                    text: xhr.responseJSON?.message || "{{ __('Failed to delete profile picture') }}"
                });
            },
            complete: function() {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text("{{ __('Delete Picture') }}");
            }
        });
    });
</script>
@endsection