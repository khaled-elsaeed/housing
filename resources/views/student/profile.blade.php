@extends('layouts.student')
@section('title', __('My Profile'))
@section('content')
<!-- Start row -->
<div class="row">
    <div class="row">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
    </div>
    <!-- End row -->
    <!-- Start col for sidebar -->
    <div class="col-lg-5 col-xl-3">
        <div class="card m-b-30">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('My Account')</h5>
            </div>
            <div class="card-body">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <!-- Profile Tab (Always visible) -->
                    <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill"
                        href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                    <i class="feather icon-user me-2"></i>@lang('Profile')
                    </a>
                    <!-- My Address Tab -->
                    @if(optional($user->student->governorate)->name_en || $user->student->street)
                    <a class="nav-link mb-2" id="v-pills-address-tab" data-bs-toggle="pill"
                        href="#v-pills-address" role="tab" aria-controls="v-pills-address" aria-selected="false">
                    <i class="feather icon-map-pin me-2"></i>@lang('Address')
                    </a>
                    @endif
                    <!-- Academic Info Tab -->
                    @if(optional($user->student)->program)
                    <a class="nav-link mb-2" id="v-pills-academic-info-tab" data-bs-toggle="pill"
                        href="#v-pills-academic-info" role="tab" aria-controls="v-pills-academic-info" aria-selected="false">
                    <i class="feather icon-book-open me-2"></i>@lang('Academic Info')
                    </a>
                    @endif
                    <!-- Parent Info Tab -->
                    @if(optional($user->parent)->relation)
                    <a class="nav-link mb-2" id="v-pills-parent-info-tab" data-bs-toggle="pill"
                        href="#v-pills-parent-info" role="tab" aria-controls="v-pills-parent-info" aria-selected="false">
                    <i class="feather icon-users me-2"></i>@lang('Parent Info')
                    </a>
                    @endif
                    <!-- Sibling Info Tab -->
                    @if(optional($user->sibling)->gender && $user->sibling)
                    <a class="nav-link mb-2" id="v-pills-sibling-info-tab" data-bs-toggle="pill"
                        href="#v-pills-sibling-info" role="tab" aria-controls="v-pills-sibling-info" aria-selected="false">
                    <i class="feather icon-users me-2"></i>@lang('Sibling Info')
                    </a>
                    @endif
                    <!-- Emergency Info Tab -->
                    @if(optional($user->parent)->living_abroad == 1 && optional($user->EmergencyContact) || optional($user->parent)->living_abroad !== null)
                    <a class="nav-link mb-2" id="v-pills-emergency-info-tab" data-bs-toggle="pill"
                        href="#v-pills-emergency-info" role="tab" aria-controls="v-pills-emergency-info" aria-selected="false">
                    <i class="feather icon-alert-triangle me-2"></i>@lang('Emergency Info')
                    </a>
                    @endif
                    <!-- Reservations Info Tab -->
                    @if(optional($user->reservations) )
                    <a class="nav-link mb-2" id="v-pills-reservation-info-tab" data-bs-toggle="pill"
                        href="#v-pills-reservation-info" role="tab" aria-controls="v-pills-reservation-info" aria-selected="false">
                    <i class="feather icon-calendar me-2"></i>@lang('Reservations')
                    </a>
                    @endif
                    <!-- Payments Info Tab -->
                    @if($user->reservations)
                    <a class="nav-link mb-2" id="v-pills-payments-info-tab" data-bs-toggle="pill"
                        href="#v-pills-payments-info" role="tab" aria-controls="v-pills-payments-info" aria-selected="false">
                    <i class="feather icon-credit-card me-2"></i>@lang('Payments Info')
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- End col for sidebar -->
    <!-- Start col for profile content -->
    <div class="col-lg-7 col-xl-9">
        <div class="tab-content" id="v-pills-tabContent">
            <!-- My Profile Tab -->
            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                <div class="card shadow-sm">
                    <!-- Card Header -->
                    <div class="card-header bg-primary text-white p-4 position-relative">
                        <div class="position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-user-circle me-2"></i> @lang('My Profile')
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
                            <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/users/boy.svg') }}" 
                                class="img-fluid mb-3 rounded-circle shadow-sm" 
                                style="width: 150px; height: 150px; object-fit: cover;" 
                                alt="@lang('user')">
                            <!-- Action Buttons -->
                            <ul class="list-inline mb-0">
                                <li class="list-inline-item">
                                    <a href="#" class="btn btn-success btn-sm font-16" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                                    <i class="fa fa-edit me-2"></i> @lang('Change Picture')
                                    </a>
                                </li>
                                @if($user->profile_picture)
                                <li class="list-inline-item">
                                    <form action="{{ route('student.profile.delete-picture') }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm font-16" 
                                            onclick="return confirm('@lang('Are you sure you want to delete your profile picture?')')">
                                        <i class="fa fa-trash-alt me-2"></i> @lang('Delete Picture')
                                        </button>
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <!-- Edit Profile Information Form -->
                        <form class="row g-3" method="POST" action="{{ route('student.profile.update') }}">
                            @csrf
                            @method('PUT')
                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">@lang('First Name')</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                    value="{{ old('first_name', $user->first_name_en) }}" required>
                            </div>
                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">@lang('Last Name')</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                    value="{{ old('last_name', $user->last_name_en) }}" required>
                            </div>
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">@lang('Email')</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                    value="{{ old('email', $user->email) }}" required>
                            </div>
                            <!-- Password Section -->
                            <div class="col-md-12">
                                <button type="button" class="btn btn-outline-secondary mb-3" id="toggle-password-btn">
                                <i class="fa fa-lock me-2"></i> @lang('Change Password')
                                </button>
                            </div>
                            <div id="password-section" class="row g-3" style="display: none;">
                                <div class="col-md-6">
                                    <label for="password" class="form-label">@lang('Password')</label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                        placeholder="@lang('Enter New Password')">
                                </div>
                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">@lang('Confirm Password')</label>
                                    <input type="password" class="form-control" id="password_confirmation" 
                                        name="password_confirmation" placeholder="@lang('Confirm New Password')">
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-12">
                                <button type="submit" class="btn btn-secondary w-50">
                                <i class="fa fa-save me-2"></i> @lang('Update Profile')
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
                                <h5 class="modal-title" id="profilePicModalLabel">@lang('Change Profile Picture')</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('student.profile.update-picture') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">@lang('Select New Picture')</label>
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">@lang('Upload New Picture')</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End My Profile Tab -->
            <!-- My Address Tab -->
            <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
                <div class="card m-b-30">
                    <!-- Header with image banner -->
                    <div class="card-header bg-primary p-4 position-relative">
                        <div class="text-white position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-building me-2"></i>@lang('My Address')
                            </h4>
                        </div>
                        <!-- Decorative pattern overlay -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('student.updateAddress') }}">
                            @csrf
                            @method('PUT')
                            @if(!empty($user->student->governorate) && !empty($user->student->governorate->name_en))
                            <div class="mb-3">
                                <label for="governorate">@lang('Governorate')</label>
                                <input type="text" class="form-control" id="governorate" name="governorate" 
                                    value="{{ app()->getLocale() == 'en' ? $user->student->governorate->name_en : $user->student->governorate->name_ar }}" disabled>
                            </div>
                            @endif
                            @if(!empty($user->student->city) && !empty($user->student->city->name_en))
                            <div class="mb-3">
                                <label for="city">@lang('City')</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                    value="{{ app()->getLocale() == 'en' ? $user->student->city->name_en : $user->student->city->name_ar }}" disabled>
                            </div>
                            @endif
                            @if(!empty($user->student->street))
                            <div class="mb-3">
                                <label for="street">@lang('Street')</label>
                                <input type="text" class="form-control" id="street" name="street" 
                                    value="{{ $user->student->street }}" disabled>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- Academic Info Tab -->
            <div class="tab-pane fade" id="v-pills-academic-info" role="tabpanel" aria-labelledby="v-pills-academic-info-tab">
                <div class="card m-b-30">
                    <!-- Header with image banner -->
                    <div class="card-header bg-primary p-4 position-relative">
                        <div class="text-white position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-building me-2"></i>@lang('Academic Info')
                            </h4>
                        </div>
                        <!-- Decorative pattern overlay -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">@lang('Manage your academic details.')</p>
                        <form class="row g-3" method="POST" action="{{ route('student.updateAcademicInfo') }}">
                            @csrf
                            @method('PUT')
                            @if(!empty(optional($user->student)->faculty))
                            <div class="col-md-6">
                                <label for="faculty">@lang('Faculty')</label>
                                <input type="text" class="form-control" id="faculty" name="faculty" 
                                    value="{{ app()->getLocale() == 'en' ? optional($user->student)->faculty->name_en : optional($user->student)->faculty->name_ar }}" disabled>
                                <input type="hidden" name="faculty_id" value="{{ optional($user->student)->faculty_id }}">
                            </div>
                            @endif
                            @if(!empty(optional($user->student)->program))
                            <div class="col-md-6">
                                <label for="program">@lang('Program')</label>
                                <input type="text" class="form-control" id="program" name="program" 
                                    value="{{ app()->getLocale() == 'en' ? optional($user->student)->program->name_en : optional($user->student)->program->name_ar }}" disabled>
                                <input type="hidden" name="program_id" value="{{ optional($user->student)->program_id }}">
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <!-- Parent Info -->
            <div class="tab-pane fade" id="v-pills-parent-info" role="tabpanel" aria-labelledby="v-pills-parent-info-tab">
                <div class="card m-b-30">
                    <div class="card-header">
                        <!-- Header with image banner -->
                        <div class="card-header bg-primary p-4 position-relative">
                            <div class="text-white position-relative z-2">
                                <h4 class="card-title mb-0 fw-bold">
                                    <i class="fa fa-building me-2"></i>@lang('Parent Information')
                                </h4>
                            </div>
                            <!-- Decorative pattern overlay -->
                            <div class="position-absolute top-0 end-0 p-3">
                                <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($user->parent)  <!-- Check if parent record exists -->
                            <form method="POST" action="{{ route('student.updateParentInfo') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Parent Name -->
                                    <div class="col-md-6">
                                        <label for="parent_name">@lang('Parent Name')</label>
                                        <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                            value="{{ old('parent_name', $user->parent->name) }}" disabled>
                                    </div>
                                    <!-- Parent Relation -->
                                    <div class="col-md-6">
                                        <label for="parent_relation">@lang('Parent Relation')</label>
                                        <input type="text" class="form-control" id="parent_relation" name="parent_relation" 
                                            value="{{ old('parent_relation', $user->parent->relation) }}" disabled>
                                    </div>
                                    <!-- Parent Email -->
                                    <div class="col-md-6">
                                        <label for="parent_email">@lang('Parent Email')</label>
                                        <input type="email" class="form-control" id="parent_email" name="parent_email" 
                                            value="{{ old('parent_email', $user->parent->email) }}" disabled>
                                    </div>
                                    <!-- Parent Mobile -->
                                    <div class="col-md-6">
                                        <label for="parent_mobile">@lang('Parent Mobile')</label>
                                        <input type="text" class="form-control" id="parent_mobile" name="parent_mobile" 
                                            value="{{ old('parent_mobile', $user->parent->mobile) }}" disabled>
                                    </div>
                                    <!-- Living Abroad -->
                                    <div class="col-md-6">
                                        <label for="parent_living_abroad">@lang('Living Abroad')</label>
                                        <input type="text" class="form-control" id="parent_living_abroad" name="parent_living_abroad" 
                                            value="{{ old('parent_living_abroad', $user->parent->living_abroad == 1 ? 'Yes' : 'No') }}" disabled>
                                    </div>
                                    <!-- Living With parents -->
                                    <div class="col-md-6">
                                        <label for="parent_living_with">@lang('Living With parents')</label>
                                        <input type="text" class="form-control" id="parent_living_with" name="parent_living_with" 
                                            value="{{ old('parent_living_with', $user->parent->living_with == 1 ? 'Yes' : 'No') }}" disabled>
                                    </div>

                                    @if($user->parent->living_with == 0) <!-- Only show if living_with is 0 (no) -->
                                        <div class="col-md-6">
                                            <label for="parent_city">@lang('Parent City')</label>
                                            <input type="text" class="form-control" id="parent_city" name="parent_city" 
                                                value="{{ old('parent_city', $user->parent->city ?? '') }}">
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="parent_governorate">@lang('Parent Governorate')</label>
                                            <input type="text" class="form-control" id="parent_governorate" name="parent_governorate" 
                                                value="{{ old('parent_governorate', $user->parent->governorate ?? '') }}">
                                        </div>
                                    @endif
                                </div>
                            </form>
                            @else
                            <p>@lang('No parent information available')</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sibling Info -->
            <div class="tab-pane fade" id="v-pills-sibling-info" role="tabpanel" aria-labelledby="v-pills-sibling-info-tab">
                <div class="card m-b-30">
                    <!-- Header with image banner -->
                    <div class="card-header bg-primary p-4 position-relative">
                        <div class="text-white position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-building me-2"></i>@lang('Sibling Information')
                            </h4>
                        </div>
                        <!-- Decorative pattern overlay -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($user->sibling)  <!-- Check if sibling record exists -->
                        <form method="POST" action="{{ route('student.updateOrCreateSiblingInfo') }}">
                            @csrf
                            <div class="row">
                                <!-- Sibling Gender -->
                                <div class="col-md-6">
                                    <label for="sibling_gender">@lang('Sibling Gender')</label>
                                    <select class="form-control" id="sibling_gender" name="sibling_gender">
                                    <option value="brother" {{ old('sibling_gender', $user->sibling->gender) == 'brother' ? 'selected' : '' }}>@lang('Brother')</option>
                                    <option value="sister" {{ old('sibling_gender', $user->sibling->gender) == 'sister' ? 'selected' : '' }}>@lang('Sister')</option>
                                    </select>
                                </div>
                                <!-- Sibling Name -->
                                <div class="col-md-6">
                                    <label for="sibling_name">@lang('Sibling Name')</label>
                                    <input type="text" class="form-control" id="sibling_name" name="sibling_name" value="{{ old('sibling_name', $user->sibling->name) }}">
                                </div>
                                <!-- Sibling National ID -->
                                <div class="col-md-6">
                                    <label for="sibling_national_id">@lang('Sibling National ID')</label>
                                    <input type="text" class="form-control" id="sibling_national_id" name="sibling_national_id" value="{{ old('sibling_national_id', $user->sibling->national_id) }}">
                                </div>
                                <!-- Sibling Faculty -->
                                <div class="col-md-6">
                                    <label for="sibling_faculty">@lang('Sibling Faculty')</label>
                                    <input type="text" class="form-control" id="sibling_faculty" name="sibling_faculty" value="{{ old('sibling_faculty', optional($user->sibling->faculty)->name_en) }}">
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <div class="col-md-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary">@lang('Save Changes')</button>
                            </div>
                        </form>
                        @else
                        <p class="text-muted">@lang('No sibling information available')</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Sibling Info Tab -->
            <!-- Emergency Info -->
            <div class="tab-pane fade" id="v-pills-emergency-info" role="tabpanel" aria-labelledby="v-pills-emergency-info-tab">
                <div class="card m-b-30">
                    <!-- Header with image banner -->
                    <div class="card-header bg-primary p-4 position-relative">
                        <div class="text-white position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-building me-2"></i>@lang('Emergency Information')
                            </h4>
                        </div>
                        <!-- Decorative pattern overlay -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($user->emergencyContact)  <!-- Check if emergency contact data exists -->
                        <form method="POST" action="{{ route('student.updateOrCreateEmergencyInfo') }}">
                            @csrf
                            <div class="row">
                                <!-- Emergency Contact Name -->
                                <div class="col-md-6">
                                    <label for="emergency_contact_name">@lang('Emergency Contact Name')</label>
                                    <input type="text" class="form-control" id="emergency_contact_name"
                                        name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergencyContact->name ?? '') }}">
                                </div>
                                <!-- Emergency Phone -->
                                <div class="col-md-6">
                                    <label for="emergency_phone">@lang('Emergency Phone')</label>
                                    <input type="text" class="form-control" id="emergency_phone" name="emergency_phone"
                                        value="{{ old('emergency_phone', $user->emergencyContact->phone ?? '') }}">
                                </div>
                                <!-- Relationship Dropdown -->
                                <div class="col-md-6">
                                    <label for="relationship">@lang('Relationship')</label>
                                    <select class="form-control" id="relationship" name="relationship">
                                    <option value="uncle" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'uncle' ? 'selected' : '' }}>@lang('Uncle')</option>
                                    <option value="aunt" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'aunt' ? 'selected' : '' }}>@lang('Aunt')</option>
                                    <option value="grandparent" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'grandparent' ? 'selected' : '' }}>@lang('Grandparent')</option>
                                    <option value="other" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'other' ? 'selected' : '' }}>@lang('Other')</option>
                                    <option value="spouse" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'spouse' ? 'selected' : '' }}>@lang('Spouse')</option>
                                    <option value="nephew" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'nephew' ? 'selected' : '' }}>@lang('Nephew')</option>
                                    <option value="cousin" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'cousin' ? 'selected' : '' }}>@lang('Cousin')</option>
                                    <option value="niece" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'niece' ? 'selected' : '' }}>@lang('Niece')</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        @else
                        <p class="text-muted">@lang('No emergency contact information available')</p>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End Emergency Info Tab -->
            <!-- Reservation Info -->
            <div class="tab-pane fade" id="v-pills-reservation-info" role="tabpanel" aria-labelledby="v-pills-reservation-info-tab">
                <div class="card border-0 shadow-lg">
                    <!-- Header with image banner -->
                    <div class="card-header bg-primary p-4 position-relative">
                        <div class="text-white position-relative z-2">
                            <h4 class="card-title mb-0 fw-bold">
                                <i class="fa fa-building me-2"></i>@lang('Reservation Information')
                            </h4>
                        </div>
                        <!-- Decorative pattern overlay -->
                        <div class="position-absolute top-0 end-0 p-3">
                            <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="row g-4">
    @if($reservations && $reservations->count() > 0)
        @foreach($reservations as $reservation)
            <div class="col-md-6 col-xl-4">
                <article class="card h-100 border-0" style="box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);">
                    <!-- Card Header -->
                    <header class="card-header bg-secondary py-3 position-relative">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="text-white small m-0 d-flex align-items-center" id="reservation-title-{{ $loop->iteration }}">
                                <i class="feather icon-file-text mr-2" aria-hidden="true"></i>
                                <span>Reservation #{{ $loop->iteration }}</span>
                            </h3>
                            <span class="badge badge-pill 
    @if($reservation->status === 'completed') badge-success
    @elseif($reservation->status === 'upcoming') badge-info
    @elseif($reservation->status === 'cancelled') badge-danger
    @else badge-warning
    @endif px-3" role="status">
    <i class="feather icon-circle mr-1" style="font-size: 8px;"></i>
    {{ ucfirst($reservation->status) }}
