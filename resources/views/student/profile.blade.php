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
      @endif @if (session('success'))
      <div class="alert alert-success">
         {{ session('success') }}
      </div>
      @endif @if (session('error'))
      <div class="alert alert-danger">
         {{ session('error') }}
      </div>
      @endif
   </div>
   <!-- End row -->
   <!-- Start sidebar -->
   <div class="col-lg-5 col-xl-3">
      <div class="card m-b-30">
         <div class="card-header">
            <h5 class="card-title mb-0">{{ __('My Account') }}</h5>
         </div>
         <div class="card-body">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
               <!-- Profile Tab (Always visible) -->
               <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill" href="#v-pills-profile"
                  role="tab" aria-controls="v-pills-profile" aria-selected="false">
               <i class="feather icon-user me-2"></i>{{ __('Profile') }}
               </a>
               <!-- My Address Tab -->
               @if (optional($user->student->governorate)->name_en || $user->student->street)
               <a class="nav-link mb-2" id="v-pills-address-tab" data-bs-toggle="pill" href="#v-pills-address"
                  role="tab" aria-controls="v-pills-address" aria-selected="false">
               <i class="feather icon-map-pin me-2"></i>{{ __('Address') }}
               </a>
               @endif
               <!-- Academic Info Tab -->
               @if (optional($user->student)->program)
               <a class="nav-link mb-2" id="v-pills-academic-info-tab" data-bs-toggle="pill"
                  href="#v-pills-academic-info" role="tab" aria-controls="v-pills-academic-info"
                  aria-selected="false">
               <i class="feather icon-book-open me-2"></i>{{ __('Academic Info') }}
               </a>
               @endif
               <!-- Parent Info Tab -->
               @if (optional($user->parent)->relation)
               <a class="nav-link mb-2" id="v-pills-parent-info-tab" data-bs-toggle="pill"
                  href="#v-pills-parent-info" role="tab" aria-controls="v-pills-parent-info"
                  aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('Parent Info') }}
               </a>
               @endif
               <!-- Sibling Info Tab -->
               @if (optional($user->sibling)->gender && $user->sibling)
               <a class="nav-link mb-2" id="v-pills-sibling-info-tab" data-bs-toggle="pill"
                  href="#v-pills-sibling-info" role="tab" aria-controls="v-pills-sibling-info"
                  aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('Sibling Info') }}
               </a>
               @endif
               <!-- Emergency Info Tab -->
               @if (optional($user->parent)->living_abroad == 1 && optional($user->EmergencyContact))
               <a class="nav-link mb-2" id="v-pills-emergency-info-tab" data-bs-toggle="pill"
                  href="#v-pills-emergency-info" role="tab" aria-controls="v-pills-emergency-info"
                  aria-selected="false">
               <i class="feather icon-alert-triangle me-2"></i>{{ __('Emergency Info') }}
               </a>
               @endif
               <!-- Reservations Info Tab -->
               @if (optional($user->reservations))
               <a class="nav-link mb-2" id="v-pills-reservation-info-tab" data-bs-toggle="pill"
                  href="#v-pills-reservation-info" role="tab" aria-controls="v-pills-reservation-info"
                  aria-selected="false">
               <i class="feather icon-calendar me-2"></i>{{ __('Reservations') }}
               </a>
               @endif
               <!-- Payments Info Tab -->
               @if ($user->reservations)
               <a class="nav-link mb-2" id="v-pills-payments-info-tab" data-bs-toggle="pill"
                  href="#v-pills-payments-info" role="tab" aria-controls="v-pills-payments-info"
                  aria-selected="false">
               <i class="feather icon-credit-card me-2"></i>{{ __('Payments') }}
               </a>
               @endif
            </div>
         </div>
      </div>
   </div>
   <!-- End sidebar -->
   <!-- Start tabs content -->
   <div class="col-lg-7 col-xl-9">
      <div class="tab-content" id="v-pills-tabContent">
         <!-- My Profile Tab -->
         <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            <div class="card shadow-sm">
               <!-- Card Header -->
               <div class="card-header bg-primary text-white p-4 position-relative">
                  <div class="position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i class="fa fa-user-circle me-2"></i>
                        {{ __('My Profile') }}
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
                     <img src="{{ $user->profilePicture() }}" class="img-fluid mb-3 rounded-circle shadow-sm"
                        style="width: 150px; height: 150px; object-fit: cover;" alt="{{ __('user') }}" />
                     <!-- Action Buttons -->
                     <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                           <a href="#" class="btn btn-success btn-sm font-16" data-bs-toggle="modal"
                              data-bs-target="#profilePicModal"> <i class="fa fa-edit me-2"></i>
                           {{ __('Change Picture') }} </a>
                        </li>
                        @if ($user->hasProfilePicture())
                        <li class="list-inline-item">
                           <form action="{{ route('student.profile.delete-picture') }}" method="POST"
                              style="display: inline;" id="deleteProfilePictureForm">
                              @csrf @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-sm font-16"
                                 id="deleteProfilePictureBtn">
                                 <i class="fa fa-trash-alt"></i>
                                 <!-- Font Awesome trash icon -->
                                 <span class="button-text">{{ __('Delete Picture') }}</span>
                                 <span class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                              </button>
                           </form>
                        </li>
                        @endif
                     </ul>
                  </div>
                  <!-- Edit Profile Information Form -->
                  <form class="row g-3" method="POST" action="{{ route('student.profile.update') }}" id="updateProfileForm">
    @csrf
    @method('PUT')

    <!-- First Name (English) -->
    <div class="col-md-6">
        <label for="first_name_en" class="form-label">{{ __('First Name (English)') }}</label>
        <input type="text" class="form-control border border-primary" id="first_name_en" name="first_name_en"
               value="{{ old('first_name_en', $user->first_name_en) }}" required dir="ltr" />
    </div>

    <!-- Last Name (English) -->
    <div class="col-md-6">
        <label for="last_name_en" class="form-label">{{ __('Last Name (English)') }}</label>
        <input type="text" class="form-control border border-primary" id="last_name_en" name="last_name_en"
               value="{{ old('last_name_en', $user->last_name_en) }}" required dir="ltr" />
    </div>

    <!-- First Name (Arabic) -->
    <div class="col-md-6">
        <label for="first_name_ar" class="form-label">{{ __('First Name (Arabic)') }}</label>
        <input type="text" class="form-control border border-primary" id="first_name_ar" name="first_name_ar"
               value="{{ old('first_name_ar', $user->first_name_ar) }}" required dir="rtl" />
    </div>

    <!-- Last Name (Arabic) -->
    <div class="col-md-6">
        <label for="last_name_ar" class="form-label">{{ __('Last Name (Arabic)') }}</label>
        <input type="text" class="form-control border border-primary" id="last_name_ar" name="last_name_ar"
               value="{{ old('last_name_ar', $user->last_name_ar) }}" required dir="rtl" />
    </div>

    <!-- Email -->
    <div class="col-md-6">
        <label for="email" class="form-label">{{ __('Email') }}</label>
        <input type="email" class="form-control border border-primary" id="email" name="email"
               value="{{ old('email', $user->email) }}" required dir="ltr" />
    </div>

    <!-- Password Section Toggle -->
    <div class="col-md-12">
        <button type="button" class="btn btn-outline-secondary mb-3" id="toggle-password-btn">
            <i class="fa fa-lock {{ App::getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
            {{ __('Change Password') }}
        </button>
    </div>

    <!-- Password Fields (Hidden by Default) -->
    <div id="password-section" class="row g-3" style="display: none;">
        <div class="col-md-6">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input type="password" class="form-control border border-primary" id="password" name="password"
                   placeholder="{{ __('Enter New Password') }}" dir="ltr" />
        </div>
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" class="form-control border border-primary" id="password_confirmation" name="password_confirmation"
                   placeholder="{{ __('Confirm New Password') }}" dir="ltr" />
        </div>
    </div>

    <!-- Submit Button -->
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-secondary w-50" id="updateProfileBtn">
            <span class="button-text">
                <i class="fa fa-save {{ App::getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Update Profile') }}
            </span>
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
                        <h5 class="modal-title" id="profilePicModalLabel">{{ __('Change Profile Picture') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                           aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                        <form id="profilePictureForm" enctype="multipart/form-data">
                           @csrf
                           <div class="mb-3">
                              <label for="profile_picture"
                                 class="form-label">{{ __('Select New Picture') }}</label>
                              <input type="file" class="form-control border border-primary" id="profile_picture"
                                 name="profile_picture" accept="image/*" required />
                              <div class="mt-2">
                                 <small class="text-muted">
                                 {{ __('Allowed formats') }}: JPG, PNG. {{ __('Max size') }}: 4MB
                                 </small>
                              </div>
                              <!-- Preview container -->
                              <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                 <img src="" alt="Preview" class="img-thumbnail"
                                    style="max-width: 200px;" />
                              </div>
                           </div>
                           <button type="submit" class="btn btn-primary w-100" id="uploadProfilePicBtn">
                           <span class="button-text">{{ __('Upload New Picture') }}</span>
                           <span class="spinner-border spinner-border-sm d-none" role="status"
                              aria-hidden="true"></span>
                           </button>
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
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('My Address') }}</h4>
                  </div>
                  <!-- Decorative pattern overlay -->
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  <form method="POST" action="{{ route('student.updateAddress') }}">
                     @csrf @method('PUT') @if (!empty($user->student->governorate) && !empty($user->student->governorate->name_en))
                     <div class="mb-3">
                        <label for="governorate">{{ __('Governorate') }}</label>
                        <input type="text" class="form-control border border-primary" id="governorate" name="governorate"
                           value="{{ app()->getLocale() == 'en' ? $user->student->governorate->name_en : $user->student->governorate->name_ar }}"
                           disabled />
                     </div>
                     @endif @if (!empty($user->student->city) && !empty($user->student->city->name_en))
                     <div class="mb-3">
                        <label for="city">{{ __('City') }}</label>
                        <input type="text" class="form-control border border-primary" id="city" name="city"
                           value="{{ app()->getLocale() == 'en' ? $user->student->city->name_en : $user->student->city->name_ar }}"
                           disabled />
                     </div>
                     @endif @if (!empty($user->student->street))
                     <div class="mb-3">
                        <label for="street">{{ __('Street') }}</label>
                        <input type="text" class="form-control border border-primary" id="street" name="street"
                           value="{{ $user->student->street }}" disabled />
                     </div>
                     @endif
                  </form>
               </div>
            </div>
         </div>
         <!-- Academic Info Tab -->
         <div class="tab-pane fade" id="v-pills-academic-info" role="tabpanel"
            aria-labelledby="v-pills-academic-info-tab">
            <div class="card m-b-30">
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('Academic Info') }}</h4>
                  </div>
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  <p class="text-muted">{{ __('Manage your academic details.') }}</p>
                  <form class="row g-3">
                     @if (!empty(optional($user->student)->faculty))
                     <div class="col-md-6">
                        <label for="faculty">{{ __('Faculty') }}</label>
                        <input type="text" class="form-control border border-primary" id="faculty"
                           value="{{ app()->getLocale() == 'en' ? optional($user->student)->faculty->name_en : optional($user->student)->faculty->name_ar }}"
                           disabled />
                     </div>
                     @endif @if (!empty(optional($user->student)->program))
                     <div class="col-md-6">
                        <label for="program">{{ __('Program') }}</label>
                        <input type="text" class="form-control border border-primary" id="program"
                           value="{{ app()->getLocale() == 'en' ? optional($user->student)->program->name_en : optional($user->student)->program->name_ar }}"
                           disabled />
                     </div>
                     @endif
                  </form>
               </div>
            </div>
         </div>
         <!-- Parent Info Tab -->
         <div class="tab-pane fade" id="v-pills-parent-info" role="tabpanel"
            aria-labelledby="v-pills-parent-info-tab">
            <div class="card m-b-30">
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('Parent Information') }}</h4>
                  </div>
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  @if ($user->parent)
                  <div class="row">
                     <div class="col-md-6">
                        <label for="parent_name">{{ __('Parent Name') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_name"
                           value="{{ $user->parent->name }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="parent_relation">{{ __('Parent Relation') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_relation"
                           value="{{ __($user->parent->relation) }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="parent_email">{{ __('Parent Email') }}</label>
                        <input type="email" class="form-control border border-primary" id="parent_email"
                           value="{{ $user->parent->email }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="parent_phone">{{ __('Parent Phone') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_phone"
                           value="{{ $user->parent->phone }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="parent_living_abroad">{{ __('Living Abroad') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_living_abroad"
                           value="{{ $user->parent->living_abroad == 1 ? __('Yes') : __('No') }}"
                           disabled />
                     </div>
                     @if ($user->parent->living_abroad === 0)
                     <div class="col-md-6">
                        <label for="parent_living_with">{{ __('Living With parents') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_living_with"
                           value="{{ $user->parent->living_with == 1 ? __('Yes') : __('No') }}"
                           disabled />
                     </div>
                     @if ($user->parent->living_with == 0)
                     <div class="col-md-6">
                        <label for="parent_city">{{ __('Parent City') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_city"
                           value="{{ $user->parent->city->name }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="parent_governorate">{{ __('Parent Governorate') }}</label>
                        <input type="text" class="form-control border border-primary" id="parent_governorate"
                           value="{{ $user->parent->governorate->name }}" disabled />
                     </div>
                     @endif
                     @endif
                  </div>
                  @else
                  <p>{{ __('No parent information available') }}</p>
                  @endif
               </div>
            </div>
         </div>
         <!-- Sibling Info Tab -->
         <div class="tab-pane fade" id="v-pills-sibling-info" role="tabpanel"
            aria-labelledby="v-pills-sibling-info-tab">
            <div class="card m-b-30">
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('Sibling Information') }}</h4>
                  </div>
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  @if ($user->sibling)
                  <div class="row">
                     <div class="col-md-6">
                        <label for="sibling_gender">{{ __('Sibling Gender') }}</label>
                        <input type="text" class="form-control border border-primary" id="sibling_gender"
                           value="{{ __('sibling'.$user->sibling->gender) }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="sibling_name">{{ __('Sibling Name') }}</label>
                        <input type="text" class="form-control border border-primary" id="sibling_name"
                           value="{{ $user->sibling->name }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="sibling_national_id">{{ __('Sibling National ID') }}</label>
                        <input type="text" class="form-control border border-primary" id="sibling_national_id"
                           value="{{ $user->sibling->national_id }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="sibling_faculty">{{ __('Sibling Faculty') }}</label>
                        <input type="text" class="form-control border border-primary" id="sibling_faculty"
                           value="{{ optional($user->sibling->faculty)->name }}" disabled />
                     </div>
                  </div>
                  @else
                  <p class="text-muted">{{ __('No sibling information available') }}</p>
                  @endif
               </div>
            </div>
         </div>
         <!-- Emergency Info Tab -->
         <div class="tab-pane fade" id="v-pills-emergency-info" role="tabpanel"
            aria-labelledby="v-pills-emergency-info-tab">
            <div class="card m-b-30">
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('Emergency Information') }}</h4>
                  </div>
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  @if ($user->emergencyContact)
                  <div class="row">
                     <div class="col-md-6">
                        <label for="emergency_contact_name">{{ __('Emergency Contact Name') }}</label>
                        <input type="text" class="form-control border border-primary" id="emergency_contact_name"
                           value="{{ $user->emergencyContact->name }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="emergency_phone">{{ __('Emergency Phone') }}</label>
                        <input type="text" class="form-control border border-primary" id="emergency_phone"
                           value="{{ $user->emergencyContact->phone }}" disabled />
                     </div>
                     <div class="col-md-6">
                        <label for="relationship">{{ __('Relationship') }}</label>
                        <input type="text" class="form-control border border-primary" id="relationship"
                           value="{{ __($user->emergencyContact->relation) }}" disabled />
                     </div>
                  </div>
                  @else
                  <p class="text-muted">{{ __('No emergency contact information available') }}</p>
                  @endif
               </div>
            </div>
         </div>
         <!-- End Emergency Info Tab -->
         <!-- Reservation Info Tab -->
         <div class="tab-pane fade" id="v-pills-reservation-info" role="tabpanel"
            aria-labelledby="v-pills-reservation-info-tab">
            <div class="card border-0 shadow-lg">
               <!-- Header with image banner -->
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold"><i
                        class="fa fa-building me-2"></i>{{ __('Reservation Information') }}</h4>
                  </div>
                  <!-- Decorative pattern overlay -->
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  <div class="row g-4">
                     @if ($reservations && $reservations->count() > 0)
                     @foreach ($reservations as $reservation)
                     <div class="col-md-6 col-xl-4">
                        <article class="card h-100 border-0"
                           style="box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
                           <!-- Card Header -->
                           <header class="card-header bg-secondary py-3 position-relative">
                              <div class="d-flex justify-content-between align-items-center">
                                 <h3 class="text-white small m-0 d-flex align-items-center"
                                    id="reservation-title-{{ $loop->iteration }}">
                                    <i class="feather icon-file-text mr-2" aria-hidden="true"></i>
                                    <span>{{ __('Reservation') }} #{{ $loop->iteration }}</span>
                                 </h3>
                                 <span
                                    class="badge badge-pill @if ($reservation->status === 'completed') badge-success @elseif($reservation->status === 'upcoming') badge-info @elseif($reservation->status === 'cancelled') badge-danger @else badge-warning @endif px-3"
                                    role="status">
                                 <i class="feather icon-circle mr-1"
                                    style="font-size: 8px;"></i>
                                 {{ __($reservation->status) }}
                                 </span>
                              </div>
                           </header>
                           <!-- Card Body -->
                           <div class="card-body p-0">
                              <div class="list-group list-group-flush"
                                 aria-label="Reservation Details">
                                 <!-- Room Information -->
                                 <div class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-center">
                                       <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light"
                                          style="width: 46px; height: 46px;">
                                          <i class="feather icon-home text-primary"
                                             style="font-size: 1.25rem;"></i>
                                       </div>
                                       <div class="flex-grow-1">
                                          <h6 class="text-uppercase text-muted mb-1"
                                             style="font-size: 11px; letter-spacing: 0.5px;">
                                             {{ __('Location') }}
                                          </h6>
                                          <div class="font-weight-bold"
                                             style="font-size: 0.95rem;">
                                             {{ __('Room') }}
                                             {{ $reservation->room->number ?? __('Not specified') }}
                                          </div>
                                          <div class="mt-1 d-flex align-items-center"
                                             style="font-size: 0.8rem;">
                                             <span class="text-muted d-flex align-items-center">
                                             <i class="feather icon-map-pin me-1"
                                                style="font-size: 12px;"></i>
                                             {{ __('Building') }}
                                             {{ $reservation->room->apartment->building->number ?? 'N/A' }}
                                             </span>
                                             <span class="mx-2 text-muted">•</span>
                                             <span class="text-muted d-flex align-items-center">
                                             <i class="feather icon-home me-1"
                                                style="font-size: 12px;"></i>
                                             {{ __('Apt') }}
                                             {{ $reservation->room->apartment->number ?? 'N/A' }}
                                             </span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- Period Information -->
                                 <div class="list-group-item border-0 py-3"
                                    style="background-color: #f8f9fa;">
                                    <div class="d-flex align-items-center">
                                       <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light"
                                          style="width: 46px; height: 46px;">
                                          <i class="feather icon-clock text-primary"
                                             style="font-size: 1.25rem;"></i>
                                       </div>
                                       <div class="flex-grow-1">
                                          <h6 class="text-uppercase text-muted mb-1"
                                             style="font-size: 11px; letter-spacing: 0.5px;">
                                             {{ __('Period') }}
                                          </h6>
                                          <div class="font-weight-bold"
                                             style="font-size: 0.95rem;">
                                             @if ($reservation->period_type === 'long')
                                             {{ __(optional($reservation->academicTerm)->name) . ' - ' . optional($reservation->academicTerm)->academic_year ?? __('Not specified') }}
                                             @else
                                             <span class="d-flex align-items-center">
                                             {{ __('Short Term') }}
                                             <i class="feather icon-zap ml-2 text-warning"
                                                style="font-size: 14px;"></i>
                                             </span>
                                             @endif
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <!-- Duration Information -->
                                 <div class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-center">
                                       <div class="rounded-lg mr-3 d-flex align-items-center justify-content-center bg-primary-light"
                                          style="width: 46px; height: 46px;">
                                          <i class="feather icon-calendar text-primary"
                                             style="font-size: 1.25rem;"></i>
                                       </div>
                                       <div class="flex-grow-1">
                                          <h6 class="text-uppercase text-muted mb-1"
                                             style="font-size: 11px; letter-spacing: 0.5px;">
                                             {{ __('Duration') }}
                                          </h6>
                                          <div class="font-weight-bold"
                                             style="font-size: 0.95rem;">
                                             <div
                                                class="d-flex align-items-center justify-content-center">
                                                <time
                                                   datetime="{{ $reservation->start_date }}"
                                                   class="text-nowrap">
                                                {{ $reservation->formatted_start_date }}
                                                </time>
                                                <span class="text-muted mx-2"
                                                   style="font-size: 14px;">&mdash;</span>
                                                <!-- Separator -->
                                                <time
                                                   datetime="{{ $reservation->end_date }}">
                                                {{ $reservation->formatted_end_date }}
                                                </time>
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
                                 <div class="d-flex align-items-center text-muted"
                                    style="font-size: 0.85rem;">
                                    <i class="feather icon-refresh-cw me-2"></i>
                                    <time
                                       datetime="{{ $reservation->updated_at->toISOString() }}">
                                    {{ $reservation->updated_at->diffForHumans() }}
                                    </time>
                                 </div>
                                 @if ($reservation->invoice)
                                 <div class="d-flex align-items-center">
                                    <i class="feather icon-credit-card text-muted me-2"
                                       style="font-size: 14px;"></i>
                                    <span
                                       class="badge badge-pill {{ $reservation->invoice->status === 'paid' ? 'badge-success' : 'badge-warning' }} px-3">
                                    {{ __($reservation->invoice->status) }}
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
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center m-0"
                           role="alert">
                           <i class="feather icon-info mr-2" style="font-size: 1.25rem;"></i>
                           <span>{{ __('No reservations found.') }}</span>
                        </div>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
         </div>
         <!-- End Reservation Info Tab -->
<!-- Payments Info Tab -->
<div class="tab-pane fade" id="v-pills-payments-info" role="tabpanel" aria-labelledby="v-pills-payments-info-tab">
    <div class="card border-0 shadow-lg">
        <!-- Card Header -->
        <div class="card-header bg-primary p-4 position-relative">
            <div class="text-white position-relative z-2">
                <h4 class="card-title mb-0 fw-bold"><i class="fa fa-credit-card me-2"></i>{{ __('Payments Info') }}</h4>
            </div>
            <div class="position-absolute top-0 end-0 p-3">
                <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body p-3">
            <div class="row">
                @if ($invoices && $invoices->count() > 0)
                    @foreach ($invoices as $invoice)
                        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                            <div class="card shadow-sm border-primary h-100">
                                <img class="card-img-top rounded-top" src="{{ asset('images/invoice/invoice.svg') }}" alt="{{ __('Housing Fees') }}" aria-hidden="true" />
                                <div class="card-body p-3">
                                    <h6 class="card-title font-weight-bold text-primary mb-2">{{ __('Housing') }}</h6>
                                    <div class="card-subtitle mb-2 text-muted small">
                                        @if ($invoice->reservation->period_type === 'long')
                                            <div class="d-flex align-items-center gap-1">
                                                @if(app()->getLocale() === 'ar')
                                                <span>{{ __('Term') }} {{ __($invoice->reservation->academicTerm->term) }}</span>
                                                <span>{{ __($invoice->reservation->academicTerm->semester) }}</span>
                                                @else
                                                <span>{{ __($invoice->reservation->academicTerm->semester) }}</span>

                                                <span>{{ __('Term') }} {{ $invoice->reservation->academicTerm->term }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-1">
                                                <span>{{ trans(optional($invoice->reservation->academicTerm)->name) }}</span>
                                                <span>
    {{ optional($invoice->reservation->academicTerm)->academic_year ? 
        (app()->getLocale() === 'ar' ? arabicNumbers(optional($invoice->reservation->academicTerm)->academic_year) : optional($invoice->reservation->academicTerm)->academic_year) 
        : __('Not specified') 
    }}
</span>                                            </div>
                                        @else
                                            <span class="d-flex align-items-center">
                                                {{ __('Short Term') }}
                                                <i class="feather icon-zap ml-1 text-warning" style="font-size: 12px;"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="mb-2">
                                        <span class="text-secondary small">{{ __('Total:') }}</span>
                                        <h6 class="text-success mb-0">{{ $invoice->totalAmount() }}</h6>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        @if($invoice->admin_approval == "pending")
                                            <span class="badge rounded-pill {{ 
                                                $invoice->status == 'paid' ? 'bg-success' : 
                                                ($invoice->status == 'pending' ? 'bg-warning' : 
                                                ($invoice->status == 'unpaid' ? 'bg-danger' : 'bg-secondary')) 
                                            }}">
                                                <i class="fa fa-circle me-1 small"></i>
                                                {{ __($invoice->status) }}
                                            </span>
                                        @elseif($invoice->admin_approval == "rejected")
                                            <span class="badge rounded-pill bg-danger">
                                                <i class="fa fa-circle me-1 small"></i>
                                                {{ __($invoice->admin_approval) }}
                                            </span>
                                        @endif
                                    </div>
                                    <!-- Rejection Notes -->
                                    @if ($invoice->admin_approval == 'rejected' && $invoice->reject_reason)
                                        <div class="alert alert-danger border-0 p-2 mb-2" role="alert">
                                            <strong>{{ __('Rejection Reason:') }}</strong>
                                            <span>{{ $invoice->reject_reason }}</span>
                                        </div>
                                    @endif
                                    <!-- Attachments Section -->
                                    @if ($invoice->media->count() > 0)
                                        <div class="list-group-item border-0 px-0 py-1">
                                            <i class="fa fa-paperclip text-secondary me-1"></i>
                                            <span class="small">{{ __('Attachments') }} ({{ $invoice->media->count() }}):</span>
                                            <div class="mt-1">
                                                @foreach ($invoice->media as $media)
                                                    @if (in_array(pathinfo($media->path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                                                        <a href="{{ asset($media->path) }}" target="_blank" aria-label="{{ __('View attachment') }}">
                                                            <img src="{{ asset($media->path) }}" alt="Attachment" class="img-thumbnail me-1" style="max-width: 60px; max-height: 60px;" />
                                                        </a>
                                                    @else
                                                        <a href="{{ asset($media->path) }}" target="_blank" class="d-block text-truncate small" aria-label="{{ __('Download attachment') }}">
                                                            <i class="fa fa-file me-1"></i> {{ basename($media->file_path) }}
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-footer d-flex justify-content-center p-2 gap-2">
                                    @if ($invoice->status == 'unpaid')
                                        <button class="btn btn-outline-primary btn-sm pay-now-btn" data-invoice-id="{{ $invoice->id }}" aria-label="{{ __('Pay now for invoice') }}">
                                            <i class="fa fa-credit-card"></i> {{ __('Pay Now') }}
                                        </button>
                                    @endif
                                    @if (($invoice->admin_approval == 'rejected' || $invoice->admin_approval == "pending") && $invoice->status == "pending")
                                        <button class="btn btn-outline-secondary btn-sm update-payment-images-btn" data-invoice-id="{{ $invoice->id }}" aria-label="{{ __('Update payment images') }}">
                                            <i class="fa fa-upload"></i> {{ __('Edit Payment')}}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center m-0" role="alert">
                            <i class="feather icon-info mr-2" style="font-size: 1.25rem;"></i>
                            <span>{{ __('No payments found.') }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- End Payments Info Tab -->
         <!-- Modals -->
            @include('modals.invoice-details')
            @include('modals.file-upload')
            @include('modals.update-images')
      </div>
   </div>
   <!-- End tabs content -->
</div>
<!-- End row -->
@endSection
 @section('scripts')
 <script>
    $(document).ready(function () {
        // =============================================
        // Tab Navigation
        // =============================================
        if (!window.location.hash) {
            $("#v-pills-profile-tab").tab("show");
        }

        function activateTabFromHash() {
            const hash = window.location.hash;
            if (hash) {
                const $tabLink = $(`a[href="${hash}"]`);
                if ($tabLink.length) {
                    $tabLink.tab("show");
                }
            }
        }

        $('a[data-bs-toggle="pill"]').on("shown.bs.tab", function (e) {
            const hash = $(e.target).attr("href");
            if (history.pushState) {
                history.pushState(null, null, hash);
            } else {
                window.location.hash = hash;
            }
        });

        $(window).on("popstate", activateTabFromHash);
        activateTabFromHash();

        // =============================================
        // File Configuration and Validation
        // =============================================
        // =============================================
        // File Configuration and Validation
        // =============================================
        const fileConfig = {
            maxSize: 3 * 1024 * 1024, // 3MB
            allowedTypes: ["image/jpeg", "image/png"],
            maxFiles: 3
        };

        function validateImageFile(file) {
            if (!file) return false;
            if (file.size > fileConfig.maxSize) {
                swal({
                    type: "error",
                    title: "{{ __('File Too Large') }}",
                    text: "{{ __('Image size must be less than 3MB') }}"
                });
                return false;
            }
            if (!fileConfig.allowedTypes.includes(file.type)) {
                swal({
                    type: "error",
                    title: "{{ __('Invalid File Type') }}",
                    text: "{{ __('Please upload only JPG or PNG images') }}"
                });
                return false;
            }
            return true;
        }

        // =============================================
        // Payment Handling
        // =============================================
        let existingMedia = [];
        let deletedMediaIds = [];

        // Open File Upload Modal
        $(".pay-now-btn").click(function () {
            const invoiceId = $(this).data("invoice-id");
            $("#fileUploadModal").data("invoice-id", invoiceId).modal("show");
        });

        // Open Update Images Modal and Load Existing Media
        $(".update-payment-images-btn").click(function () {
            const invoiceId = $(this).data("invoice-id");
            $("#updateImagesModal").data("invoice-id", invoiceId).modal("show");

            $.ajax({
                url: "{{ route('student.invoices.media', ['invoiceId' => ':invoiceId']) }}".replace(':invoiceId', invoiceId),
                type: "GET",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    existingMedia = response.media || [];
                    deletedMediaIds = [];
                    $("#existingAttachments").show();
                    $("#existingAttachmentsContainer").empty();

                    existingMedia.forEach((media) => {
                        const fileSize = media.size < 1024 * 1024 ? `${(media.size / 1024).toFixed(2)} KB` : `${(media.size / (1024 * 1024)).toFixed(2)} MB`;
                        const $preview = $(`
                            <div class="photo-preview-item mb-2 position-relative" data-media-id="${media.id}">
                                <img src="${media.path}" alt="Existing Attachment" class="img-thumbnail">
                                <button type="button" class="btn btn-sm btn-danger position-absolute remove-existing-btn">
                                    <i class="fa fa-times"></i>
                                </button>
                                <div class="file-size text-center mt-1">
                                    <small class="text-muted">${fileSize}</small>
                                </div>
                            </div>
                        `);
                        $("#existingAttachmentsContainer").append($preview);
                    });
                },
                error: function () {
                    $("#existingAttachments").hide();
                }
            });
        });

        // Remove Existing Media
        $("#existingAttachmentsContainer").on("click", ".remove-existing-btn", function () {
            const $previewItem = $(this).parent();
            const mediaId = $previewItem.data("media-id");
            deletedMediaIds.push(mediaId);
            $previewItem.remove();
            $("#deletedMedia").val(deletedMediaIds.join(","));
        });

        // Handle File Input Change for New Images
        $("input[type='file'][name='photos[]']").on("change", function () {
    const $form = $(this).closest("form");
    const $photoPreviewContainer = $form.find(".photoPreviewContainer");

    $photoPreviewContainer.empty();
    const newFiles = Array.from(this.files);
    const validFiles = newFiles.filter((file) => validateImageFile(file));
    const totalFiles = existingMedia.length - deletedMediaIds.length + validFiles.length;

    if (totalFiles > fileConfig.maxFiles) {
        swal({
            type: "error",
            title: "{{ __('Too Many Files') }}",
            text: "{{ __('Up to 3 photos, max 3MB each (JPG, PNG)') }}"
        });
        this.value = ""; // Clear the input
        return;
    }

    // Update the file input to only include valid files
    const dataTransfer = new DataTransfer();
    validFiles.forEach((file) => {
        dataTransfer.items.add(file); // Add only valid files to the DataTransfer object
    });
    this.files = dataTransfer.files; // Update the file input's files

    if (validFiles.length > 0) {
        validFiles.forEach((file) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const fileSize = file.size < 1024 * 1024 ? `${(file.size / 1024).toFixed(2)} KB` : `${(file.size / (1024 * 1024)).toFixed(2)} MB`;
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
        });
    }
});

        // Remove Preview Image
        $(document).on("click", ".remove-preview-btn", function () {
            const $previewItem = $(this).parent();
            const $fileInput = $previewItem.closest("form").find("input[type='file'][name='photos[]']");
            const index = $previewItem.index();
            $previewItem.remove();

            const currentFiles = Array.from($fileInput[0].files);
            const dataTransfer = new DataTransfer();
            currentFiles.forEach((file, i) => {
                if (i !== index) dataTransfer.items.add(file);
            });
            $fileInput[0].files = dataTransfer.files;
        });

        // Submit File Upload Form
        $("#fileUploadForm").on("submit", function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const originalBtnText = $btn.html();
            const invoiceId = $("#fileUploadModal").data("invoice-id");
            const formData = new FormData(this);
            formData.append("invoice_id", invoiceId);

            $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Processing...") }}');

            $.ajax({
                url: "{{ route('student.invoice.pay') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    swal({
                        type: "success",
                        title: "{{ __('Success') }}",
                        text: "{{ __('Payment files updated successfully!') }}"
                    }).then(() => {
                        $("#fileUploadModal").modal("hide");
                        location.reload();
                    });
                },
                error: function (xhr) {
                    swal({
                        type: "error",
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON?.message || "{{ __('Failed to update payment files') }}"
                    });
                },
                complete: function () {
                    $btn.prop("disabled", false).html(originalBtnText);
                }
            });
        });

        // Submit Update Images Form
        $("#updateImagesForm").on("submit", function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            const originalBtnText = $btn.html();
            const invoiceId = $("#updateImagesModal").data("invoice-id");
            const formData = new FormData(this);
            formData.append("invoice_id", invoiceId);
            formData.append("deleted_media", deletedMediaIds.join(","));

            $btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Processing...") }}');

            $.ajax({
                url: "{{ route('student.invoice.update-media', ':invoiceId') }}".replace(':invoiceId', invoiceId),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    swal({
                        type: "success",
                        title: "{{ __('Success') }}",
                        text: "{{ __('Payment images updated successfully!') }}"
                    }).then(() => {
                        $("#updateImagesModal").modal("hide");
                        location.reload();
                    });
                },
                error: function (xhr) {
                    swal({
                        type: "error",
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON?.message || "{{ __('Failed to update payment images') }}"
                    });
                },
                complete: function () {
                    $btn.prop("disabled", false).html(originalBtnText);
                }
            });
        });

        // =============================================
        // Profile Management
        // =============================================
        $("#toggle-password-btn").click(function () {
            $("#password-section").toggle();
            $(this).text($("#password-section").is(":visible") ? "{{ __('Cancel Password Change') }}" : "{{ __('Change Password') }}");
        });

        $("#profile_picture").on("change", function () {
            const file = this.files[0];
            if (file && validateImageFile(file)) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $("#imagePreview").show().find("img").attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
            } else {
                $(this).val("");
                $("#imagePreview").hide();
            }
        });

        $("#profilePictureForm").on("submit", function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $("#uploadProfilePicBtn");
            const $spinner = $btn.find(".spinner-border");
            const $btnText = $btn.find(".button-text");
            const formData = new FormData(this);

            $btn.prop("disabled", true);
            $spinner.removeClass("d-none");
            $btnText.text("{{ __('Uploading...') }}");

            $.ajax({
                url: "{{ route('student.profile.update-picture') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    if (response.image_url) {
                        $(".profile-picture").attr("src", response.image_url);
                    }
                    swal({
                        type: "success",
                        title: "{{ __('Success') }}",
                        text: "{{ __('Profile picture updated successfully!') }}"
                    }).then(() => {
                        $("#profilePicModal").modal("hide");
                        window.location.reload();
                    });
                },
                error: function (xhr) {
                    swal({
                        type: "error",
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON?.message || "{{ __('Failed to update profile picture') }}"
                    });
                },
                complete: function () {
                    $btn.prop("disabled", false);
                    $spinner.addClass("d-none");
                    $btnText.text("{{ __('Upload New Picture') }}");
                }
            });
        });

        $("#updateProfileForm").on("submit", function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $("#updateProfileBtn");
            const $spinner = $btn.find(".spinner-border");
            const $btnText = $btn.find(".button-text");
            const formData = new FormData(this);

            $btn.prop("disabled", true);
            $spinner.removeClass("d-none");
            $btnText.text("{{ __('Updating...') }}");

            $.ajax({
                url: "{{ route('student.profile.update') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    swal({
                        type: "success",
                        title: "{{ __('Success') }}",
                        text: "{{ __('Profile data updated successfully!') }}"
                    }).then(() => window.location.reload());
                },
                error: function (xhr) {
                    swal({
                        type: "error",
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON?.message || "{{ __('Failed to update profile data') }}"
                    });
                },
                complete: function () {
                    $btn.prop("disabled", false);
                    $spinner.addClass("d-none");
                    $btnText.text("{{ __('Update Profile') }}");
                }
            });
        });

        $("#deleteProfilePictureForm").on("submit", function (e) {
            e.preventDefault();
            const $form = $(this);
            const $btn = $("#deleteProfilePictureBtn");
            const $spinner = $btn.find(".spinner-border");
            const $btnText = $btn.find(".button-text");
            const formData = new FormData(this);

            $btn.prop("disabled", true);
            $spinner.removeClass("d-none");
            $btnText.text("{{ __('Deleting...') }}");

            $.ajax({
                url: "{{ route('student.profile.delete-picture') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (response) {
                    swal({
                        type: "success",
                        title: "{{ __('Success') }}",
                        text: "{{ __('Profile picture deleted successfully!') }}"
                    }).then(() => window.location.reload());
                },
                error: function (xhr) {
                    swal({
                        type: "error",
                        title: "{{ __('Error') }}",
                        text: xhr.responseJSON?.message || "{{ __('Failed to delete profile picture') }}"
                    });
                },
                complete: function () {
                    $btn.prop("disabled", false);
                    $spinner.addClass("d-none");
                    $btnText.text("{{ __('Delete Picture') }}");
                }
            });
        });
    });
</script>
@endsection