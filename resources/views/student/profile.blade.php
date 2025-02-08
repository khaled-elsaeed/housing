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
            <h5 class="card-title mb-0">{{ __('My Account') }}</h5>
         </div>
         <div class="card-body">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
               <!-- Profile Tab (Always visible) -->
               <a class="nav-link mb-2" id="v-pills-profile-tab" data-bs-toggle="pill"
                  href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
               <i class="feather icon-user me-2"></i>{{ __('Profile') }}
               </a>
               <!-- My Address Tab -->
               @if(optional($user->student->governorate)->name_en || $user->student->street)
               <a class="nav-link mb-2" id="v-pills-address-tab" data-bs-toggle="pill"
                  href="#v-pills-address" role="tab" aria-controls="v-pills-address" aria-selected="false">
               <i class="feather icon-map-pin me-2"></i>{{ __('Address') }}
               </a>
               @endif
               <!-- Academic Info Tab -->
               @if(optional($user->student)->program)
               <a class="nav-link mb-2" id="v-pills-academic-info-tab" data-bs-toggle="pill"
                  href="#v-pills-academic-info" role="tab" aria-controls="v-pills-academic-info" aria-selected="false">
               <i class="feather icon-book-open me-2"></i>{{ __('Academic Info') }}
               </a>
               @endif
               <!-- Parent Info Tab -->
               @if(optional($user->parent)->relation)
               <a class="nav-link mb-2" id="v-pills-parent-info-tab" data-bs-toggle="pill"
                  href="#v-pills-parent-info" role="tab" aria-controls="v-pills-parent-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('Parent Info') }}
               </a>
               @endif
               <!-- Sibling Info Tab -->
               @if(optional($user->sibling)->gender && $user->sibling)
               <a class="nav-link mb-2" id="v-pills-sibling-info-tab" data-bs-toggle="pill"
                  href="#v-pills-sibling-info" role="tab" aria-controls="v-pills-sibling-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('Sibling Info') }}
               </a>
               @endif
               <!-- Emergency Info Tab -->
               @if(optional($user->parent)->living_abroad == 1 && optional($user->EmergencyContact))
               <a class="nav-link mb-2" id="v-pills-emergency-info-tab" data-bs-toggle="pill"
                  href="#v-pills-emergency-info" role="tab" aria-controls="v-pills-emergency-info" aria-selected="false">
               <i class="feather icon-alert-triangle me-2"></i>{{ __('Emergency Info') }}
               </a>
               @endif
               <!-- Reservations Info Tab -->
               @if(optional($user->reservations) )
               <a class="nav-link mb-2" id="v-pills-reservation-info-tab" data-bs-toggle="pill"
                  href="#v-pills-reservation-info" role="tab" aria-controls="v-pills-reservation-info" aria-selected="false">
               <i class="feather icon-calendar me-2"></i>{{ __('Reservations') }}
               </a>
               @endif
               <!-- Payments Info Tab -->
               @if($user->reservations)
               <a class="nav-link mb-2" id="v-pills-payments-info-tab" data-bs-toggle="pill"
                  href="#v-pills-payments-info" role="tab" aria-controls="v-pills-payments-info" aria-selected="false">
               <i class="feather icon-credit-card me-2"></i>{{ __('Payments') }}
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
                           <form action="{{ route('student.profile.delete-picture') }}" method="POST" style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-sm font-16" 
                                 onclick="return confirm('{{ __('Are you sure you want to delete your profile picture?') }}')">
                              <i class="fa fa-trash-alt me-2"></i> {{ __('Delete Picture') }}
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
                        <button type="submit" class="btn btn-secondary w-50">
                        <i class="fa fa-save me-2"></i> {{ __('Update Profile') }}
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
         <!-- My Address Tab -->
         <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
            <div class="card m-b-30">
               <!-- Header with image banner -->
               <div class="card-header bg-primary p-4 position-relative">
                  <div class="text-white position-relative z-2">
                     <h4 class="card-title mb-0 fw-bold">
                        <i class="fa fa-building me-2"></i>{{ __('My Address') }}
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
                        <label for="governorate">{{ __('Governorate') }}</label>
                        <input type="text" class="form-control" id="governorate" name="governorate" 
                           value="{{ app()->getLocale() == 'en' ? $user->student->governorate->name_en : $user->student->governorate->name_ar }}" disabled>
                     </div>
                     @endif
                     @if(!empty($user->student->city) && !empty($user->student->city->name_en))
                     <div class="mb-3">
                        <label for="city">{{ __('City') }}</label>
                        <input type="text" class="form-control" id="city" name="city" 
                           value="{{ app()->getLocale() == 'en' ? $user->student->city->name_en : $user->student->city->name_ar }}" disabled>
                     </div>
                     @endif
                     @if(!empty($user->student->street))
                     <div class="mb-3">
                        <label for="street">{{ __('Street') }}</label>
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
                        <i class="fa fa-building me-2"></i>{{ __('Academic Info') }}
                     </h4>
                  </div>
                  <!-- Decorative pattern overlay -->
                  <div class="position-absolute top-0 end-0 p-3">
                     <i class="fa fa-shapes text-white opacity-25 fa-3x"></i>
                  </div>
               </div>
               <div class="card-body">
                  <p class="text-muted">{{ __('Manage your academic details.') }}</p>
                  <form class="row g-3" method="POST" action="{{ route('student.updateAcademicInfo') }}">
                     @csrf
                     @method('PUT')
                     @if(!empty(optional($user->student)->faculty))
                     <div class="col-md-6">
                        <label for="faculty">{{ __('Faculty') }}</label>
                        <input type="text" class="form-control" id="faculty" name="faculty" 
                           value="{{ app()->getLocale() == 'en' ? optional($user->student)->faculty->name_en : optional($user->student)->faculty->name_ar }}" disabled>
                        <input type="hidden" name="faculty_id" value="{{ optional($user->student)->faculty_id }}">
                     </div>
                     @endif
                     @if(!empty(optional($user->student)->program))
                     <div class="col-md-6">
                        <label for="program">{{ __('Program') }}</label>
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
                           <i class="fa fa-building me-2"></i>{{ __('Parent Information') }}
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
                              <label for="parent_name">{{ __('Parent Name') }}</label>
                              <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                 value="{{ old('parent_name', $user->parent->name) }}" disabled>
                           </div>
                           <!-- Parent Relation -->
                           <div class="col-md-6">
                              <label for="parent_relation">{{ __('Parent Relation') }}</label>
                              <input type="text" class="form-control" id="parent_relation" name="parent_relation" 
                                 value="{{ old('parent_relation', $user->parent->relation) }}" disabled>
                           </div>
                           <!-- Parent Email -->
                           <div class="col-md-6">
                              <label for="parent_email">{{ __('Parent Email') }}</label>
                              <input type="email" class="form-control" id="parent_email" name="parent_email" 
                                 value="{{ old('parent_email', $user->parent->email) }}" disabled>
                           </div>
                           <!-- Parent Phone -->
                           <div class="col-md-6">
                              <label for="parent_phone">{{ __('Parent Phone') }}</label>
                              <input type="text" class="form-control" id="parent_phone" name="parent_phone" 
                                 value="{{ old('parent_phone', $user->parent->phone) }}" disabled>
                           </div>
                              <!-- Living Abroad -->
                              <div class="col-md-6">
                                <label for="parent_living_abroad">{{ __('Living Abroad') }}</label>
                                <input type="text" class="form-control" id="parent_living_abroad" name="parent_living_abroad" 
                                  value="{{ old('parent_living_abroad', $user->parent->living_abroad == 1 ? __('Yes') : __('No')) }}" disabled>
                              </div>
                              <!-- Living With parents -->
                              <div class="col-md-6">
                                <label for="parent_living_with">{{ __('Living With parents') }}</label>
                                <input type="text" class="form-control" id="parent_living_with" name="parent_living_with" 
                                  value="{{ old('parent_living_with', $user->parent->living_with == 1 ? __('Yes') : __('No')) }}" disabled>
                              </div>
                           @if($user->parent->living_with == 0) <!-- Only show if living_with is 0 (no) -->
                           <div class="col-md-6">
                              <label for="parent_city">{{ __('Parent City') }}</label>
                              <input type="text" class="form-control" id="parent_city" name="parent_city" 
                                 value="{{ old('parent_city', $user->parent->city ?? '') }}">
                           </div>
                           <div class="col-md-6">
                              <label for="parent_governorate">{{ __('Parent Governorate') }}</label>
                              <input type="text" class="form-control" id="parent_governorate" name="parent_governorate" 
                                 value="{{ old('parent_governorate', $user->parent->governorate ?? '') }}">
                           </div>
                           @endif
                        </div>
                     </form>
                     @else
                     <p>{{ __('No parent information available') }}</p>
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
                        <i class="fa fa-building me-2"></i>{{ __('Sibling Information') }}
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
                           <label for="sibling_gender">{{ __('Sibling Gender') }}</label>
                           <select class="form-control" id="sibling_gender" name="sibling_gender">
                           <option value="brother" {{ old('sibling_gender', $user->sibling->gender) == 'brother' ? 'selected' : '' }}>{{ __('Brother') }}</option>
                           <option value="sister" {{ old('sibling_gender', $user->sibling->gender) == 'sister' ? 'selected' : '' }}>{{ __('Sister') }}</option>
                           </select>
                        </div>
                        <!-- Sibling Name -->
                        <div class="col-md-6">
                           <label for="sibling_name">{{ __('Sibling Name') }}</label>
                           <input type="text" class="form-control" id="sibling_name" name="sibling_name" value="{{ old('sibling_name', $user->sibling->name) }}">
                        </div>
                        <!-- Sibling National ID -->
                        <div class="col-md-6">
                           <label for="sibling_national_id">{{ __('Sibling National ID') }}</label>
                           <input type="text" class="form-control" id="sibling_national_id" name="sibling_national_id" value="{{ old('sibling_national_id', $user->sibling->national_id) }}">
                        </div>
                        <!-- Sibling Faculty -->
                        <div class="col-md-6">
                           <label for="sibling_faculty">{{ __('Sibling Faculty') }}</label>
                           <input type="text" class="form-control" id="sibling_faculty" name="sibling_faculty" value="{{ old('sibling_faculty', optional($user->sibling->faculty)->name_en) }}">
                        </div>
                     </div>
                    
                  </form>
                  @else
                  <p class="text-muted">{{ __('No sibling information available') }}</p>
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
                        <i class="fa fa-building me-2"></i>{{ __('Emergency Information') }}
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
                           <label for="emergency_contact_name">{{ __('Emergency Contact Name') }}</label>
                           <input type="text" class="form-control" id="emergency_contact_name"
                              name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergencyContact->name ?? '') }}">
                        </div>
                        <!-- Emergency Phone -->
                        <div class="col-md-6">
                           <label for="emergency_phone">{{ __('Emergency Phone') }}</label>
                           <input type="text" class="form-control" id="emergency_phone" name="emergency_phone"
                              value="{{ old('emergency_phone', $user->emergencyContact->phone ?? '') }}">
                        </div>
                        <!-- Relationship Dropdown -->
                        <div class="col-md-6">
                           <label for="relationship">{{ __('Relationship') }}</label>
                           <select class="form-control" id="relationship" name="relationship">
                           <option value="uncle" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'uncle' ? 'selected' : '' }}>{{ __('Uncle') }}</option>
                           <option value="aunt" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'aunt' ? 'selected' : '' }}>{{ __('Aunt') }}</option>
                           <option value="grandparent" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'grandparent' ? 'selected' : '' }}>{{ __('Grandparent') }}</option>
                           <option value="other" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                           <option value="spouse" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'spouse' ? 'selected' : '' }}>{{ __('Spouse') }}</option>
                           <option value="nephew" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'nephew' ? 'selected' : '' }}>{{ __('Nephew') }}</option>
                           <option value="cousin" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'cousin' ? 'selected' : '' }}>{{ __('Cousin') }}</option>
                           <option value="niece" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'niece' ? 'selected' : '' }}>{{ __('Niece') }}</option>
                           </select>
                        </div>
                     </div>
                  </form>
                  @else
                  <p class="text-muted">{{ __('No emergency contact information available') }}</p>
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
                        <i class="fa fa-building me-2"></i>{{ __('Reservation Information') }}
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
                                    <span>{{ __('Reservation') }} #{{ $loop->iteration }}</span>
                                 </h3>
                                 <span class="badge badge-pill 
                                    @if($reservation->status === 'completed') badge-success
                                    @elseif($reservation->status === 'upcoming') badge-info
                                    @elseif($reservation->status === 'cancelled') badge-danger
                                    @else badge-warning
                                    @endif px-3" role="status">
                                 <i class="feather icon-circle mr-1" style="font-size: 8px;"></i>
                                 {{ __($reservation->status) }}
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
                                          <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('Location') }}</h6>
                                          <div class="font-weight-bold" style="font-size: 0.95rem;">
                                             {{ __('Room') }} {{ $reservation->room->number ?? __('Not specified') }}
                                          </div>
                                          <div class="mt-1 d-flex align-items-center" style="font-size: 0.8rem;">
                                             <span class="text-muted d-flex align-items-center">
                                             <i class="feather icon-map-pin me-1" style="font-size: 12px;"></i>
                                             {{ __('Building') }} {{ $reservation->room->apartment->building->number ?? 'N/A' }}
                                             </span>
                                             <span class="mx-2 text-muted">•</span>
                                             <span class="text-muted d-flex align-items-center">
                                             <i class="feather icon-home me-1" style="font-size: 12px;"></i>
                                             {{ __('Apt') }} {{ $reservation->room->apartment->number ?? 'N/A' }}
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
                                          <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('Period') }}</h6>
                                          <div class="font-weight-bold" style="font-size: 0.95rem;">
                                             @if($reservation->period_type === 'long')
                                             {{ __(optional($reservation->academicTerm)->name). ' - '.optional($reservation->academicTerm)->academic_year ?? __('Not specified') }}
                                             @else
                                             <span class="d-flex align-items-center">
                                             {{ __('Short Term') }}
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
                                          <h6 class="text-uppercase text-muted mb-1" style="font-size: 11px; letter-spacing: 0.5px;">{{ __('Duration') }}</h6>
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
                        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center m-0" role="alert">
                           <i class="feather icon-info mr-2" style="font-size: 1.25rem;"></i>
                           <span>{{ __('No reservations found.') }}</span>
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
                  <h5 class="card-title mb-0">{{ __('Payments Info') }}</h5>
               </div>
               <div class="card-body">
                  <div class="row">
                     <!-- Existing Invoice Cards -->
                     @foreach($invoices as $invoice)
                       <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="card shadow-sm border-primary">
                           <!-- Image Section -->
                           <img class="card-img-top rounded-top" src="{{ asset('images/invoice/invoice.svg') }}" alt="{{ __('Housing Fees') }}">
                           <div class="card-body">
                            <!-- Title and Subtitle -->
                            <h5 class="card-title font-weight-bold text-primary">
                              {{ __('Housing') }}
                            </h5>
                            <h6 class="card-subtitle mb-3 text-muted">
                            @if($invoice->reservation->period_type === 'long')
                            <h6 class="text">
                              @if(app()->getLocale() == 'ar')
                            {{ __('Term') }} {{ $invoice->reservation->academicTerm->term }}

                                {{ __($invoice->reservation->academicTerm->semester) }}  
                                @else

                                {{ __($invoice->reservation->academicTerm->semester) }}  
                                {{ __('Term') }} {{ $invoice->reservation->academicTerm->term }}

                                @endif

