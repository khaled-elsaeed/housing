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
               <!-- Profile Tab (Always visible) -->
               <a class="nav-link mb-2 active" id="v-pills-profile-tab" data-bs-toggle="pill"
                  href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="true">
               <i class="feather icon-user me-2"></i>{{ __('pages.student.profile.profile') }}
               </a>
               <!-- My Address Tab (Visible only if governorate or street exists) -->
               @if(optional($user->student->governorate)->name_en || $user->student->street)
               <a class="nav-link mb-2" id="v-pills-address-tab" data-bs-toggle="pill"
                  href="#v-pills-address" role="tab" aria-controls="v-pills-address" aria-selected="false">
               <i class="feather icon-map-pin me-2"></i>{{ __('pages.student.profile.address') }}
               </a>
               @endif
               <!-- Academic Info Tab (Visible only if program exists) -->
               @if(optional($user->student)->program)
               <a class="nav-link mb-2" id="v-pills-academic-info-tab" data-bs-toggle="pill"
                  href="#v-pills-academic-info" role="tab" aria-controls="v-pills-academic-info" aria-selected="false">
               <i class="feather icon-book-open me-2"></i>{{ __('pages.student.profile.academic_info') }}
               </a>
               @endif
               <!-- Parent Info Tab (Visible only if parent relation exists) -->
               @if(optional($user->parent)->relation)
               <a class="nav-link mb-2" id="v-pills-parent-info-tab" data-bs-toggle="pill"
                  href="#v-pills-parent-info" role="tab" aria-controls="v-pills-parent-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('pages.student.profile.parent_info') }}
               </a>
               @endif
               <!-- Sibling Info Tab (Visible only if sibling info exists) -->
               @if(optional($user->sibling)->gender && $user->sibling)
               <a class="nav-link mb-2" id="v-pills-sibling-info-tab" data-bs-toggle="pill"
                  href="#v-pills-sibling-info" role="tab" aria-controls="v-pills-sibling-info" aria-selected="false">
               <i class="feather icon-users me-2"></i>{{ __('pages.student.profile.sibling_info') }}
               </a>
               @endif
               <!-- Emergency Info Tab (Visible only if parent living_abroad is true and emergency contact exists) -->
               @if(optional($user->parent)->living_abroad == 1 && optional($user->EmergencyContact) || optional($user->parent)->living_abroad !== null)
               <a class="nav-link mb-2" id="v-pills-emergency-info-tab" data-bs-toggle="pill"
                  href="#v-pills-emergency-info" role="tab" aria-controls="v-pills-emergency-info" aria-selected="false">
               <i class="feather icon-alert-triangle me-2"></i>{{ __('pages.student.profile.emergency_info') }}
               </a>
               @endif
               @if($user->reservation && $user->reservation->invoice) <!-- Check if the user has invoices -->
    <a class="nav-link mb-2" id="v-pills-payments-info-tab" data-bs-toggle="pill"
       href="#v-pills-payments-info" role="tab" aria-controls="v-pills-payments-info" aria-selected="false">
        <i class="feather icon-credit-card me-2"></i>{{ __('pages.student.profile.payments_info') }}
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
                    <h5 class="card-title mb-0">{{ __('pages.student.profile.my_profile') }}</h5>
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
                                <i class="feather icon-edit"></i> {{ __('pages.student.profile.change_picture') }}
                                </a>
                            </li>
                            <!-- Delete Profile Picture Button -->
                            @if($user->profile_picture)
                            <li class="list-inline-item">
                                <form action="#" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger-rgba font-18">
                                    <i class="feather icon-trash"></i> {{ __('pages.student.profile.delete_picture') }}
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
                                <h5 class="modal-title" id="profilePicModalLabel">{{ __('pages.student.profile.change_profile_picture') }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="#" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="profile_picture" class="form-label">{{ __('pages.student.profile.select_new_picture') }}</label>
                                        <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('pages.student.profile.upload_new_picture') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Profile Information -->
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('pages.student.profile.edit_profile_information') }}</h5>
                </div>
                <div class="card-body">
                    <form class="row g-3" method="POST" action="#">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label for="first_name">{{ __('pages.student.profile.first_name') }}</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name_en }}">
                        </div>
                        <div class="col-md-6">
                            <label for="last_name">{{ __('pages.student.profile.last_name') }}</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name_en }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email">{{ __('pages.student.profile.email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>
                        <!-- Password Fields -->
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary mb-3" id="toggle-password-btn">
                            {{ __('pages.student.profile.change_password') }}
                            </button>
                        </div>
                        <div id="password-section" style="display: none;">
                            <div class="col-md-6">
                                <label for="password">{{ __('pages.student.profile.password') }}</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="{{ __('pages.student.profile.enter_new_password') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation">{{ __('pages.student.profile.confirm_password') }}</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="{{ __('pages.student.profile.confirm_new_password') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary font-16">
                        <i class="feather icon-save me-2"></i>{{ __('pages.student.profile.update') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <!-- End My Profile Tab -->
        
        <!-- My Address -->
        <div class="tab-pane fade" id="v-pills-address" role="tabpanel" aria-labelledby="v-pills-address-tab">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('pages.student.profile.my_address') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.updateAddress') }}">
                        @csrf
                        @method('PUT')
                        <!-- Governorate Field -->
                        @if(!empty($user->student->governorate) && !empty($user->student->governorate->name_en))
                        <div class="mb-3">
                            <label for="governorate">{{ __('pages.student.profile.governorate') }}</label>
                            <input type="text" class="form-control" id="governorate" name="governorate" value="{{ $user->student->governorate->name_en }}" disabled>
                        </div>
                        @endif
                        <!-- City Field -->
                        @if(!empty($user->student->city) && !empty($user->student->city->name_en))
                        <div class="mb-3">
                            <label for="city">{{ __('pages.student.profile.city') }}</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ $user->student->city->name_en }}" disabled>
                        </div>
                        @endif
                        <!-- Street Field -->
                        @if(!empty($user->student->street))
                        <div class="mb-3">
                            <label for="street">{{ __('pages.student.profile.street') }}</label>
                            <input type="text" class="form-control" id="street" name="street" value="{{ $user->student->street }}" disabled>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <!-- Academic Info Tab Content -->
        <div class="tab-pane fade" id="v-pills-academic-info" role="tabpanel" aria-labelledby="v-pills-academic-info-tab">
            <div class="card m-b-30">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('pages.student.profile.academic_info') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('pages.student.profile.manage_academic_details') }}</p>
                    <form class="row g-3" method="POST" action="{{ route('student.updateAcademicInfo') }}">
                        @csrf
                        @method('PUT')
                        <!-- Faculty Field -->
                        @if(!empty(optional($user->student)->faculty))
                        <div class="col-md-6">
                            <label for="faculty">{{ __('pages.student.profile.faculty') }}</label>
                            <input type="text" class="form-control" id="faculty" name="faculty" value="{{ optional($user->student)->faculty->name_en }}" disabled>
                            <input type="hidden" name="faculty_id" value="{{ optional($user->student)->faculty_id }}">
                        </div>
                        @endif
                        <!-- Program Field -->
                        @if(!empty(optional($user->student)->program))
                        <div class="col-md-6">
                            <label for="program">{{ __('pages.student.profile.program') }}</label>
                            <input type="text" class="form-control" id="program" name="program" value="{{ optional($user->student)->program->name_en }}" disabled>
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
                    <h5 class="card-title mb-0">{{ __('pages.student.profile.parent_info') }}</h5>
                </div>
                <div class="card-body">
                    @if($user->parent)  <!-- Check if parent record exists -->
                    <form method="POST" action="{{ route('student.updateParentInfo') }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Parent Name -->
                            <div class="col-md-6">
                                <label for="parent_name">{{ __('pages.student.profile.parent_name') }}</label>
                                <input type="text" class="form-control" id="parent_name" name="parent_name" 
                                    value="{{ old('parent_name', $user->parent->name) }}" disabled>
                            </div>
                            <!-- Parent Relation -->
                            <div class="col-md-6">
                                <label for="parent_relation">{{ __('pages.student.profile.parent_relation') }}</label>
                                <input type="text" class="form-control" id="parent_relation" name="parent_relation" 
                                    value="{{ old('parent_relation', $user->parent->relation) }}" disabled>
                            </div>
                            <!-- Parent Email -->
                            <div class="col-md-6">
                                <label for="parent_email">{{ __('pages.student.profile.parent_email') }}</label>
                                <input type="email" class="form-control" id="parent_email" name="parent_email" 
                                    value="{{ old('parent_email', $user->parent->email) }}" disabled>
                            </div>
                            <!-- Parent Mobile -->
                            <div class="col-md-6">
                                <label for="parent_mobile">{{ __('pages.student.profile.parent_mobile') }}</label>
                                <input type="text" class="form-control" id="parent_mobile" name="parent_mobile" 
                                    value="{{ old('parent_mobile', $user->parent->mobile) }}" disabled>
                            </div>
                            <!-- Living Abroad -->
                            <div class="col-md-6">
                                <label for="parent_living_abroad">{{ __('pages.student.profile.living_abroad') }}</label>
                                <input type="text" class="form-control" id="parent_living_abroad" name="parent_living_abroad" 
                                    value="{{ old('parent_living_abroad', $user->parent->living_abroad == 1 ? __('pages.student.profile.yes') : __('pages.student.profile.no')) }}" disabled>
                            </div>
                        </div>
                    </form>
                    @else
                    <p>{{ __('pages.student.profile.no_parent_info') }}</p>
                    @endif
                </div>
            </div>
        </div>
 

         <!-- Sibling Info -->
