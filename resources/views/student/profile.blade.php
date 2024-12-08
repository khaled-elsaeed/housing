@extends('layouts.student')
@section('title', 'My Profile')
@section('content')
<!-- Start row -->
<div class="row">
   <div class="row">
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
   <!-- Start col for sidebar -->
   <div class="col-lg-5 col-xl-3">
      <div class="card m-b-30">
         <div class="card-header">
            <h5 class="card-title mb-0">My Account</h5>
         </div>
         <div class="card-body">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
               <!-- Profile Tab -->
               <a class="nav-link mb-2 active" id="v-pills-profile-tab" data-bs-toggle="pill"
                  href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
               <i class="feather icon-user me-2"></i>My Profile
               </a>
               <!-- Notifications Tab -->
               <a class="nav-link mb-2" id="v-pills-notifications-tab" data-bs-toggle="pill"
                  href="#v-pills-notifications" role="tab" aria-controls="v-pills-notifications"
                  aria-selected="false">
               <i class="feather icon-bell me-2"></i>Notifications
               </a>
               <!-- My Address Tab -->
               <a class="nav-link mb-2" id="v-pills-address-tab" data-bs-toggle="pill"
                  href="#v-pills-address" role="tab" aria-controls="v-pills-address" aria-selected="false">
               <i class="feather icon-map-pin me-2"></i>My Address
               @if((!optional($user->student->governorate)->name_en) || !($user->student->street))
               <span class="badge bg-warning ms-2" data-bs-toggle="tooltip" title="Incomplete data">
               <i class="feather icon-alert-triangle"></i>
               </span>
               @endif
               </a>
               <!-- Add Academic Info tab in the sidebar -->
               <a class="nav-link mb-2" id="v-pills-academic-info-tab" data-bs-toggle="pill"
                  href="#v-pills-academic-info" role="tab" aria-controls="v-pills-academic-info" aria-selected="false">
               <i class="feather icon-book-open me-2"></i>Academic Info
               @if(!optional($user->student)->program)
               <span class="badge bg-warning ms-2" data-bs-toggle="tooltip" title="Incomplete data">
               <i class="feather icon-alert-triangle"></i>
               </span>
               @endif
               </a>
               <!-- Parent Info Tab -->
               <a class="nav-link mb-2" id="v-pills-parent-info-tab" data-bs-toggle="pill"
                  href="#v-pills-parent-info" role="tab" aria-controls="v-pills-parent-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>Parent Info
               @if(!optional($user->parent)->relation)
               <span class="badge bg-warning ms-2" data-bs-toggle="tooltip" title="Incomplete data">
               <i class="feather icon-alert-triangle"></i>
               </span>
               @endif
               </a>
               <!-- Sibling Info Tab -->
               <a class="nav-link mb-2" id="v-pills-sibling-info-tab" data-bs-toggle="pill"
                  href="#v-pills-sibling-info" role="tab" aria-controls="v-pills-sibling-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>Sibling Info
               @if(
               !optional($user->sibling)->gender || !$user->sibling
               )
               <span class="badge bg-warning ms-2" data-bs-toggle="tooltip" title="Incomplete data">
               <i class="feather icon-alert-triangle"></i>
               </span>
               @endif
               </a>
               <!-- Emergency Info Tab -->
               <a class="nav-link mb-2" id="v-pills-emergency-info-tab" data-bs-toggle="pill"
                  href="#v-pills-emergency-info" role="tab" aria-controls="v-pills-emergency-info" aria-selected="false">
               <i class="feather icon-alert-triangle me-2"></i>Emergency Info
               @if(
    (optional($user->parent)->living_abroad == 1 && !optional($user->EmergencyContact)) || 
    (optional($user->parent)->living_abroad === null)
)

               <span class="badge bg-warning ms-2" data-bs-toggle="tooltip" title="Incomplete data">
               <i class="feather icon-alert-triangle"></i>
               </span>
               @endif
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
                  <h5 class="card-title mb-0">My Profile</h5>
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
                           <i class="feather icon-edit"></i> Change Picture
                           </a>
                        </li>
                        <!-- Delete Profile Picture Button -->
                        @if($user->profile_picture)
                        <li class="list-inline-item">
                           <form action="#" method="POST" style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger-rgba font-18">
                              <i class="feather icon-trash"></i> Delete Picture
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
                           <h5 class="modal-title" id="profilePicModalLabel">Change Profile Picture</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <form action="#" method="POST" enctype="multipart/form-data">
                              @csrf
                              <div class="mb-3">
                                 <label for="profile_picture" class="form-label">Select New Profile Picture</label>
                                 <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                              </div>
                              <button type="submit" class="btn btn-primary">Upload New Picture</button>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Edit Profile Information -->
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">Edit Profile Information</h5>
               </div>
               <div class="card-body">
                  <form class="row g-3" method="POST" action="#">
                     @csrf
                     @method('PUT')
                     <div class="col-md-6">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                           value="{{ $user->first_name_en }}">
                     </div>
                     <div class="col-md-6">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                           value="{{ $user->last_name_en }}">
                     </div>
                     <div class="col-md-6">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                           value="{{ $user->email }}">
                     </div>
                     <!-- Password Fields -->
                     <div class="col-md-12">
                        <button type="button" class="btn btn-secondary mb-3" id="toggle-password-btn">
                        Change Password
                        </button>
                     </div>
                     <div id="password-section" style="display: none;">
                        <div class="col-md-6">
                           <label for="password">Password</label>
                           <input type="password" class="form-control" id="password" name="password"
                              placeholder="Enter new password">
                        </div>
                        <div class="col-md-6">
                           <label for="password_confirmation">Confirm Password</label>
                           <input type="password" class="form-control" id="password_confirmation"
                              name="password_confirmation" placeholder="Confirm new password">
                        </div>
                     </div>
                     <button type="submit" class="btn btn-primary font-16">
                     <i class="feather icon-save me-2"></i>Update
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
                  <h5 class="card-title mb-0">Notifications</h5>
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
                        <li class="p-2">No notifications available.</li>
                        @endforelse
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <!-- End My Notifications Tab -->
         <!-- My Address -->
         <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">My Address</h5>
               </div>
               <div class="card-body">
                  <form method="POST" action="{{ route('student.updateAddress') }}">
                     @csrf
                     @method('PUT')
                     <!-- Governorate Field -->
                     <div class="mb-3">
                        <label for="governorate">Governorate</label>
                        @if(optional($user->student->governorate)->name_en)
                        <input type="text" class="form-control" id="governorate" name="governorate" 
                           value="{{ $user->student->governorate->name_en }}" disabled>
                        <input type="hidden" name="governorate_id" value="{{ $user->student->governorate_id }}">
                        @else
                        <select class="form-select" id="governorate_id" name="governorate_id">
                           <option value="">Select Governorate</option>
                           @foreach($governorates as $governorate)
                           <option value="{{ $governorate->id }}">{{ $governorate->name_en }}</option>
                           @endforeach
                        </select>
                        @endif
                     </div>
                     <!-- City Field -->
                     <div class="mb-3">
                        <label for="city">City</label>
                        @if(optional($user->student->city)->name_en)
                        <input type="text" class="form-control" id="city" name="city" 
                           value="{{ $user->student->city->name_en }}" disabled>
                        <input type="hidden" name="city_id" value="{{ $user->student->city_id }}">
                        @else
                        <select class="form-control" id="city_id" name="city_id">
                           <option value="">Select City</option>
                           <!-- City options will be dynamically added here based on selected governorate -->
                        </select>
                        @endif
                     </div>
                     <!-- Street Field -->
                     <div class="mb-3">
                        <label for="street">Street</label>
                        @if($user->student->street)
                        <input type="text" class="form-control" id="street" name="street" 
                           value="{{ $user->student->street }}" disabled>
                        @else
                        <input type="text" class="form-control" id="street" name="street" 
                           placeholder="Enter Street Name" value="{{ old('street') }}">
                        @endif
                     </div>
                     @if(!$user->student->street)
                     <!-- Submit Button -->
                     <button type="submit" class="btn btn-primary">
                     <i class="feather icon-save me-2"></i>Update Address
                     </button>
                     @endif
                  </form>
               </div>
            </div>
         </div>
         <!-- Academic Info Tab Content -->
         <div class="tab-pane fade" id="v-pills-academic-info" role="tabpanel" aria-labelledby="v-pills-academic-info-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">Academic Info</h5>
               </div>
               <div class="card-body">
                  <p class="text-muted">Manage your academic details below.</p>
                  <form class="row g-3"method="POST" action="{{ route('student.updateAcademicInfo') }}">
                     @csrf
                     @method('PUT')
                     <!-- Faculty Field -->
                     <div class="col-md-6">
                        <label for="faculty">Faculty</label>
                        @if(optional($user->student)->faculty)
                        <input type="text" class="form-control" id="faculty" name="faculty"
                           value="{{ optional($user->student)->faculty->name_en }}" disabled>
                        <input type="hidden" name="faculty_id" value="{{ optional($user->student)->faculty_id }}">
                        @else
                        <select class="form-control" id="faculty_id" name="faculty_id" onchange="populatePrograms(this.value)">
                           <option value="">Select Faculty</option>
                           @foreach($faculties as $faculty)
                           <option value="{{ $faculty->id }}">{{ $faculty->name_en }}</option>
                           @endforeach
                        </select>
                        @endif
                     </div>
                     <!-- Program Field -->
                     <div class="col-md-6">
                        <label for="program">Program</label>
                        @if(optional($user->student)->program)
                        <input type="text" class="form-control" id="program" name="program"
                           value="{{ optional($user->student)->program->name_en }}" disabled>
                        <input type="hidden" name="program_id" value="{{ optional($user->student)->program_id }}">
                        @else
                        <select class="form-control" id="program_id" name="program_id" 
                        @if(!optional($user->student)->faculty) disabled @endif>
                        <option value="">Select Program</option>
                        @if(optional($user->student)->faculty)
                        @foreach($programs->where('faculty_id', optional($user->student)->faculty_id) as $program)
                        <option value="{{ $program->id }}">{{ $program->name_en }}</option>
                        @endforeach
                        @endif
                        </select>
                        @endif
                     </div>
                     <!-- Level Field -->
                     <!-- Submit Button -->
                     <button type="submit" class="btn btn-primary font-16">
                     <i class="feather icon-save me-2"></i>Save Academic Info
                     </button>
                  </form>
               </div>
            </div>
         </div>
         <!-- Parent Info -->
         <div class="tab-pane fade" id="v-pills-parent-info" role="tabpanel" aria-labelledby="v-pills-parent-info-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">Parent Info</h5>
               </div>
               <div class="card-body">
                  <form method="POST" action="{{ route('student.updateParentInfo') }}">
                     @csrf
                     @method('PUT')
                     <div class="row">
                        <!-- Check if Parent Record Exists -->
                        @if($user->parent)
                        <!-- Parent Name -->
                        <div class="col-md-6">
                           <label for="parent_name">Parent's Name</label>
                           <input type="text" 
                           class="form-control" 
                           id="parent_name" 
                           name="parent_name" 
                           value="{{ old('parent_name', $user->parent->name) }}" 
                           {{ $user->parent->name ? 'disabled' : '' }}>
                           @if($user->parent->name)
                           <input type="hidden" name="parent_name" value="{{ $user->parent->name }}">
                           @endif
                        </div>
                        <!-- Parent Relation -->
                        <div class="col-md-6">
                           <label for="parent_relation">Relation</label>
                           <select class="form-control" 
                           id="parent_relation" 
                           name="parent_relation" 
                           {{ $user->parent->relation ? 'disabled' : '' }}>
                           <option value="father" {{ old('parent_relation', $user->parent->relation) == 'father' ? 'selected' : '' }}>Father</option>
                           <option value="mother" {{ old('parent_relation', $user->parent->relation) == 'mother' ? 'selected' : '' }}>Mother</option>
                           <option value="guardian" {{ old('parent_relation', $user->parent->relation) == 'guardian' ? 'selected' : '' }}>Guardian</option>
                           </select>
                           @if($user->parent->relation)
                           <input type="hidden" name="parent_relation" value="{{ $user->parent->relation }}">
                           @endif
                        </div>
                        <!-- Parent Email -->
                        <div class="col-md-6">
                           <label for="parent_email">Email</label>
                           <input type="parent_email" 
                           class="form-control" 
                           id="parent_email" 
                           name="parent_email" 
                           value="{{ old('parent_email', $user->parent->email) }}" 
                           {{ $user->parent->email ? 'disabled' : '' }}>
                           @if($user->parent->email)
                           <input type="hidden" name="parent_email" value="{{ $user->parent->email }}">
                           @endif
                        </div>
                        <!-- Parent Mobile -->
                        <div class="col-md-6">
                           <label for="parent_mobile">Mobile</label>
                           <input type="text" 
                           class="form-control" 
                           id="parent_mobile" 
                           name="parent_mobile" 
                           value="{{ old('parent_mobile', $user->parent->mobile) }}" 
                           {{ $user->parent->mobile ? 'disabled' : '' }}>
                           @if($user->parent->mobile)
                           <input type="hidden" name="parent_mobile" value="{{ $user->parent->mobile }}">
                           @endif
                        </div>
                        <!-- Living Abroad -->
                        <div class="col-md-6">
                           <label for="parent_living_abroad">Living Abroad</label>
                           <select class="form-control" 
                           id="parent_living_abroad" 
                           name="parent_living_abroad" 
                           {{ $user->parent->living_abroad !== null ? 'disabled' : '' }}>
                           <option value="0" {{ old('parent_living_abroad', $user->parent->living_abroad) == 0 ? 'selected' : '' }}>No</option>
                           <option value="1" {{ old('parent_living_abroad', $user->parent->living_abroad) == 1 ? 'selected' : '' }}>Yes</option>
                           </select>
                           @if($user->parent->living_abroad !== null)
                           <input type="hidden" name="parent_living_abroad" value="{{ $user->parent->living_abroad }}">
                           @endif
                        </div>
                        <div class="col-md-6" id="abroad_country_container" style="{{ old('parent_living_abroad', $user->parent->living_abroad) == 1 ? '' : 'display:none;' }}">
                           <label for="parent_abroad_country_id">Abroad Country</label>
                           <select class="form-control" 
                           id="parent_abroad_country_id" 
                           name="parent_abroad_country_id" 
                           {{ $user->parent->living_abroad == 1 && !$user->parent->abroad_country_id ? '' : 'disabled' }} 
                           {{ $user->parent->living_abroad == 1 && !$user->parent->abroad_country_id ? '' : 'readonly' }}>
                           <option value="">Select Country</option>
                           @foreach($countries as $country)
                           <option value="{{ $country->id }}" 
                           {{ old('parent_abroad_country_id', $user->parent->abroad_country_id) == $country->id ? 'selected' : '' }}>
                           {{ $country->name_en }}
                           </option>
                           @endforeach
                           </select>
                        </div>
                        <div class="col-md-6">
                           <label for="parent_living_with">Living With</label>
                           <select class="form-control" 
                              id="parent_living_with" 
                              name="parent_living_with">
                           <option value="1" {{ old('parent_living_with', $user->parent->living_with) == 1 ? 'selected' : '' }}>Yes</option>
                           <option value="0" {{ old('parent_living_with', $user->parent->living_with) == 0 ? 'selected' : '' }}>No</option>
                           </select>
                        </div>
                        <div id="location_fields_fill" style="{{ $user->parent->living_with == 0 ? '' : 'display:none;' }}">
                           <!-- Parent Governorate -->
                           <div class="col-md-6">
                              <label for="parent_governorate_id">Governorate</label>
                              <select class="form-control" id="parent_governorate_id" name="parent_governorate_id" {{ $user->parent->governorate_id ? 'disabled' : '' }}>
                              <option value="">Select Governorate</option>
                              @foreach($governorates as $governorate)
                              <option value="{{ $governorate->id }}" 
                              {{ $governorate->id == old('parent_governorate_id', $user->parent->governorate_id) ? 'selected' : '' }}>
                              {{ $governorate->name_en }}
                              </option>
                              @endforeach
                              </select>
                           </div>
                           <!-- Parent City -->
                           <div class="col-md-6">
                              <label for="city_id">City</label>
                              <!-- Check if city_id exists in user data -->
                              @if($user->parent->city_id)
                              <!-- If city_id exists, show a readonly input with the city pre-filled -->
                              <input type="text" class="form-control" id="parent_city_id" name="parent_city_id" 
                                 value="{{ old('parent_city_id', $user->parent->city->name) }}" readonly>
                              @else
                              <!-- If parent_city_id does not exist, show a select dropdown for the user to choose a city -->
                              <select class="form-control" id="parent_city_id" name="parent_city_id">
                                 <option value="">Select City</option>
                              </select>
                              @endif
                           </div>
                        </div>
                     </div>
                     @else
                     <div class="row">
                        <!-- Parent's Name -->
                        <div class="col-md-6">
                           <label for="parent_name">Parent's Name</label>
                           <input type="text" 
                              class="form-control" 
                              id="parent_name" 
                              name="parent_name" 
                              value="{{ old('parent_name') }}">
                        </div>
                        <!-- Parent Relation -->
                        <div class="col-md-6">
                           <label for="parent_relation">Relation</label>
                           <select class="form-control" 
                              id="parent_relation" 
                              name="parent_relation">
                           <option value="father" {{ old('parent_relation') == 'father' ? 'selected' : '' }}>Father</option>
                           <option value="mother" {{ old('parent_relation') == 'mother' ? 'selected' : '' }}>Mother</option>
                           <option value="guardian" {{ old('parent_relation') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                           </select>
                        </div>
                        <!-- Parent Email -->
                        <div class="col-md-6">
                           <label for="parent_email">Email</label>
                           <input type="email" 
                              class="form-control" 
                              id="parent_email" 
                              name="parent_email" 
                              value="{{ old('parent_email') }}">
                        </div>
                        <!-- Parent Mobile -->
                        <div class="col-md-6">
                           <label for="parent_mobile">Mobile</label>
                           <input type="text" 
                              class="form-control" 
                              id="parent_mobile" 
                              name="parent_mobile" 
                              value="{{ old('parent_mobile') }}">
                        </div>
                        <div class="col-md-6">
                           <label for="parent_living_abroad">Living Abroad</label>
                           <select class="form-control" 
                              id="parent_living_abroad" 
                              name="parent_living_abroad">
                           <option value="0" {{ old('parent_living_abroad') == '0' ? 'selected' : '' }}>No</option>
                           <option value="1" {{ old('parent_living_abroad') == '1' ? 'selected' : '' }}>Yes</option>
                           </select>
                        </div>
                        <div class="col-md-6" id="abroad_country_container" style="{{ old('parent_living_abroad') == '1' ? '' : 'display:none;' }}">
                           <label for="parent_abroad_country_id">Abroad Country</label>
                           <select class="form-control" 
                           id="parent_abroad_country_id" 
                           name="parent_abroad_country_id" 
                           <option value="">Select Country</option>
                           @foreach($countries as $country)
                           <option value="{{ $country->id }}" {{ old('parent_parent_abroad_country_id') == $country->id ? 'selected' : '' }}>
                           {{ $country->name_en }}
                           </option>
                           @endforeach
                           </select>
                        </div>
                        <div class="col-md-6">
                           <label for="parent_living_with">Living With</label>
                           <select class="form-control" 
                              id="parent_living_with" 
                              name="parent_living_with">
                              <!-- Option for Yes (default) -->
                              <option selected value="1" >Yes</option>
                              <!-- Option for No -->
                              <option value="0" >No</option>
                           </select>
                        </div>
                        <div id="location_fields_not_fill" style="{{ old('parent_living_with') == '0' ? '' : 'display:none;' }}">
                           <!-- Governorate -->
                           <div class="col-md-6">
                              <label for="parent_governorate_id">Governorate</label>
                              <select class="form-control" 
                              id="parent_governorate_id" 
                              name="parent_governorate_id" 
                              {{ old('parent_living_with') == 'no' ? '' : 'disabled' }}>
                              <option value="">Select Governorate</option>
                              @foreach($governorates as $governorate)
                              <option value="{{ $governorate->id }}" {{ old('parent_governorate_id') == $governorate->id ? 'selected' : '' }}>
                              {{ $governorate->name_en }}
                              </option>
                              @endforeach
                              </select>
                           </div>
                           <!-- City -->
                           <div class="col-md-6">
                              <label for="parent_city_id">City</label>
                              <select class="form-control" 
                              id="parent_city_id" 
                              name="parent_city_id" 
                              {{ old('parent_city_id') ? '' : 'disabled' }}>
                              <option value="">Select City</option>
                              @if(old('parent_governorate_id'))
                              @foreach($cities as $city)
                              @if($city->governorate_id == old('parent_governorate_id'))
                              <option value="{{ $city->id }}" {{ old('parent_city_id') == $city->id ? 'selected' : '' }}>
                              {{ $city->name }}
                              </option>
                              @endif
                              @endforeach
                              @endif
                              </select>
                           </div>
                        </div>
                        @endif
                     </div>
                     <!-- Submit Button -->
                     <button type="submit" class="btn btn-primary mt-3">
                     <i class="feather icon-save me-2"></i>Save Parent Info
                     </button>
                  </form>
               </div>
            </div>

         
         <div class="tab-pane fade" id="v-pills-sibling-info" role="tabpanel" aria-labelledby="v-pills-sibling-info-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">Sibling Info</h5>
               </div>
               <div class="card-body">
                  <form method="POST" action="{{ route('student.updateOrCreateSiblingInfo') }}">
                     @csrf
                     @if($user->sibling) <!-- Check if sibling data exists -->
                     <!-- Sibling Gender -->
                     <div class="row">
                        <div class="col-md-6">
                           <label for="sibling_gender">Sibling Gender</label>
                           <select class="form-control" id="sibling_gender" name="sibling_gender">
                           <option value="brother" {{ old('sibling_gender', $user->sibling->gender) == 'brother' ? 'selected' : '' }}>Brother</option>
                           <option value="sister" {{ old('sibling_gender', $user->sibling->gender) == 'sister' ? 'selected' : '' }}>Sister</option>
                           </select>
                        </div>
                        <!-- Sibling Name -->
                        <div class="col-md-6">
                           <label for="sibling_name">Sibling's Name</label>
                           <input type="text" class="form-control" id="sibling_name" name="sibling_name" value="{{ old('sibling_name', $user->sibling->name) }}">
                        </div>
                        <!-- Sibling National ID -->
                        <div class="col-md-6">
                           <label for="sibling_national_id">Sibling's National ID</label>
                           <input type="text" class="form-control" id="sibling_national_id" name="sibling_national_id" value="{{ old('sibling_national_id', $user->sibling->national_id) }}">
                        </div>
                        <!-- Sibling Faculty -->
                        <div class="col-md-6">
                           <label for="sibling_faculty">Sibling's Faculty</label>
                           <input type="text" class="form-control" id="sibling_faculty" name="sibling_faculty" value="{{ old('sibling_faculty', $user->sibling->faculty->name_en) }}">
                        </div>
                     </div>
                     @else <!-- No sibling data, allow user to enter all data -->
                     <!-- Sibling Gender -->
                     <div class="row">
                        <div class="col-md-6">
                           <label for="sibling_gender">Sibling Gender</label>
                           <select class="form-control" id="sibling_gender" name="sibling_gender">
                           <option value="brother" {{ old('sibling_gender') == 'brother' ? 'selected' : '' }}>Brother</option>
                           <option value="sister" {{ old('sibling_gender') == 'sister' ? 'selected' : '' }}>Sister</option>
                           </select>
                        </div>
                        <!-- Sibling Name -->
                        <div class="col-md-6">
                           <label for="sibling_name">Sibling's Name</label>
                           <input type="text" class="form-control" id="sibling_name" name="sibling_name" value="{{ old('sibling_name') }}">
                        </div>
                        <!-- Sibling National ID -->
                        <div class="col-md-6">
                           <label for="sibling_national_id">Sibling's National ID</label>
                           <input type="text" class="form-control" id="sibling_national_id" name="sibling_national_id" value="{{ old('sibling_national_id') }}">
                        </div>
                        <!-- Sibling Faculty -->
                        <div class="col-md-6">
                           <label for="sibling_faculty">Sibling's Faculty</label>
                           <select class="form-control" id="sibling_faculty" name="sibling_faculty">
                              <option value="">Select Faculty</option>
                              <!-- Default empty option -->
                              @foreach($faculties as $faculty)
                              <option value="{{ $faculty->id }}" {{ old('sibling_faculty') == $faculty->id ? 'selected' : '' }}>
                              {{ $faculty->name_en }}
                              </option>
                              @endforeach
                           </select>
                        </div>
                     </div>
                     @endif
                     <!-- Submit Button -->
                     <button type="submit" class="btn btn-primary">
                     <i class="feather icon-save me-2"></i>Save Sibling Info
                     </button>
                  </form>
               </div>
            </div>
         </div>
         <!-- Emergency Info -->
<div class="tab-pane fade" id="v-pills-emergency-info" role="tabpanel" aria-labelledby="v-pills-emergency-info-tab">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title mb-0">Emergency Info</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('student.updateOrCreateEmergencyInfo') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label for="emergency_contact_name">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name"
                               name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergencyContact->name ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="emergency_phone">Emergency Phone</label>
                        <input type="text" class="form-control" id="emergency_phone" name="emergency_phone"
                               value="{{ old('emergency_phone', $user->emergencyContact->phone ?? '') }}">
                    </div>
                    <!-- Relationship Dropdown -->
                    <div class="col-md-6">
                        <label for="relationship">Relationship</label>
                        <select class="form-control" id="relationship" name="relationship">
                            <option value="uncle" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'uncle' ? 'selected' : '' }}>Uncle</option>
                            <option value="aunt" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'aunt' ? 'selected' : '' }}>Aunt</option>
                            <option value="grandparent" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'grandparent' ? 'selected' : '' }}>Grandparent</option>
                            <option value="other" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                            <option value="spouse" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'spouse' ? 'selected' : '' }}>Spouse</option>
                            <option value="nephew" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'nephew' ? 'selected' : '' }}>Nephew</option>
                            <option value="cousin" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'cousin' ? 'selected' : '' }}>Cousin</option>
                            <option value="niece" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'niece' ? 'selected' : '' }}>Niece</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="feather icon-save me-2"></i> Save Emergency Info
                </button>
            </form>
        </div>
    </div>