</h6>
                              <div class="d-flex align-items-center gap-2">
                               
                               <span>{{ __(optional($invoice->reservation->academicTerm)->name). ' - '.optional($invoice->reservation->academicTerm)->academic_year ?? __('Not specified') }}</span>
                              </div>
                            @else
                              <span class="d-flex align-items-center">
                               {{ __('Short Term') }}
                               <i class="feather icon-zap ml-2 text-warning" style="font-size: 14px;"></i>
                              </span>
                            @endif
                            </h6>
                            
                            <!-- Info Section: Total and Status -->
                            <div class="mb-2">
                              <h6 class="text-secondary">
                               <strong>{{ __('Total:') }}</strong>
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
                               {{ __($invoice->status) }}
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
                            <i class="fa fa-credit-card"></i> {{ __('Pay Now') }}
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
                     <h5 class="modal-title" id="invoiceDetailsModalLabel">{{ __('Reservation and Payment Details') }}</h5>
                     <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                  </div>
                  <div class="modal-body">
                     <!-- Reservation Details Section -->
                     <h6><i class="fa fa-calendar-check"></i> {{ __('Reservation Details') }}</h6>
                     <div class="mb-3">
                        <strong>{{ __('Location:') }}</strong> <span id="location">{{ __('Empty') }}</span>
                     </div>
                     <!-- Payment Details Section -->
                     <h6><i class="fa fa-credit-card"></i> {{ __('Payment Details') }}</h6>
                     <table class="table table-bordered table-striped table-hover">
                        <thead>
                           <tr>
                              <th scope="col" class="text-center"><i class="fa fa-tag"></i> {{ __('Category') }}</th>
                              <th scope="col" class="text-center"><i class="fa fa-dollar-sign"></i> {{ __('Amount') }}</th>
                           </tr>
                        </thead>
                        <tbody id="paymentDetails">
                           <!-- Payment rows will be dynamically added here -->
                        </tbody>
                     </table>
                     <!-- Total Payment -->
                     <div class="d-flex justify-content-between">
                        <strong>{{ __('Total:') }}</strong>
                        <span id="totalAmount" class="text-success">{{ __('Empty') }}</span>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                  </div>
               </div>
            </div>
         </div>
         <!-- Modal for File Upload -->
         <div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" >
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="fileUploadModalLabel">{{ __('Upload Payment File') }}</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                  </div>
                  <div class="modal-body">
                     <form id="fileUploadForm">
                        <!-- Payment Method Selection -->
                        <div class="mb-3">
                           <label for="paymentMethod" class="form-label">{{ __('Select Payment Method') }}</label>
                           <select class="form-select" id="uploadPaymentMethod" name="payment_method" required>
                              <option value="" disabled selected>{{ __('Select Payment Method') }}</option>
                              <option value="instapay">{{ __('Instapay') }}</option>
                              <option value="bank_transfer">{{ __('Bank Transfere') }}</option>
                           </select>
                        </div>
                        <!-- File Upload -->
                        <div class="mb-3">
                           <label for="uploadInvoiceReceipt" class="form-label">{{ __('Choose File') }}</label>
                           <input type="file" class="form-control" id="uploadInvoiceReceipt" name="invoice-receipt" required>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">{{ __('Upload File') }}</button>
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
       // Initialize first tab if no hash present
       if (!window.location.hash) {
           $('#v-pills-profile-tab').tab('show');
       }
   
       // Handle tab navigation
       function activateTabFromHash() {
           let hash = window.location.hash;
           if (hash) {
               const $tabLink = $(`a[href="${hash}"]`);
               if ($tabLink.length) {
                   $tabLink.tab('show');
               }
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
       $(window).on('popstate', activateTabFromHash);
   
       // Initial tab activation
       activateTabFromHash();
   
       // File validation config
       const fileConfig = {
           maxSize: 4 * 1024 * 1024,
           allowedTypes: ['image/jpeg', 'image/png']
       };
   
       // Password section toggle
       $('#toggle-password-btn').click(function() {
           $('#password-section').toggle();
           $(this).text(
               $('#password-section').is(':visible') 
               ? "{{ __('Cancel Password Change') }}" 
               : "{{ __('Change Password') }}"
           );
       });
   
       // Image file validation
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

               // Real-time image validation on file input change
               $('input[type="file"]').on('change', function() {
            const file = this.files[0];
            validateImageFile(file, this);
        });
    
       
   
       // Profile picture preview
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
   
       // Profile picture upload
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
               url: "{{ route('student.profile.update-picture') }}",
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
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
   
       // Payment handling
       $('.pay-now-btn').click(function() {
           const invoiceId = $(this).data('invoice-id');
           $('#fileUploadModal')
               .data('invoice-id', invoiceId)
               .modal('show');
       });
   
       // Payment file upload
       $('#fileUploadForm').on('submit', function(e) {
           e.preventDefault();
           const $form = $(this);
           const $btn = $form.find('button[type="submit"]');
           const originalBtnText = $btn.html();
           const file = $('#uploadInvoiceReceipt')[0].files[0];
           const invoiceId = $('#fileUploadModal').data('invoice-id');
   
           if (!validateImageFile(file)) return;
   
           const formData = new FormData(this);
           formData.append('invoice_id', invoiceId);
   
           $btn.prop('disabled', true)
               .html('<i class="fa fa-spinner fa-spin"></i> {{ __("Processing...") }}');
   
           $.ajax({
               url: "{{ route('student.invoice.pay') }}",
               type: 'POST',
               data: formData,
               processData: false,
               contentType: false,
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               success: function(response) {
                   swal({
                       type: 'success',
                       title: "{{ __('Success') }}",
                       text: "{{ __('Payment file uploaded successfully!') }}"
                   }).then(() => {
                       $('#fileUploadModal').modal('hide');
                       location.reload();
                   });
               },
               error: function(xhr) {
                   swal({
                       type: 'error',
                       title: "{{ __('Error') }}",
                       text: xhr.responseJSON?.message || "{{ __('Failed to upload payment file') }}"
                   });
               },
               complete: function() {
                   $btn.prop('disabled', false).html(originalBtnText);
               }
           });
       });
   });
</script>
@endsection