<div class="tab-pane fade" id="v-pills-sibling-info" role="tabpanel" aria-labelledby="v-pills-sibling-info-tab">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('pages.student.profile.sibling_info') }}</h5>
        </div>
        <div class="card-body">
            @if($user->sibling)  <!-- Check if sibling record exists -->
            <form method="POST" action="{{ route('student.updateOrCreateSiblingInfo') }}">
                @csrf
                <div class="row">
                    <!-- Sibling Gender -->
                    <div class="col-md-6">
                        <label for="sibling_gender">{{ __('pages.student.profile.sibling_gender') }}</label>
                        <select class="form-control" id="sibling_gender" name="sibling_gender">
                            <option value="brother" {{ old('sibling_gender', $user->sibling->gender) == 'brother' ? 'selected' : '' }}>{{ __('pages.student.profile.brother') }}</option>
                            <option value="sister" {{ old('sibling_gender', $user->sibling->gender) == 'sister' ? 'selected' : '' }}>{{ __('pages.student.profile.sister') }}</option>
                        </select>
                    </div>
                    <!-- Sibling Name -->
                    <div class="col-md-6">
                        <label for="sibling_name">{{ __('pages.student.profile.sibling_name') }}</label>
                        <input type="text" class="form-control" id="sibling_name" name="sibling_name" value="{{ old('sibling_name', $user->sibling->name) }}">
                    </div>
                    <!-- Sibling National ID -->
                    <div class="col-md-6">
                        <label for="sibling_national_id">{{ __('pages.student.profile.sibling_national_id') }}</label>
                        <input type="text" class="form-control" id="sibling_national_id" name="sibling_national_id" value="{{ old('sibling_national_id', $user->sibling->national_id) }}">
                    </div>
                    <!-- Sibling Faculty -->
                    <div class="col-md-6">
                        <label for="sibling_faculty">{{ __('pages.student.profile.sibling_faculty') }}</label>
                        <input type="text" class="form-control" id="sibling_faculty" name="sibling_faculty" value="{{ old('sibling_faculty', optional($user->sibling->faculty)->name_en) }}">
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="col-md-12 text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('pages.student.profile.save_changes') }}</button>
                </div>
            </form>
            @else
            <!-- If no sibling info exists -->
            <p class="text-muted">{{ __('pages.student.profile.no_sibling_info') }}</p>
            @endif
        </div>
    </div>