</div>

         <!-- Wallet Info Tab Content -->
         <div class="tab-pane fade" id="v-pills-wallet-info" role="tabpanel" aria-labelledby="v-pills-wallet-info-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">Wallet Info</h5>
               </div>
               <div class="card-body">
                  <p class="text-muted">Manage your wallet information below.</p>
                  <div class="row">
                     <!-- Wallet Balance -->
                     <div class="col-md-6">
                        <label for="wallet_balance">Wallet Balance</label>
                        <input type="text" class="form-control" id="wallet_balance" name="wallet_balance" value="500.00" disabled>
                     </div>
                     <!-- Transaction History -->
                     <div class="col-md-6">
                        <label for="wallet_transaction_history">Recent Transactions</label>
                        <ul class="list-unstyled" id="wallet_transaction_history">
                           <li class="d-flex justify-content-between">
                              <span>01 Dec 2024: $50.00 (Deposit)</span>
                           </li>
                           <li class="d-flex justify-content-between">
                              <span>25 Nov 2024: $20.00 (Withdrawal)</span>
                           </li>
                           <li class="d-flex justify-content-between">
                              <span>15 Nov 2024: $100.00 (Deposit)</span>
                           </li>
                           <li class="d-flex justify-content-between">
                              <span>10 Nov 2024: $30.00 (Withdrawal)</span>
                           </li>
                           <li class="d-flex justify-content-between">
                              <span>01 Nov 2024: $200.00 (Deposit)</span>
                           </li>
                        </ul>
                     </div>
                  </div>
                  <!-- Add Funds Button -->
                  <button type="button" class="btn btn-primary font-16" data-bs-toggle="modal" data-bs-target="#addFundsModal">
                  <i class="feather icon-plus me-2"></i>Add Funds
                  </button>
               </div>
            </div>
         </div>
      
         <!-- Modal for Adding Funds to Wallet -->
         <div class="modal fade" id="addFundsModal" tabindex="-1" aria-labelledby="addFundsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="addFundsModalLabel">Add Funds to Wallet</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                     <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                           <label for="amount" class="form-label">Amount</label>
                           <input type="number" class="form-control" id="amount" name="amount" required min="1">
                        </div>
                        <button type="submit" class="btn btn-primary">Add Funds</button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- End col for profile content -->
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
   
   function populatePrograms(facultyId) {
      const programSelect = document.getElementById('program_id');
      
      // Reset program dropdown
      programSelect.innerHTML = '<option value="">Select Program</option>';
      
      // If no faculty selected, disable and return
      if (!facultyId) {
         programSelect.disabled = true;
         return;
      }
     
      // Fetch and populate programs for the selected faculty
      const route = `{{ route('get-programs', ':facultyId') }}`.replace(':facultyId', facultyId);
   
      // Fetch the programs for the selected faculty
      fetch(route)
         .then(response => response.json())
         .then(programs => {
            programs.forEach(program => {
               const option = document.createElement('option');
               option.value = program.id;
               option.textContent = program.name_en;
               programSelect.appendChild(option);
            });
            programSelect.disabled = false;
         })
         .catch(error => {
            console.error('Error fetching programs:', error);
         });
   }
   
   // JavaScript to populate cities based on the selected governorate
   document.getElementById('governorate_id').addEventListener('change', function () {
      const governorateId = this.value;
   
      // Clear existing options in the select element
      const cityList = document.getElementById('city_id');
      cityList.innerHTML = '<option value="">Select City</option>'; // Reset dropdown
   
      if (!governorateId) {
         return; // Exit if no governorate is selected
      }
   
      // Fetch cities for the selected governorate
      const route = `{{ route('get-cities', ':governorateId') }}`.replace(':governorateId', governorateId);
   
      fetch(route)
         .then(response => {
            if (!response.ok) {
               throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
         })
         .then(data => {
            data.forEach(city => {
               const option = document.createElement('option');
               option.value = city.id; // Assign city ID for backend submission
               option.textContent = city.name_en;
               cityList.appendChild(option);
            });
         })
         .catch(error => console.error('Error fetching cities:', error));
   });
   
   // JavaScript to populate cities based on the selected governorate for parent location
   document.getElementById('parent_governorate_id').addEventListener('change', function () {
      const governorateId = this.value;
   
      // Clear existing options in the select element
      const cityList = document.getElementById('parent_city_id');
      cityList.innerHTML = '<option value="">Select City</option>'; // Reset dropdown
   
      if (!governorateId) {
         return; // Exit if no governorate is selected
      }
   
      // Fetch cities for the selected governorate
      const route = `{{ route('get-cities', ':governorateId') }}`.replace(':governorateId', governorateId);
   
      fetch(route)
         .then(response => {
            if (!response.ok) {
               throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
         })
         .then(data => {
            data.forEach(city => {
               const option = document.createElement('option');
               option.value = city.id; // Assign city ID for backend submission
               option.textContent = city.name_en;
               cityList.appendChild(option);
            });
         })
         .catch(error => console.error('Error fetching cities:', error));
   });
   
   document.getElementById('parent_living_abroad').addEventListener('change', function () {
      const abroadFieldContainer = document.getElementById('abroad_country_container');
      const abroadField = document.getElementById('parent_parent_abroad_country_id');
      if (this.value === "1") { // If "Yes" is selected
         abroadFieldContainer.style.display = '';
         abroadField.disabled = false;
      } else { // If "No" is selected
         abroadFieldContainer.style.display = 'none';
         abroadField.disabled = true;
      }
   });
   
   
   document.getElementById('parent_living_with').addEventListener('change', function () {
    const locationFields_1 = document.getElementById('location_fields_not_fill');
    const locationFields_2 = document.getElementById('location_fields_fill');
   
    // Show/hide location fields only if they exist
    if (locationFields_1) {
        if (this.value === '0') { // Show location field 1
            locationFields_1.style.display = '';
        } else { // Hide location field 1
            locationFields_1.style.display = 'none';
        }
    }
   
    if (locationFields_2) {
        if (this.value === '0') { // Show location field 2
            locationFields_2.style.display = '';
        } else { // Hide location field 2
            locationFields_2.style.display = 'none';
        }
    }
   });
   
</script>
@endsection