</span>
                        </div>
                    </header>

                    <!-- Card Body -->
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" aria-label="Reservation Details">
                            <!-- Room Information -->
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light" style="width: 46px; height: 46px;">
                                        <i class="feather icon-home text-primary" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
    <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Location</h6>
    <div class="font-weight-bold" style="font-size: 0.95rem;">
        Room {{ $reservation->room->number ?? 'Not specified' }}
    </div>
    <div class="mt-1 d-flex align-items-center" style="font-size: 0.8rem;">
        <span class="text-muted d-flex align-items-center">
            <i class="feather icon-map-pin me-1" style="font-size: 12px;"></i>
            Building {{ $reservation->room->apartment->building->number ?? 'N/A' }}
        </span>
        <span class="mx-2 text-muted">•</span>
        <span class="text-muted d-flex align-items-center">
            <i class="feather icon-home me-1" style="font-size: 12px;"></i>
            Apt {{ $reservation->room->apartment->number ?? 'N/A' }}
        </span>
    </div>
</div>
                                </div>
                            </div>

                            <!-- Period Information -->
                            <div class="list-group-item border-0 py-3" style="background-color: #f8f9fa;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light" style="width: 46px; height: 46px;">
                                        <i class="feather icon-clock text-primary" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Period</h6>
                                        <div class="font-weight-bold" style="font-size: 0.95rem;">
                                            @if($reservation->period_type === 'long')
                                                {{ optional($reservation->academicTerm)->name. ' - '.optional($reservation->academicTerm)->academic_year ?? 'Not specified' }}
                                            @else
                                                <span class="d-flex align-items-center">
                                                    Short Term
                                                    <i class="feather icon-zap ml-2 text-warning" style="font-size: 14px;"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Duration Information -->
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light" style="width: 46px; height: 46px;">
                                        <i class="feather icon-calendar text-primary" style="font-size: 1.25rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Duration</h6>
                                        <div class="font-weight-bold" style="font-size: 0.95rem;">
                                            <div class="d-flex align-items-center">
                                                <time datetime="{{ $reservation->start_date }}">{{ $reservation->formatted_start_date }}</time>
                                                <i class="feather icon-arrow-right text-muted mx-2" style="font-size: 14px;"></i>
                                                <time datetime="{{ $reservation->end_date }}">{{ $reservation->formatted_end_date }}</time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <footer class="card-footer bg-white border-top py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center text-muted" style="font-size: 0.85rem;">
                                <i class="feather icon-refresh-cw me-2"></i>
                                <time datetime="{{ $reservation->updated_at->toISOString() }}">
                                    {{ $reservation->updated_at->diffForHumans() }}
                                </time>
                            </div>
                            @if($reservation->invoice)
                                <div class="d-flex align-items-center">
                                    <i class="feather icon-credit-card text-muted me-2" style="font-size: 14px;"></i>
                                    <span class="badge badge-pill {{ $reservation->invoice->status === 'paid' ? 'badge-success' : 'badge-warning' }} px-3">
                                        {{ ucfirst($reservation->invoice->status) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </footer>
                </article>
            </div>
        @endforeach
    @else
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center m-0" role="alert">
                <i class="feather icon-info mr-2" style="font-size: 1.25rem;"></i>
                <span>No reservations found.</span>
            </div>
        </div>
    @endif
</div>


</div>
                </div>
            </div>
            <!-- Payments Info Tab -->
            <!-- Payments Info Tab -->
            <div class="tab-pane fade" id="v-pills-payments-info" role="tabpanel" aria-labelledby="v-pills-payments-info-tab">
                <div class="card shadow border-0 rounded-lg">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">@lang('Payments Info')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Existing Invoice Cards -->
                            @foreach($invoices as $invoice)
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                <div class="card shadow-sm border-primary">
                                    <!-- Image Section -->
                                    <img class="card-img-top rounded-top" src="{{ asset('images/invoice/invoice.svg') }}" alt="@lang('Housing Fees')">
                                    <div class="card-body">
                                        <!-- Title and Subtitle -->
                                        <h5 class="card-title font-weight-bold text-primary">
                                            @lang('Housing') {{ $invoice->category }}
                                        </h5>
                                        <h6 class="card-subtitle mb-3 text-muted">
    @if ($invoice->reservation && $invoice->reservation->academicTerm)
        {{ $invoice->reservation->academicTerm->name }} - {{ $invoice->reservation->academicTerm->academic_year }}
    @else
        @lang('No academic term specified')
    @endif
</h6>
                                       <!-- Info Section: Total and Status -->
<div class="mb-2">
    <h6 class="text-secondary">
        <strong>@lang('Total:')</strong>
    </h6>
    <h6 class="text-success">
        {{ $invoice->totalAmount() }}
    </h6>
</div>
                                         <!-- Info Section: Total and Status -->
                                         <div class="d-flex justify-content-between mb-2">
                                           
                                            <p class="mb-0">
                                                <span class="badge rounded-pill {{ 
                                                    $invoice->status == 'paid' ? 'bg-success' : 
                                                    ($invoice->status == 'pending' ? 'bg-warning' : 'bg-danger') 
                                                }}">
                                                    <i class="fa fa-circle me-1 small"></i>
                                                    @lang(ucfirst($invoice->status))
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Footer with Buttons -->
                                    <div class="card-footer d-flex flex-column flex-sm-row justify-content-center align-items-center align-items-sm-center gap-2">
                                        @if($invoice->status == 'unpaid')
                                        <!-- Pay Now Button with Icon and Loading State -->
                                        <button class="btn btn-outline-primary btn-sm pay-now-btn" data-invoice-id="{{ $invoice->id }}">
                                            <span class="button-content">
                                                <i class="fa fa-credit-card"></i> @lang('Pay Now')
                                            </span>
                                            
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Payments Info Tab -->
            <!-- Modal for Reservation and Payment Details -->
            <div class="modal fade" id="invoiceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailsModalLabel" >
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="invoiceDetailsModalLabel">@lang('Reservation and Payment Details')</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Reservation Details Section -->
                            <h6><i class="fa fa-calendar-check"></i> @lang('Reservation Details')</h6>
                            <div class="mb-3">
                                <strong>@lang('Location:')</strong> <span id="location">@lang('Empty')</span>
                            </div>
                            <!-- Payment Details Section -->
                            <h6><i class="fa fa-credit-card"></i> @lang('Payment Details')</h6>
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center"><i class="fa fa-tag"></i> @lang('Category')</th>
                                        <th scope="col" class="text-center"><i class="fa fa-dollar-sign"></i> @lang('Amount')</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentDetails">
                                    <!-- Payment rows will be dynamically added here -->
                                </tbody>
                            </table>
                            <!-- Total Payment -->
                            <div class="d-flex justify-content-between">
                                <strong>@lang('Total:')</strong>
                                <span id="totalAmount" class="text-success">@lang('Empty')</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal for File Upload -->
            <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fileUploadModalLabel">@lang('Upload Payment File')</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                        </div>
                        <div class="modal-body">
                            <form id="fileUploadForm">
                                <!-- Payment Method Selection -->
                                <div class="mb-3">
                                    <label for="paymentMethod" class="form-label">@lang('Select Payment Method')</label>
                                    <select class="form-select" id="uploadPaymentMethod" name="payment_method" required>
                                        <option value="" disabled selected>@lang('Select Payment Method')</option>
                                        <option value="instapay">@lang('Instapay')</option>
                                        <option value="bank_transfer">@lang('Bank Statement')</option>
                                    </select>
                                </div>
                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label for="uploadInvoiceReceipt" class="form-label">@lang('Choose File')</label>
                                    <input type="file" class="form-control" id="uploadInvoiceReceipt" name="invoice-receipt" required>
                                </div>
                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary">@lang('Upload File')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End col for profile content -->
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        // Tab Navigation with URL Hash
        function activateTabFromHash() {
            let hash = window.location.hash;
            if (hash) {
                // Remove 'tab-' prefix if present
                hash = hash.replace('tab-', '');
                const $tabLink = $(`a[href="${hash}"]`);
                if ($tabLink.length) {
                    // Remove any existing active classes
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('show active');
                    
                    // Activate the correct tab
                    $tabLink.addClass('active');
                    $(hash).addClass('show active');
                }
            } else {
                // If no hash, activate profile tab by default
                $('#v-pills-profile-tab').addClass('active');
                $('#v-pills-profile').addClass('show active');
            }
        }
    
        // Update URL hash when tab changes
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            const hash = $(e.target).attr('href');
            if (history.pushState) {
                history.pushState(null, null, hash);
            } else {
                window.location.hash = hash;
            }
        });
    
        // Handle back/forward browser buttons
        $(window).on('popstate', function() {
            activateTabFromHash();
        });
    
        // Initial tab activation on page load
        activateTabFromHash();
        
        // File validation configuration
        const fileConfig = {
            maxSize: 4 * 1024 * 1024, // 4MB
            allowedTypes: ['image/jpeg', 'image/png'],
            allowedExtensions: ['.jpg', '.jpeg', '.png']
        };
    
        // Validate image file
        function validateImageFile(file, inputElement) {
            if (!file) {
                swal({
                    type: 'warning',
                    title: "@lang('No File Selected')",
                    text: "@lang('Please select an image file.')",
                    confirmButtonText: "@lang('OK')"
                });
                return false;
            }
    
            // Check file size
            if (file.size > fileConfig.maxSize) {
                swal({
                    type: 'error',
                    title: "@lang('File Too Large')",
                    text: "@lang('Image size must be less than 4MB')",
                    confirmButtonText: "@lang('OK')"
                });
                $(inputElement).val(''); // Clear the file input
                return false;
            }
    
            // Check file type
            if (!fileConfig.allowedTypes.includes(file.type)) {
                swal({
                    type: 'error',
                    title: "@lang('Invalid File Type')",
                    text: "@lang('Please upload only JPG or PNG images')",
                    confirmButtonText: "@lang('OK')"
                });
                $(inputElement).val(''); // Clear the file input
                return false;
            }
    
            return true;
        }
    
        // Password Toggle
        $('#toggle-password-btn').click(function() {
            const $passwordSection = $('#password-section');
            const isVisible = $passwordSection.is(':visible');
    
            $passwordSection.toggle();
            $(this).text(isVisible ? "@lang('Change Password')" : "@lang('Cancel Password Change')");
        });
    
        // Invoice Details
        $('.invoice-details-btn').click(function() {
            const invoiceId = $(this).data('invoice-id');
    
            $.ajax({
                url: "{{ route('student.payment.info') }}",
                method: 'POST',
                data: {
                    invoice_id: invoiceId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Update location
                    const location = response.reservation.location;
                    $('#location').text([
                        location.building,
                        location.apartment,
                        location.room
                    ].join('-') || "@lang('Empty')");
    
                    // Update payment details
                    let totalAmount = 0;
                    const $paymentDetails = $('#paymentDetails').empty();
    
                    response.paymentDetails.forEach(function(payment) {
                        totalAmount += parseFloat(payment.amount || 0);
                        $paymentDetails.append(`
                            <tr>
                                <td>${payment.category || "@lang('Empty')"}</td>
                                <td>${(payment.amount || 0)} @lang('USD')</td>
                            </tr>
                        `);
                    });
    
                    $('#totalAmount').text(`${totalAmount} @lang('USD')`);
    
                    // Show modal
                    $('#invoiceDetailsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error("@lang('Error fetching invoice details:')", error);
                    swal({
                        type: 'error',
                        title: "@lang('Error')",
                        text: "@lang('Failed to load invoice details. Please try again.')",
                        confirmButtonText: "@lang('OK')"
                    });
                }
            });
        });
    
        // File Upload for Pay Now with Loading State
        $('.pay-now-btn').click(function(e) {
    e.preventDefault();

    // Use $(this) to refer to the clicked button
    const $btn = $(this);
    const invoiceId = $btn.data('invoice-id');

    // Show modal and reset button state
    $('#fileUploadModal')
        .data('invoice-id', invoiceId)
        .modal('show');
});
    
        // Real-time image validation on file input change
        $('input[type="file"]').on('change', function() {
            const file = this.files[0];
            validateImageFile(file, this);
        });
    
        // Payment File Upload Form with Loading State
        $('#fileUploadForm').submit(function(e) {
            e.preventDefault();
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"]');
            const originalBtnText = $submitBtn.html();

            // Show loading state
            $submitBtn.prop('disabled', true)
                     .html('<i class="fa fa-spinner fa-spin"></i> @lang("Processing...")');

            const file = $('#uploadInvoiceReceipt')[0].files[0];
            const invoiceId = $('#fileUploadModal').data('invoice-id');

            if (!validateImageFile(file, '#uploadInvoiceReceipt')) {
                // Reset button state
                $submitBtn.prop('disabled', false).html(originalBtnText);
                return;
            }

            const formData = new FormData(this);
            formData.append('invoice_id', invoiceId);

            $.ajax({
                type: 'POST',
                url: "{{ route('student.invoice.pay') }}",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    swal({
                        type: 'success',
                        title: "@lang('Success')",
                        text: "@lang('Payment file has been uploaded successfully!')"
                    }).then((result) => {
                        $('#fileUploadModal').modal('hide');
                        $form[0].reset();
                        // Optionally refresh the page or update the UI
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    swal({
                        type: 'error',
                        title: "@lang('Error')",
                        text: "@lang('Failed to upload payment file. Please try again.')"
                    });
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    
        // Form validation
        $('form').on('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                swal({
                    type: 'warning',
                    title: "@lang('Validation Error')",
                    text: "@lang('Please fill in all required fields correctly.')",
                    confirmButtonText: "@lang('OK')"
                });
            }
            $(this).addClass('was-validated');
        });
    });
</script>
@endsection