</div>
<!-- End Sibling Info Tab -->

         <!-- Emergency Info -->
<div class="tab-pane fade" id="v-pills-emergency-info" role="tabpanel" aria-labelledby="v-pills-emergency-info-tab">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('pages.student.profile.emergency_info') }}</h5>
        </div>
        <div class="card-body">
            @if($user->emergencyContact)  <!-- Check if emergency contact data exists -->
            <form method="POST" action="{{ route('student.updateOrCreateEmergencyInfo') }}">
                @csrf
                <div class="row">
                    <!-- Emergency Contact Name -->
                    <div class="col-md-6">
                        <label for="emergency_contact_name">{{ __('pages.student.profile.emergency_contact_name') }}</label>
                        <input type="text" class="form-control" id="emergency_contact_name"
                            name="emergency_contact_name" value="{{ old('emergency_contact_name', $user->emergencyContact->name ?? '') }}">
                    </div>
                    <!-- Emergency Phone -->
                    <div class="col-md-6">
                        <label for="emergency_phone">{{ __('pages.student.profile.emergency_phone') }}</label>
                        <input type="text" class="form-control" id="emergency_phone" name="emergency_phone"
                            value="{{ old('emergency_phone', $user->emergencyContact->phone ?? '') }}">
                    </div>
                    <!-- Relationship Dropdown -->
                    <div class="col-md-6">
                        <label for="relationship">{{ __('pages.student.profile.relationship') }}</label>
                        <select class="form-control" id="relationship" name="relationship">
                            <option value="uncle" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'uncle' ? 'selected' : '' }}>{{ __('pages.student.profile.uncle') }}</option>
                            <option value="aunt" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'aunt' ? 'selected' : '' }}>{{ __('pages.student.profile.aunt') }}</option>
                            <option value="grandparent" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'grandparent' ? 'selected' : '' }}>{{ __('pages.student.profile.grandparent') }}</option>
                            <option value="other" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'other' ? 'selected' : '' }}>{{ __('pages.student.profile.other') }}</option>
                            <option value="spouse" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'spouse' ? 'selected' : '' }}>{{ __('pages.student.profile.spouse') }}</option>
                            <option value="nephew" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'nephew' ? 'selected' : '' }}>{{ __('pages.student.profile.nephew') }}</option>
                            <option value="cousin" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'cousin' ? 'selected' : '' }}>{{ __('pages.student.profile.cousin') }}</option>
                            <option value="niece" {{ old('relationship', $user->emergencyContact->relation ?? '') == 'niece' ? 'selected' : '' }}>{{ __('pages.student.profile.niece') }}</option>
                        </select>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary mt-3">
                <i class="feather icon-save me-2"></i>{{ __('pages.student.profile.save_emergency_info') }}
                </button>
            </form>
            @else
            <!-- If no emergency contact info exists -->
            <p class="text-muted">{{ __('pages.student.profile.no_emergency_info') }}</p>
            @endif
        </div>
    </div>
