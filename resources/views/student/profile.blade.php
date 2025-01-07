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
                @if($user->reservation && $user->reservation->invoice)
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
                <div class="modal fade" id="profilePicModal" tabindex="-1" aria-labelledby="profilePicModalLabel" aria-hidden="true">
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
         <div class="tab-pane fade" id="v-pills-payments-info" role="tabpanel" aria-labelledby="v-pills-payments-info-tab">
            <div class="card shadow border-0 rounded-lg">
               <div class="card-header bg-primary text-white">
                  <h5 class="card-title mb-0">@lang('Payments Info')</h5>
               </div>
               <div class="card-body">
                  <!-- Payment Form for Both Terms -->
                  @if($user->reservation && $user->reservation->invoice)
                  @php 
                  $firstTermInvoice = $user->reservation->invoice()->where('term', 'first_term')->first(); 
                  $secondTermInvoice = $user->reservation->invoice()->where('term', 'second_term')->first(); 
                  @endphp
                  <!-- First Term Invoice Information -->
                  @if($firstTermInvoice)
                  <div class="invoice-card border rounded p-3 mb-4 shadow-sm">
                     <div class="row align-items-center">
                        <div class="col-md-8">
                           <h6 class="mb-1">
                              <span class="text-muted">@lang('Year:')</span> 
                              <span class="text-dark fw-bold">2024/2025</span>
                           </h6>
                           <p class="mb-1">
                              <span class="text-muted">@lang('Term:')</span> 
                              <span class="text-dark">{{ ucwords(str_replace('_', ' ', $firstTermInvoice->term)) }}</span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Amount:')</strong> 
                              <span class="text-success">${{ number_format($firstTermInvoice->amount, 2) }}</span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Status:')</strong> 
                              <span class="badge {{ $firstTermInvoice->status == 'paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2">
                              {{ $firstTermInvoice->status }}
                              </span>
                           </p>
                        </div>
                        <div class="col-md-4 text-center">
                           @if($firstTermInvoice->status == 'unpaid')
                           <form id="paymentForm" method="POST" action="{{ route('student.uploadPayment') }}" enctype="multipart/form-data">
                              @csrf
                              <input type="file" name="payment_receipt" id="payment_receipt" class="form-control mb-2" required>
                              <input type="hidden" name="term" value="first_term">
                              <input type="hidden" name="invoice_id" value="{{ $firstTermInvoice->id }}">
                              <button type="submit" class="btn btn-primary btn-block">
                              @lang('Upload Payment')
                              </button>
                           </form>
                           @endif
                        </div>
                     </div>
                  </div>
                  @endif
                  <!-- Second Term Invoice Information -->
                  @if($secondTermInvoice)
                  <div class="invoice-card border rounded p-3 mb-4 shadow-sm">
                     <div class="row align-items-center">
                        <div class="col-md-8">
                           <h6 class="mb-1">
                              <span class="text-muted">@lang('Year:')</span> 
                              <span class="text-dark fw-bold">2024/2025</span>
                           </h6>
                           <p class="mb-1">
                              <span class="text-muted">@lang('Term:')</span> 
                              <span class="text-dark">@lang('Second Term')</span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Amount:')</strong> 
                              <span class="text-success">${{ number_format($secondTermInvoice->amount, 2) }}</span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Status:')</strong> 
                              <span class="badge {{ $secondTermInvoice->status == 'paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2">
                              {{ $secondTermInvoice->status }}
                              </span>
                           </p>
                        </div>
                        <div class="col-md-4 text-center">
                           @if($secondTermInvoice->status == 'unpaid')
                           <form id="paymentForm" method="POST" action="{{ route('student.uploadPayment') }}" enctype="multipart/form-data">
                              @csrf
                              <input type="file" name="payment_receipt" id="payment_receipt" class="form-control mb-2" required>
                              <input type="hidden" name="term" value="second_term">
                              <input type="hidden" name="invoice_id" value="{{ $secondTermInvoice->id }}">
                              <button type="submit" class="btn btn-primary btn-block">
                              @lang('Upload Payment')
                              </button>
                           </form>
                           @endif
                        </div>
                     </div>
                  </div>
                  @else
                  <!-- Static Second Term Invoice (if not present) -->
                  <div class="invoice-card border rounded p-3 mb-4 shadow-sm">
                     <div class="row align-items-center">
                        <div class="col-md-8">
                           <h6 class="mb-1">
                              <span class="text-muted">@lang('Year:')</span> 
                              <span class="text-dark fw-bold">2024/2025</span>
                           </h6>
                           <p class="mb-1">
                              <span class="text-muted">@lang('Term:')</span> 
                              <span class="text-dark">@lang('Second Term')</span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Amount:')</strong> 
                              <span class="text-success">
                              @if(isset($firstTermInvoice) && $firstTermInvoice->amount)
                              ${{ number_format($firstTermInvoice->amount, 2) }}
                              @else
                              @lang('No amount')
                              @endif
                              </span>
                           </p>
                           <p class="mb-1">
                              <strong>@lang('Status:')</strong> 
                              <span class="badge badge-warning px-3 py-2">
                              @lang('Unpaid')
                              </span>
                           </p>
                        </div>
                        <div class="col-md-4 text-center">
                           <form id="paymentForm" method="POST" action="{{ route('student.uploadPayment') }}" enctype="multipart/form-data">
                              @csrf
                              <input type="file" name="payment_receipt" id="payment_receipt" class="form-control mb-2" required>
                              <input type="hidden" name="term" value="second_term">
                              <button type="submit" class="btn btn-primary btn-block">
                              @lang('Upload Payment')
                              </button>
                           </form>
                        </div>
                     </div>
                  </div>
                  @endif
                  @else
                  <div class="alert alert-info text-center">
                     @lang('No invoices available')
                  </div>
                  @endif
               </div>
            </div>
         </div>
         <!-- End Payments Info Tab -->
      </div>
   </div>
</div>
<!-- End col for profile content -->
@endsection
@section('scripts')
<script>
   document.getElementById('toggle-password-btn').addEventListener('click', function() {
      const passwordSection = document.getElementById('password-section');
      if (passwordSection.style.display === 'none' || passwordSection.style.display === '') {
          passwordSection.style.display = 'block';
          this.textContent = "@lang('Cancel Password Change')";
      } else {
          passwordSection.style.display = 'none';
          this.textContent = "@lang('Change Password')";
      }
   });
</script>
@endsection