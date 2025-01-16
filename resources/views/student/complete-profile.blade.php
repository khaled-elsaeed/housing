@extends('layouts.student-without-leftbar')
@section('title', __('Complete Profile'))
@section('links')
<link rel="stylesheet" href="{{ asset('css/complete-profile.css') }}">
@endsection
@section('content')
<div class="container py-2 py-sm-2 py-md-3">
   <div class="row justify-content-center mt-5">
      <div class="col-lg-10 col-md-10 col-sm-6">
         <div class="form-card p-4">
            <div class="row g-4">
               <!-- Vertical Nav Tabs -->
               <div class="col-3 col-md-5">
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
               <div class="col-9 col-md-6">
                  <form id="multiStepForm" novalidate>
                     <div class="tab-content">
                        <!-- Step 1 - personal info -->
                        <div class="form-step tab-pane fade show active" id="step1">
                           <h4 class="mb-4">{{ __('Personal Information') }}</h4>
                           <!-- Full Name Arabic -->
                           <div class="mb-2">
                              <label for="firstName" class="form-label">{{ __('Full Name Arabic') }}</label>
                              <input type="text" class="form-control" id="firstName" name="firstName" value="{{ old('firstName', optional($user)->first_name) }}" required>
                              <div class="error-message"></div>
                           </div>
                           <!-- Full Name English -->
                           <div class="mb-2">
                              <label for="lastName" class="form-label">{{ __('Full Name English') }}</label>
                              <input type="text" class="form-control" id="lastName" name="lastName" value="{{ old('lastName', optional($user)->last_name) }}" required>
                              <div class="error-message"></div>
                           </div>
                           <!-- National ID -->
                           <div class="mb-2">
                              <label for="nationalId" class="form-label">{{ __('National ID') }}</label>
                              <input type="text" class="form-control" id="nationalId" name="nationalId" value="{{ old('nationalId', optional($user)->national_id) }}" required>
                              <div class="error-message"></div>
                           </div>
                           <!-- National ID and Gender in the same row -->
                           <div class="row">
                              <!-- Birth Date -->
                              <div class="col-md-6 mb-2">
                                 <label for="birthDate" class="form-label">{{ __('Birth Date') }}</label>
                                 <input type="date" class="form-control" id="birthDate" name="birthDate" value="{{ old('birthDate', optional($user)->birth_date) }}" required>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Gender -->
                              <div class="col-md-6 mb-2">
                                 <label for="gender" class="form-label">{{ __('Gender') }}</label>
                                 <select class="form-control" id="gender" name="gender" required>
                                    <option value="">{{ __('Select Gender') }}</option>
                                    <option value="male" {{ old('gender', optional($user)->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="female" {{ old('gender', optional($user)->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
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
                                    @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">
                                       {{ $governorate->name_en }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- City (Dropdown) -->
                              <div class="col-md-6 mb-2">
                                 <label for="city" class="form-label">{{ __('City') }}</label>
                                 <select class="form-control" id="city" name="city" required disabled>
                                    <option value="">{{ __('Select Governorate First') }}</option>
                                 </select>
                                 <!-- Hidden Datalist -->
                                 <datalist class="d-none" id="city-list">
                                    @foreach ($cities as $city)
                                    <option data-governorate-id="{{ $city->governorate_id }}" data-city-id="{{ $city->id }}" value="{{ $city->name_en }}"></option>
                                    @endforeach
                                 </datalist>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- Street -->
                           <div class="mb-2">
                              <label for="street" class="form-label">{{ __('Street') }}</label>
                              <input type="text" class="form-control" id="street" name="street">
                              <div class="error-message"></div>
                           </div>
                           <!-- Phone Number -->
                           <div class="mb-2">
                              <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="phone" name="phone">
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
                                 <select class="form-control" id="faculty" name="faculty" required>
                                    <option value="">{{ __('Select Faculty') }}</option>
                                    @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" 
                                    @if(optional($user)->faculty_id == $faculty->id) selected @endif>
                                    {{ $faculty->name_en }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- Program (Dropdown) -->
                              <div class="col-md-6  mb-2">
                                 <label for="program" class="form-label">{{ __('Program') }}</label>
                                 <select class="form-control" id="program" name="program" required disabled>
                                    <option value="">{{ __('Select Program') }}</option>
                                    @if (optional($user)->faculty_id)
                                    @foreach (optional($user)->faculty->programs as $program) <!-- Eager load the programs -->
                                    <option value="{{ $program->id }}" 
                                    @if(optional($user)->program_id == $program->id) selected @endif>
                                    {{ $program->name_en }}
                                    </option>
                                    @endforeach
                                    @endif
                                 </select>
                                 <!-- Hidden Datalist -->
                                 <datalist class="d-none" id="faculty-programs">
                                    @foreach ($programs as $program)
                                    <option data-faculty-id="{{ $program->faculty->id }}" data-program-id="{{ $program->id }}" value="{{ $program->name_en }}"></option>
                                    @endforeach
                                 </datalist>
                                 <div class="error-message"></div>
                              </div>
                           </div>
                           <!-- University ID -->
                           <div class="mb-2">
                              <label for="universityId" class="form-label">{{ __('University ID') }}</label>
                              <input type="text" class="form-control" id="universityId" name="universityId" value="{{ old('university_id', optional($user)->university_id) }}">
                              <div class="error-message"></div>
                           </div>
                           <!-- University Email -->
                           <div class="mb-2">
                              <label for="universityEmail" class="form-label">{{ __('University Email') }}</label>
                              <input type="email" class="form-control" id="universityEmail" name="universityEmail" value="{{ old('university_email', optional($user)->university_email) }}">
                              <div class="error-message"></div>
                           </div>
                           <!-- GPA/Score -->
                           <div class="mb-2">
                              <label for="gpa" class="form-label">{{ __('GPA/Score') }}</label>
                              <input type="number" class="form-control" id="gpa" name="gpa" step="0.01" min="0" max="4.0" value="{{ old('gpa', optional($user)->gpa) }}">
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
                                 <select name="parentRelationship" id="parentRelationship" class="form-control" required>
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="grandfather">{{ __('Grandfather') }}</option>
                                       <option value="grandmother">{{ __('Grandmother') }}</option>
                                       <option value="uncle">{{ __('Uncle') }}</option>
                                       <option value="aunt">{{ __('Aunt') }}</option>
                                       <option value="cousin">{{ __('Cousin') }}</option>
                                 </select>
                              </div>
                              <!-- Name -->
                              <div class="col-md-8 mb-2">
                                 <label for="parentName" class="form-label">{{ __('Name') }}</label>
                                 <input type="text" class="form-control" id="parentName" name="parentName" 
                                    value="{{ old('parentName', optional($user)->parent_name) }}" required>
                              </div>
                           </div>
                           <!-- Phone Number -->
                           <div class="mb-2">
                              <label for="parentPhone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="parentPhone" name="parentPhone" 
                                 value="{{ old('parentPhone', optional($user)->parent_phone) }}" required>
                           </div>
                           <!-- Email -->
                           <div class="mb-2">
                              <label for="parentEmail" class="form-label">{{ __('Email') }}</label>
                              <input type="email" class="form-control" id="parentEmail" name="parentEmail" 
                                 value="{{ old('parentEmail', optional($user)->parent_email) }}">
                           </div>
                           <div class="row">
                              <!-- Is Abroad -->
                              <div class="col-md-6 mb-2">
                                 <label for="isParentAbroad" class="form-label">{{ __('Is the Parent Abroad?') }}</label>
                                 <select name="isParentAbroad" id="isParentAbroad" class="form-control" required>
                                    <option value="">{{ __('Select Option') }}</option>
                                    <option value="yes">{{ __('Yes') }}</option>
                                    <option value="no">{{ __('No') }}</option>
                                 </select>
                              </div>
                              <!-- Country if Abroad -->
                              <div class="col-md-6 mb-2 d-none" id="abroadCountryDiv">
                                 <label for="abroadCountry" class="form-label">{{ __('Country') }}</label>
                                 <select class="form-control" id="abroadCountry" name="abroadCountry">
                                    <option value="">{{ __('Select Country') }}</option>
                                    @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" 
                                    {{ old('abroadCountry', optional($user)->abroad_country) == $country->id ? 'selected' : '' }}>
                                    {{ __($country->name_en) }}
                                    </option>
                                    @endforeach
                                 </select>
                              </div>
                              <!-- Living with Parent if Not Abroad -->
                              <div class="col-md-6 d-none" id="livingWithParentDiv">
                                 <label for="livingWithParent" class="form-label">{{ __('Do You Live With Them?') }}</label>
                                 <select name="livingWithParent" id="livingWithParent" class="form-control">
                                    <option value="">{{ __('Select Option') }}</option>
                                    <option value="yes">{{ __('Yes') }}</option>
                                    <option value="no">{{ __('No') }}</option>
                                 </select>
                              </div>
                           </div>
                           <!-- Governorate and City (only shown if not living with them) -->
                           <div class="row d-none" id="parentGovernorateCityDiv">
                              <div class="col-md-6 mb-2">
                                 <label for="parentGovernorate" class="form-label">{{ __('Governorate') }}</label>
                                 <select class="form-control" id="parentGovernorate" name="parentGovernorate" required>
                                    <option value="">{{ __('Select Governorate') }}</option>
                                    @foreach ($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">
                                       {{ $governorate->name_en }}
                                    </option>
                                    @endforeach
                                 </select>
                                 <div class="error-message"></div>
                              </div>
                              <!-- City (Dropdown) -->
                              <div class="col-md-6 mb-2">
                                 <label for="parentCity" class="form-label">{{ __('City') }}</label>
                                 <select class="form-control" id="parentCity" name="parentCity" required disabled>
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
                              <select name="hasSiblingInDorm" id="hasSiblingInDorm" class="form-control" required onchange="toggleSiblingFields()">
                                 <option value="">{{ __('Select Option') }}</option>
                                 <option value="yes">{{ __('Yes') }}</option>
                                 <option value="no">{{ __('No') }}</option>
                              </select>
                           </div>
                           <!-- Sibling Information - This will be shown if the user has a sibling in the dorm -->
                           <div id="siblingInfoSection" class="d-none">
                              <!-- Relationship -->
                              <div class="row">
                                 <div class="col-md-4 mb-2">
                                    <label for="siblingRelationship" class="form-label">{{ __('Relationship') }}</label>
                                    <select name="siblingRelationship" id="siblingRelationship" class="form-control" required>
                                       <option value="">{{ __('Select Relationship') }}</option>
                                       <option value="brother">{{ __('Brother') }}</option>
                                       <option value="sister">{{ __('Sister') }}</option>
                                       <option value="other">{{ __('Other') }}</option>
                                    </select>
                                 </div>
                                 <!-- Name -->
                                 <div class="col-md-8 mb-2">
                                    <label for="siblingName" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" id="siblingName" name="siblingName" value="{{ old('siblingName', optional($user)->sibling_name) }}" required>
                                 </div>
                              </div>
                              <!-- National ID -->
                              <div class="mb-2">
                                 <label for="siblingNationalId" class="form-label">{{ __('National ID') }}</label>
                                 <input type="text" class="form-control" id="siblingNationalId" name="siblingNationalId" value="{{ old('siblingNationalId', optional($user)->sibling_national_id) }}">
                              </div>
                              <!-- Faculty -->
                              <div class="mb-2">
                                 <label for="siblingFaculty" class="form-label">{{ __('Faculty') }}</label>
                                 <select class="form-control" id="siblingFaculty" name="siblingFaculty" required>
                                    <option value="">{{ __('Select Faculty') }}</option>
                                    @foreach ($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" 
                                    @if(old('siblingFaculty', optional($user)->sibling_faculty_id) == $faculty->id) selected @endif>
                                    {{ $faculty->name_en }}
                                    </option>
                                    @endforeach
                                 </select>
                              </div>
                           </div>
                        </div>
                        <script>
                           // Function to toggle sibling information based on the selection
                           function toggleSiblingFields() {
                              const hasSiblingInDorm = document.getElementById('hasSiblingInDorm').value;
                              const siblingInfoSection = document.getElementById('siblingInfoSection');
                              
                              // Show sibling info if the user selects 'Yes'
                              if (hasSiblingInDorm === 'yes') {
                                 siblingInfoSection.classList.remove('d-none');
                              } else {
                                 siblingInfoSection.classList.add('d-none');
                              }
                           }
                        </script>
                        <!-- Step 6: Emergency Contact Information -->
                        <div class="form-step tab-pane fade d-none" id="step6">
                           <h4 class="mb-4">{{ __('Emergency Contact Information') }}</h4>
                           <!-- Relationship -->
                           <div class="row">
                              <div class="col-md-4 mb-2">
                                 <label for="emergencyContactRelationship" class="form-label">{{ __('Relationship') }}</label>
                                 <select name="emergencyContactRelationship" id="emergencyContactRelationship" class="form-control" required>
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="father">{{ __('Father') }}</option>
                                    <option value="mother">{{ __('Mother') }}</option>
                                    <option value="brother">{{ __('Brother') }}</option>
                                    <option value="sister">{{ __('Sister') }}</option>
                                    <option value="other">{{ __('Other') }}</option>
                                 </select>
                              </div>
                              <!-- Name -->
                              <div class="col-md-8 mb-2">
                                 <label for="emergencyContactName" class="form-label">{{ __('Name') }}</label>
                                 <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" 
                                    value="{{ old('emergencyContactName', optional($user)->emergency_contact_name) }}" required>
                              </div>
                           </div>
                           <!-- Phone Number -->
                           <div class="mb-2">
                              <label for="emergencyContactPhone" class="form-label">{{ __('Phone Number') }}</label>
                              <input type="tel" class="form-control" id="emergencyContactPhone" name="emergencyContactPhone" 
                                 value="{{ old('emergencyContactPhone', optional($user)->emergency_contact_phone) }}" required>
                           </div>
                        </div>
                        <div class="form-step tab-pane fade" id="step7">
                           <h4 class="mb-4">{{ __('Terms and Conditions') }}</h4>
                           <div class="mb-3">
                              <p>
                                 {{ __('By proceeding, you agree to the following Terms and Conditions') }}:
                              </p>
                              <div class="form-check">
                                 <input class="form-check-input" type="checkbox" value="" id="termsCheckbox" name="termsCheckbox" required>
                                 <label class="form-check-label" for="termsCheckbox">
                                 {{ __('I agree to the') }} 
                                 <a href="#" target="_blank">{{ __('Terms and Conditions') }}</a> 
                                 {{ __('and') }} 
                                 <a href="#" target="_blank">{{ __('Privacy Policy') }}</a>.
                                 </label>
                              </div>
                           </div>
                        </div>
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4 gap-2">
                           <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                           <i class="fa fa-arrow-left me-2"></i>{{ __('Previous') }}
                           </button>
                           <button type="button" class="btn btn-primary" id="nextBtn">
                           {{ __('Next') }}<i class="fa fa-arrow-right ms-2"></i>
                           </button>
                           <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                           <i class="fa fa-check me-2"></i>{{ __('Submit') }}
                           </button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('js/pages/complete-profile.js') }}"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {
       const governorateSelect = document.getElementById('governorate');
       const parentGovernorateSelect = document.getElementById('parentGovernorate');
   
       const citySelect = document.getElementById('city');
       const parentCitySelect = document.getElementById('parentCity');
       const cityList = document.getElementById('city-list');
   
   
       governorateSelect.addEventListener('change', function() {
           const selectedGovernorateId = this.value;
   
           // Enable the city dropdown and clear previous selections
           citySelect.disabled = false;
           citySelect.innerHTML = '<option value="">{{ __('Select City') }}</option>';
   
           // Filter cities from the datalist based on the selected governorate
           const cityOptions = cityList.querySelectorAll('option');
           const filteredCities = Array.from(cityOptions).filter(option => option.dataset.governorateId == selectedGovernorateId);
   
           // Populate the city dropdown with filtered cities
           filteredCities.forEach(option => {
               const newOption = document.createElement('option');
               newOption.value = option.dataset.cityId;
               newOption.text = option.value;
   
               console.log(option);
               citySelect.appendChild(newOption);
           });
       });
   
       parentGovernorateSelect.addEventListener('change', function() {
           const selectedGovernorateId = this.value;
   
           // Enable the city dropdown and clear previous selections
           parentCitySelect.disabled = false;
           parentCitySelect.innerHTML = '<option value="">{{ __('Select City') }}</option>';
   
           // Filter cities from the datalist based on the selected governorate
           const cityOptions = cityList.querySelectorAll('option');
           const filteredCities = Array.from(cityOptions).filter(option => option.dataset.governorateId == selectedGovernorateId);
   
           // Populate the city dropdown with filtered cities
           filteredCities.forEach(option => {
               const newOption = document.createElement('option');
               newOption.value = option.dataset.cityId;
               newOption.text = option.value;
   
               console.log(option);
               parentCitySelect.appendChild(newOption);
           });
       });
   
       // When the Faculty is selected, update the Program options
       document.getElementById('faculty').addEventListener('change', function() {
           var facultyId = this.value;
           var programSelect = document.getElementById('program');
           var programsList = document.getElementById('faculty-programs').querySelectorAll('option');
           
           // Clear existing program options
           programSelect.innerHTML = '<option value="">{{ __('Select Program') }}</option>';
   
           if (facultyId) {
               programSelect.removeAttribute('disabled'); // Enable the program dropdown
               // Add the filtered programs based on selected faculty
               programsList.forEach(function(option) {
                   if (option.getAttribute('data-faculty-id') == facultyId) {
                       var programOption = document.createElement('option');
                       programOption.value = option.value;
                       programOption.text = option.value;
                       programSelect.appendChild(programOption);
                   }
               });
           } else {
               programSelect.setAttribute('disabled', 'true');
           }
       });
       document.getElementById('livingWithParent').addEventListener('change', function() {
         const livingWithParent = document.getElementById('livingWithParent').value;
        const parentGovernorateCityDiv = document.getElementById('parentGovernorateCityDiv');
        
        if (livingWithParent === 'no') {
            parentGovernorateCityDiv.classList.remove('d-none');
        } else {
            parentGovernorateCityDiv.classList.add('d-none');
        }
       });
       
   });
</script>
@endsection