</div>
<!-- End Emergency Info Tab -->

<!-- Payments Info Tab -->
<div class="tab-pane fade" id="v-pills-payments-info" role="tabpanel" aria-labelledby="v-pills-payments-info-tab">
    <div class="card shadow border-0 rounded-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">{{ __('pages.student.profile.payments_info') }}</h5>
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
                                <span class="text-muted">{{ __('pages.student.profile.year') }}:</span> 
                                <span class="text-dark fw-bold">2024/2025</span>
                            </h6>
                            <p class="mb-1">
                                <span class="text-muted">{{ __('pages.student.profile.term') }}:</span> 
                                <span class="text-dark">{{ ucwords(str_replace('_', ' ', $firstTermInvoice->term)) }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>{{ __('pages.student.profile.amount') }}:</strong> 
                                <span class="text-success">${{ number_format($firstTermInvoice->amount, 2) }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>{{ __('pages.student.profile.status') }}:</strong> 
                                <span class="badge {{ $firstTermInvoice->status == 'paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2">
                                    {{ __('pages.student.profile.' . $firstTermInvoice->status) }}
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
        {{ __('pages.student.profile.upload_payment') }}
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
                                <span class="text-muted">{{ __('pages.student.profile.year') }}:</span> 
                                <span class="text-dark fw-bold">2024/2025</span>
                            </h6>
                            <p class="mb-1">
                                <span class="text-muted">{{ __('pages.student.profile.term') }}:</span> 
                                <span class="text-dark">{{ __('pages.student.profile.second_term') }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>{{ __('pages.student.profile.amount') }}:</strong> 
                                <span class="text-success">${{ number_format($secondTermInvoice->amount, 2) }}</span>
                            </p>
                            <p class="mb-1">
                                <strong>{{ __('pages.student.profile.status') }}:</strong> 
                                <span class="badge {{ $secondTermInvoice->status == 'paid' ? 'badge-success' : 'badge-warning' }} px-3 py-2">
                                    {{ __('pages.student.profile.' . $secondTermInvoice->status) }}
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
        {{ __('pages.student.profile.upload_payment') }}
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
                                    <span class="text-muted">{{ __('pages.student.profile.year') }}:</span> 
                                    <span class="text-dark fw-bold">2024/2025</span>
                                </h6>
                                <p class="mb-1">
                                    <span class="text-muted">{{ __('pages.student.profile.term') }}:</span> 
                                    <span class="text-dark">{{ __('pages.student.profile.second_term') }}</span>
                                </p>
                                <p class="mb-1">
    <span class="text-muted">{{ __('pages.student.profile.term') }}:</span> 
    <span class="text-dark">{{ __('pages.student.profile.second_term') }}</span>
</p>
<p class="mb-1">
    <strong>{{ __('pages.student.profile.amount') }}:</strong> 
    <span class="text-success">
        @if(isset($firstTermInvoice) && $firstTermInvoice->amount)
            ${{ number_format($firstTermInvoice->amount, 2) }}
        @else
            {{ __('pages.student.profile.no_amount') }}
        @endif
    </span>
</p>
<p class="mb-1">
    <strong>{{ __('pages.student.profile.status') }}:</strong> 
    <span class="badge badge-warning px-3 py-2">
        {{ __('pages.student.profile.unpaid') }}
    </span>
</p>
</div>
<div class="col-md-4 text-center">
    <form id="paymentForm" method="POST" action="{{ route('student.uploadPayment') }}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="payment_receipt" id="payment_receipt" class="form-control mb-2" required>
        <input type="hidden" name="term" value="second_term">
        <button type="submit" class="btn btn-primary btn-block">
            {{ __('pages.student.profile.upload_payment') }}
        </button>
    </form>
</div>
</div>
</div>
@endif
@else
                <div class="alert alert-info text-center">
                    {{ __('pages.student.profile.no_invoices') }}
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
           this.textContent = 'Cancel Password Change';
       } else {
           passwordSection.style.display = 'none';
           this.textContent = 'Change Password';
       }
   });
   


    
   
</script>
@endsection