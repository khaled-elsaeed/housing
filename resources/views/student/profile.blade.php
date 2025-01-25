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
               <a class="nav-link mb-2 active" id="v-pills-profile-tab" data-bs-toggle="pill"
                  href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
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
         <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">@lang('My Profile')</h5>
               </div>
               <div class="card-body">
                  <div class="profilebox pt-4 text-center">
                     <img src="{{ $user->profile_picture ? asset($user->profile_picture) : asset('images/users/boy.svg') }}" 
                        class="img-fluid mb-3 rounded-circle" 
                        style="width: 150px; height: 150px; object-fit: cover;" 
                        alt="@lang('user')">
                     <ul class="list-inline">
                        <li class="list-inline-item">
                           <a href="#" class="btn btn-success-rgba font-18" data-bs-toggle="modal" data-bs-target="#profilePicModal">
                           <i class="feather icon-edit"></i> @lang('Change Picture')
                           </a>
                        </li>
                        @if($user->profile_picture)
                        <li class="list-inline-item">
                           <form action="{{ route('student.profile.delete-picture') }}" method="POST" style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger-rgba font-18" 
                                 onclick="return confirm('@lang('Are you sure you want to delete your profile picture?')')">
                              <i class="feather icon-trash"></i> @lang('Delete Picture')
                              </button>
                           </form>
                        </li>
                        @endif
                     </ul>
                  </div>
               </div>
               <!-- Profile Picture Modal -->
               <div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" >
                  <div class="modal-dialog">
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
                  <form class="row g-3" method="POST" action="{{ route('student.profile.update') }}">
                     @csrf
                     @method('PUT')
                     <div class="col-md-6">
                        <label for="first_name">@lang('First Name')</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="{{ old('first_name', $user->first_name_en) }}" required>
                     </div>
                     <div class="col-md-6">
                        <label for="last_name">@lang('Last Name')</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="{{ old('last_name', $user->last_name_en) }}" required>
                     </div>
                     <div class="col-md-6">
                        <label for="email">@lang('Email')</label>
                        <input type="email" class="form-control" id="email" name="email" 
                           value="{{ old('email', $user->email) }}" required>
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
                              placeholder="@lang('Enter New Password')">
                        </div>
                        <div class="col-md-6">
                           <label for="password_confirmation">@lang('Confirm Password')</label>
                           <input type="password" class="form-control" id="password_confirmation" 
                              name="password_confirmation" placeholder="@lang('Confirm New Password')">
                        </div>
                     </div>
                     <div class="col-12">
                        <button type="submit" class="btn btn-primary font-16">
                        <i class="feather icon-save me-2"></i> @lang('Update')
                        </button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <!-- End My Profile Tab -->
         <!-- My Address Tab -->
         <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">@lang('My Address')</h5>
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
               <div class="card-header">
                  <h5 class="card-title mb-0">@lang('Academic Info')</h5>
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
                  <h5 class="card-title mb-0">@lang('Parent Information')</h5>
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
                     </div>
                  </form>
                  @else
                  <p>@lang('No parent information available')</p>
                  @endif
               </div>
            </div>
         </div>
         <!-- Sibling Info -->
         <div class="tab-pane fade" id="v-pills-sibling-info" role="tabpanel" aria-labelledby="v-pills-sibling-info-tab">
            <div class="card m-b-30">
               <div class="card-header">
                  <h5 class="card-title mb-0">@lang('Sibling Information')</h5>
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
               <div class="card-header">
                  <h5 class="card-title mb-0">@lang('Emergency Information')</h5>
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
                                @lang('Spring') 2024-2025 - @lang($invoice->reservation->term)
                            </h6>
                            <!-- Info Section: Total and Status -->
                            <div class="d-flex justify-content-between mb-2">
                                <p class="mb-0">
                                    <strong>@lang('Total:')</strong> 
                                    <span class="text-success">{{ $invoice->totalAmount() }}</span>
                                </p>
                                <p class="mb-0">
                                    <strong>@lang('Status:')</strong> 
                                    <span class="{{ $invoice->status == 'paid' ? 'text-success' : 'text-danger' }}">
                                        @lang($invoice->status)
                                    </span>
                                </p>
                            </div>
                        </div>
                        <!-- Footer with Buttons -->
                        <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                            @if($invoice->status == 'unpaid')
                            <!-- Pay Now Button with Icon -->
                            <button class="btn btn-outline-primary btn-sm pay-now-btn" data-invoice-id="{{ $invoice->id }}">
                                <i class="fa fa-credit-card"></i> @lang('Pay Now')
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

    // File Upload for Pay Now
    $('.pay-now-btn').click(function(e) {
        e.preventDefault();
        const invoiceId = $(this).data('invoice-id');

        // Store the invoice data on the modal
        $('#fileUploadModal')
            .data('invoice-id', invoiceId)
            .modal('show');
    });

    // Real-time image validation on file input change
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        validateImageFile(file, this);
    });

    // Payment File Upload Form
    $('#fileUploadForm').submit(function(e) {
        e.preventDefault();
        const file = $('#uploadInvoiceReceipt')[0].files[0];
        const invoiceId = $('#fileUploadModal').data('invoice-id');

        if (!validateImageFile(file, '#uploadInvoiceReceipt')) {
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
                    $('#fileUploadForm')[0].reset();
                });
            },
            error: function(xhr, status, error) { 
                console.log(xhr.responseText);
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