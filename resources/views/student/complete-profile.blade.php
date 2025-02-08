@extends('layouts.student-without-leftbar')
@section('title', __('Complete Profile'))
@section('links')
<link rel="stylesheet" href="{{ asset('css/complete-profile.css') }}">
@endsection
@section('content')
<div class="container py-2 py-sm-2 py-md-3">
   <div class="row justify-content-center mt-2 mt-md-5">
      <div class="col-lg-10 col-md-10 col-sm-6">
         <div class="form-card p-4">
            <div class="row flex-column flex-md-row">
               <!-- Vertical Nav Tabs -->
               <div class="col-12 col-md-5">
                  <div class="nav flex-column nav-tabs nav-pills nav-justified gap-1" role="tablist">
                     <!-- Personal Info Tab -->
                     <a class="nav-link active" id="step1-tab" data-bs-toggle="tab" href="#step1" role="tab" data-step="1">
                     <i class="fa fa-user"></i> 
                     <span>{{ __('Personal Info') }}</span>
                     </a>
                     <!-- Contact Details Tab -->
                     <a class="nav-link disabled" id="step2-tab" data-bs-toggle="tab" href="#step2" role="tab" data-step="2">
                     <i class="fa fa-address-card"></i> 
                     <span>{{ __('Contact Details') }}</span>
                     </a>
                     <!-- Academic Info Tab -->
                     <a class="nav-link disabled" id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab" data-step="3">
                     <i class="fa fa-graduation-cap"></i> 
                     <span>{{ __('Academic Info') }}</span>
                     </a>
                     <!-- Parent Info Tab -->
                     <a class="nav-link disabled" id="step4-tab" data-bs-toggle="tab" href="#step4" role="tab" data-step="4">
                     <i class="fa fa-users"></i> 
                     <span>{{ __('Parent Info') }}</span>
                     </a>
                     <!-- Sibling Info Tab -->
                     <a class="nav-link disabled" id="step5-tab" data-bs-toggle="tab" href="#step5" role="tab" data-step="5">
                     <i class="fa fa-child"></i> 
                     <span>{{ __('Sibling Info') }}</span>
                     </a>
                     <!-- Emergency Contact Info Tab -->
                     <a class="nav-link disabled d-none" id="step6-tab" data-bs-toggle="tab" href="#step6" role="tab" data-step="6">
                     <i class="fa fa-phone"></i> 
                     <span>{{ __('Emergency Info') }}</span>
                     </a>
                     <!-- Terms and Conditions Tab -->
                     <a class="nav-link disabled" id="step7-tab" data-bs-toggle="tab" href="#step7" role="tab" data-step="7">
                     <i class="fa fa-exclamation-triangle"></i>
                     <span>{{ __('T&C') }}</span>
                     </a>
                  </div>
               </div>
               <!-- Form Content -->
               <div class="col-12 col-md-7 d-flex flex-column justify-content-between">
                  <form id="multiStepForm" action="{{route('profile.store')}}" novalidate>
                     @csrf
                     <div class="tab-content">
                        <!-- Step 1 - personal info -->
                        <div class="form-step tab-pane fade show active" id="step1">
                           <h4 class="mb-4">{{ __('Personal Information') }}</h4>
                           <!-- Full Name Arabic -->
                           <div class="mb-2">
                              <label for="nameAr" class="form-label">{{ __('Full Name Arabic') }}</label>
                              <input type="text" class="form-control" id="nameAr" name="nameAr" value="{{ old('nameAr', $profileData['personalInformation']['nameAr'] ?? '') }}" readonly>
                              <div class="error-message"></div>
                           </div>
                           <!-- Full Name English -->
                           <div class="mb-2">
                              <label for="nameEn" class="form-label">{{ __('Full Name English') }}</label>
                              <input type="text" class="form-control" id="nameEn" name="nameEn" value="{{ old('nameEn', $profileData['personalInformation']['nameEn'] ?? '') }}" readonly>
                              <div class="error-message"></div>
                           </div>
                           <!-- National ID -->
                           <div class="mb-2">
                              <label for="nationalId" class="form-label">{{ __('National ID') }}</label>
                              <input type="text" class="form-control" id="nationalId" name="nationalId" value="{{ old('nationalId', $profileData['personalInformation']['nationalId'] ?? '') }}" readonly>
                              <div class="error-message"></div>
                           </div>
                           <!-- National ID and Gender in the same row -->
                           <div class="row">
                              <!-- Birth Date -->
                              <div class="col-md-6 mb-2">
                                 <label for="birthDate" class="form-label">{{ __('Birth Date') }}</label>
                                 <input type="date" class="form-control" id="birthDate" name="birthDate" value="{{ old('birthDate', $profileData['personalInformation']['birthDate'] ?? '') }}" readonly>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Gender -->
                              <div class="col-md-6 mb-2">
                                 <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                 <select class="form-control" id="gender" name="gender" readonly>
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" {{ old('gender', $profileData['personalInformation']['gender'] ?? '') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', $profileData['personalInformation']['gender'] ?? '') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                        </div>
                        <!-- Step 2 - contact and location info -->
                        <div class="form-step tab-pane fade" id="step2">
                           <h4 class="mb-4">{{ __('Contact Information') }}</h4>
                           <div class="row">
                              <!-- Governorate (Dropdown) -->
                              <div class="col-md-6 mb-2">
                                 <label for="governorate" class="form-label">{{ __('Governorate') }}</label>
                                 <select class="form-control" id="governorate" name="governorate" required>
                                    <option value="">{{ __('Select Governorate') }}</option>
                                    @foreach ($formData['governorates'] ?? [] as $governorate)
                                    <option value="{{ $governorate->id }}" {{ old('governorate', $profileData['contactInformation']['governorate'] ?? '') == $governorate->id ? 'selected' : '' }}>
                                    {{ $governorate->name }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- City (Dropdown) -->
                              <div class="col-md-6 mb-2">
                                 <label for="city" class="form-label">{{ __('City') }}</label>
                                 @if($profileData['contactInformation']['city'])
                                 <select class="form-control" id="city" name="city" >
                                 @foreach ($formData['cities'] as $city)
                                 <option value="{{ $city->id }}" {{ old('city', $profileData['contactInformation']['city'] ?? '') == $city->id ? 'selected' : '' }}>
                                 {{ $city->name }}
                                 </option>
                                 @endforeach
                                 </select>
                                 @else
                                 <select class="form-control" id="city" name="city"  disabled>
                                    <option value="">{{ __('Select Governorate First') }}</option>
                                 </select>
                                 @endif
                                 <!-- Hidden Datalist -->
                                 <datalist class="d-none" id="city-list">
                                    @foreach ($formData['cities'] as $city)
                                    <option data-governorate-id="{{ $city->governorate_id }}" data-city-id="{{ $city->id }}" value="{{ $city->name }}"></option>
                                    @endforeach
                                 </datalist>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- Street -->
                           <div class="mb-2">
                              <label for="street" class="form-label">{{ __('Street') }}</label>
                              <input type="text" class="form-control" id="street" name="street" value="{{ old('street', $profileData['contactInformation']['street'] ?? '') }}">
                              <div class="error-message"></div>
                           </div>
                           <div class="mb-2">
                              <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="phone" name="phone" 
                                 value="{{ old('phone', $profileData['contactInformation']['phone'] ?? '') }}">
                              <small class="form-text text-muted">{{ __('Enter your phone number in international format (e.g., 01014545865).') }}</small>
                              <div class="error-message"></div>
                           </div>
                        </div>
                        <!-- Step 3 academic info -->
                        <div class="form-step tab-pane fade" id="step3">
                           <h4 class="mb-4">{{ __('Academic Information') }}</h4>
                           <!-- Faculty (Dropdown) -->
                           <div class="row">
                              <div class="col-md-6 mb-2">
                                 <label for="faculty" class="form-label">{{ __('Faculty') }}</label>
                                 <select class="form-control" id="faculty" name="faculty" >
                                    <option value="">{{ __('Select Faculty') }}</option>
                                    @foreach ($formData['faculties'] as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty', $profileData['academicInformation']['faculty'] ?? '') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Program (Dropdown) -->
                              <div class="col-md-6  mb-2">
                                 <label for="program" class="form-label">{{ __('Program') }}</label>
                                 @if($profileData['academicInformation']['program'])
                                 <select class="form-control" id="program" name="program" >
                                 @foreach ($formData['programs'] as $program)
                                 <option value="{{ $program->id }}" {{ old('program', $profileData['academicInformation']['program'] ?? '') == $program->id ? 'selected' : '' }}>
                                 {{ $program->name }}
                                 </option>
                                 @endforeach
                                 </select>
                                 @else
                                 <select class="form-control" id="program" name="program"  disabled>
                                    <option value="">{{ __('Select faculty First') }}</option>
                                 </select>
                                 @endif
                                 <!-- Hidden Datalist -->
                                 <datalist class="d-none" id="faculty-programs">
                                    @foreach ($formData['programs'] as $program)
                                    <option data-faculty-id="{{ $program->faculty->id }}" data-program-id="{{ $program->id }}" value="{{ $program->name }}"></option>
                                    @endforeach
                                 </datalist>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- University ID -->
                           <div class="mb-2">
                              <label for="academicId" class="form-label">{{ __('University ID') }}</label>
                              <input type="text" class="form-control" id="academicId" name="academicId" value="{{ old('academicId', $profileData['academicInformation']['academicId'] ?? '') }}" readonly>
                              <div class="error-message"></div>
                           </div>
                           <!-- University Email -->
                           <div class="mb-2">
                              <label for="academicEmail" class="form-label">{{ __('University Email') }}</label>
                              <input type="email" class="form-control" id="academicEmail" name="academicEmail" value="{{ old('academicEmail', $profileData['academicInformation']['academicEmail'] ?? '') }}" readonly>
                              <div class="error-message"></div>
                           </div>
                           
                        </div>
                        <!-- step 4 parent info -->
                        <div class="form-step tab-pane fade" id="step4">
                           <h4 class="mb-4">{{ __('Parent Information') }}</h4>
                           <!-- Relationship -->
                           <div class="row">
                              <div class="col-md-4 mb-2">
                                 <label for="parentRelationship" class="form-label">{{ __('Relationship') }}</label>
                                 <select name="parentRelationship" id="parentRelationship" class="form-control" >
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="father" {{ old('parentRelationship', $profileData['parentInformation']['parentRelationship'] ?? '') == 'father' ? 'selected' : '' }}>{{ __('Father') }}</option>
                                    <option value="mother" {{ old('parentRelationship', $profileData['parentInformation']['parentRelationship'] ?? '') == 'mother' ? 'selected' : '' }}>{{ __('Mother') }}</option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Name -->
                              <div class="col-md-8 mb-2">
                                 <label for="parentName" class="form-label">{{ __('Name') }}</label>
                                 <input type="text" class="form-control" id="parentName" name="parentName" 
                                    value="{{ old('parentName', $profileData['parentInformation']['parentName'] ?? '') }}" >
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- Phone Number -->
                           <div class="mb-2">
                              <label for="parentPhone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="parentPhone" name="parentPhone" 
                                 value="{{ old('parentPhone', $profileData['parentInformation']['parentPhone'] ?? '') }}" >
                              <div class="error-message"></div>
                           </div>
                           <!-- Email -->
                           <div class="mb-2">
                           <label for="parentEmail" class="form-label">
    {{ __('Email') }} <span class="text-muted">({{ __('Optional') }})</span>
</label>
                              <input type="email" class="form-control" id="parentEmail" name="parentEmail" 
                                 value="{{ old('parentEmail', $profileData['parentInformation']['parentEmail'] ?? '') }}">
                              <div class="error-message"></div>
                           </div>
                           <div class="row">
                              <!-- Is Abroad -->
                              <div class="col-md-6 mb-2">
                                 <label for="isParentAbroad" class="form-label">{{ __('Is the Parent Abroad?') }}</label>
                                 <select name="isParentAbroad" id="isParentAbroad" class="form-control" >
                                    <option value="">{{ __('Select Option') }}</option>
                                    <option value="1" {{ old('isParentAbroad', $profileData['parentInformation']['isParentAbroad'] ?? '') == "1" ? 'selected' : '' }}>
                                    {{ __('Yes') }}
                                    </option>
                                    <option value="0" {{ old('isParentAbroad', $profileData['parentInformation']['isParentAbroad'] ?? '') == "0" ? 'selected' : '' }}>
                                    {{ __('No') }}
                                    </option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Country if Abroad -->
                              <div class="col-md-6 mb-2 d-none" id="abroadCountryDiv">
                                 <label for="abroadCountry" class="form-label">{{ __('Country') }}</label>
                                 <select class="form-control" id="abroadCountry" name="abroadCountry">
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($formData['countries'] ?? [] as $country)
                                    <option value="{{ $country->id }}" 
                                    {{ old('abroadCountry', $profileData['parentInformation']['abroadCountry'] ?? '') == $country->id ? 'selected' : '' }}>
                                    {{ __($country->name) }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Living with Parent if Not Abroad -->
                              <div class="col-md-6 d-none" id="livingWithParentDiv">
                                 <label for="livingWithParent" class="form-label">{{ __('Do You Live With Them?') }}</label>
                                 <select name="livingWithParent" id="livingWithParent" class="form-control">
                                    <option value="">{{ __('Select Option') }}</option>
                                    <option value="1" {{ old('livingWithParent', $profileData['parentInformation']['livingWithParent'] ?? '') == "1" ? 'selected' : '' }}>
                                    {{ __('Yes') }}
                                    </option>
                                    <option value="0" {{ old('livingWithParent', $profileData['parentInformation']['livingWithParent'] ?? '') == "0" ? 'selected' : '' }}>
                                    {{ __('No') }}
                                    </option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- Governorate and City (only shown if not living with them) -->
                           <div class="row d-none" id="parentGovernorateCityDiv">
                              <div class="col-md-6 mb-2">
                                 <label for="parentGovernorate" class="form-label">{{ __('Governorate') }}</label>
                                 <select class="form-control" id="parentGovernorate" name="parentGovernorate" >
                                    <option value="">{{ __('Select Governorate') }}</option>
                                    @foreach ($formData['governorates'] as $governorate)
                                    <option value="{{ $governorate->id }}">
                                       {{ $governorate->name }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- City (Dropdown) -->
                              <div class="col-md-6 mb-2">
                                 <label for="parentCity" class="form-label">{{ __('City') }}</label>
                                 <select class="form-control" id="parentCity" name="parentCity"  disabled>
                                    <option value="">{{ __('Select Governorate First') }}</option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                        </div>
                        <!-- Step 5: Sibling Information -->
                        <div class="form-step tab-pane fade" id="step5">
                           <h4 class="mb-4">{{ __('Sibling Information') }}</h4>
                           <!-- Do you have a sibling in dorm? -->
                           <div class="mb-3">
                              <label for="hasSiblingInDorm" class="form-label">{{ __('Do you have a sibling in the dorm?') }}</label>
                              <select name="hasSiblingInDorm" id="hasSiblingInDorm" class="form-control"  >
                                 <option value="">{{ __('Select Option') }}</option>
                                 <option value="1" {{ old('hasSiblingInDorm', $profileData['siblingInformation']['hasSiblingInDorm'] ?? '') == '1' ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                 <option value="0" {{ old('hasSiblingInDorm', $profileData['siblingInformation']['hasSiblingInDorm'] ?? '') == '0' ? 'selected' : '' }}>{{ __('No') }}</option>
                              </select>
                              <div class="error-message"></div>
                           </div>
                           <!-- Sibling Information - This will be shown if the user has a sibling in the dorm -->
                           <div id="siblingInfoSection" class="d-none">
                              <!-- Relationship -->
                              <div class="row">
                                 <div class="col-md-4 mb-2">
                                    <label for="siblingGender" class="form-label">{{ __('Relationship') }}</label>
                                    <select name="siblingGender" id="siblingGender" class="form-control" >
                                       <option value="">{{ __('Select Relationship') }}</option>
                                       <option value="male" {{ old('siblingGender', $profileData['siblingInformation']['siblingGender'] ?? '') == 'male' ? 'selected' : '' }}>{{ __('Brother') }}</option>
                                       <option value="female" {{ old('siblingGender', $profileData['siblingInformation']['siblingGender'] ?? '') == 'female' ? 'selected' : '' }}>{{ __('Sister') }}</option>
                                    </select>
                                    <div class="error-message"></div>
                                 </div>
                                 <!-- Name -->
                                 <div class="col-md-8 mb-2">
                                    <label for="siblingName" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" id="siblingName" name="siblingName" value="{{ old('siblingName', $profileData['siblingInformation']['siblingName'] ?? '') }}" >
                                    <div class="error-message"></div>
                                 </div>
                              </div>
                              <!-- National ID -->
                              <div class="mb-2">
                                 <label for="siblingNationalId" class="form-label">{{ __('National ID') }}</label>
                                 <input type="text" class="form-control" id="siblingNationalId" name="siblingNationalId" value="{{ old('siblingNationalId', $profileData['siblingInformation']['siblingNationalId'] ?? '') }}">
                                 <div class="error-message"></div>
                              </div>
                              <!-- Faculty -->
                              <div class="mb-2">
                                 <label for="siblingFaculty" class="form-label">{{ __('Faculty') }}</label>
                                 <select class="form-control" id="siblingFaculty" name="siblingFaculty" >
                                    <option value="">{{ __('Select Faculty') }}</option>
                                    @foreach ($formData['faculties'] as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('siblingFaculty', $profileData['siblingInformation']['siblingFaculty'] ?? '') == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                        </div>
                        <!-- Step 6: Emergency Contact Information -->
                        <div class="form-step tab-pane fade d-none" id="step6">
                           <h4 class="mb-4">{{ __('Emergency Contact Information') }}</h4>
                           <!-- Relationship -->
                           <div class="row">
                              <div class="col-md-4 mb-2">
                                 <label for="emergencyContactRelationship" class="form-label">{{ __('Relationship') }}</label>
                                 <select name="emergencyContactRelationship" id="emergencyContactRelationship" class="form-control" >
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="father" {{ old('emergencyContactRelationship', $profileData['emergencyContact']['emergencyContactRelationship'] ?? '') == 'father' ? 'selected' : '' }}>{{ __('Father') }}</option>
                                    <option value="mother" {{ old('emergencyContactRelationship', $profileData['emergencyContact']['emergencyContactRelationship'] ?? '') == 'mother' ? 'selected' : '' }}>{{ __('Mother') }}</option>
                                    <option value="brother" {{ old('emergencyContactRelationship', $profileData['emergencyContact']['emergencyContactRelationship'] ?? '') == 'brother' ? 'selected' : '' }}>{{ __('Brother') }}</option>
                                    <option value="sister" {{ old('emergencyContactRelationship', $profileData['emergencyContact']['emergencyContactRelationship'] ?? '') == 'sister' ? 'selected' : '' }}>{{ __('Sister') }}</option>
                                    <option value="other" {{ old('emergencyContactRelationship', $profileData['emergencyContact']['emergencyContactRelationship'] ?? '') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Name -->
                              <div class="col-md-8 mb-2">
                                 <label for="emergencyContactName" class="form-label">{{ __('Name') }}</label>
                                 <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" 
                                    value="{{ old('emergencyContactName', $profileData['emergencyContact']['emergencyContactName'] ?? '') }}" >
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- Phone Number -->
                           <div class="mb-2">
                              <label for="emergencyContactPhone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="emergencyContactPhone" name="emergencyContactPhone" 
                                 value="{{ old('emergencyContactPhone', $profileData['emergencyContact']['emergencyContactPhone'] ?? '') }}" >
                              <div class="error-message"></div>
                           </div>
                        </div>
                        <div class="form-step tab-pane fade" id="step7">
                           <h4 class="mb-4">{{ __('Terms and Conditions') }}</h4>
                           <div class="mb-3">
                              <p>
                                 {{ __('By proceeding, you agree to the following Terms and Conditions') }}:
                              </p>
                              <div class="form-check">
                                 <input class="form-check-input border border-primary" type="checkbox" value="accepted" id="termsCheckbox" name="termsCheckbox" >
                                 <div class="error-message"></div>
                                 <label class="form-check-label" for="termsCheckbox">
                                 {{ __('I agree to the') }} 
                                 <a href="#" target="_blank">{{ __('Terms and Conditions') }}</a> 
                                 {{ __('and') }} 
                                 <a href="#" target="_blank">{{ __('Privacy Policy') }}</a>.
                                 </label>
                              </div>
                           </div>
                        </div>
                     </div>
                  </form>
                  <!-- Navigation Buttons -->
                  @php
                  $isRtl = app()->getLocale() === 'ar' ? true : false; // Adjust based on your RTL language
                  @endphp
                  <div class="d-flex justify-content-end mt-4 gap-2" id="navBtnsContainer">
                     <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                     <i class="fa {{ $isRtl ? 'fa-arrow-right' : 'fa-arrow-left' }} me-2"></i> {{ __('Previous') }}
                     </button>
                     <button type="button" class="btn btn-primary" id="nextBtn">
                     {{ __('Next') }} 
                     <i class="fa {{ $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' }} ms-2"></i>
                     </button>
                     <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                     <i class="fa fa-check me-2"></i> {{ __('Submit') }}
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/pages/complete-profile.js') }}"></script>
@endsection