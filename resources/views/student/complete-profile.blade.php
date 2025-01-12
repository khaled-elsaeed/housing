@extends('layouts.student-without-leftbar')

@section('title', __('Complete Profile'))

@section('links')
    <link rel="stylesheet" href="{{ asset('css/complete-profile.css') }}">
@endsection

@section('content')
<div class="container py-2 py-sm-2 py-md-3">
    <div class="row justify-content-center mt-5">
        <div class="col-lg-10 col-md-8 col-sm-6">
            <div class="form-card p-4">
                <div class="row g-4">
                    <!-- Vertical Nav Tabs -->
                    <div class="col-md-3">
                        <div class="nav flex-column nav-tabs nav-pills nav-justified gap-1" role="tablist">
                            <a class="nav-link active" id="step1-tab" data-bs-toggle="tab" href="#step1" role="tab" data-step="1">
                                <i class="fa fa-user"></i>
                                <span>{{ __('Personal Info') }}</span>
                            </a>
                            <a class="nav-link disabled" id="step2-tab" data-bs-toggle="tab" href="#step2" role="tab" data-step="2">
                                <i class="fa fa-address-card"></i>
                                <span>{{ __('Contact Details') }}</span>
                            </a>
                            <a class="nav-link disabled" id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab" data-step="3">
                                <i class="fa fa-lock"></i>
                                <span>{{ __('Additional Info') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="col-md-9">
                        <form id="multiStepForm" novalidate>
                            <div class="tab-content">
                                <!-- Step 1 -->
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

                                <!-- Step 2 -->
                                <div class="form-step tab-pane fade" id="step2">
                                    <h4 class="mb-4">{{ __('Contact Details') }}</h4>

                                    <div class="row">
                                        <!-- Governorate (Dropdown) -->
                                        <div class="col-md-3 mb-3">
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
                                        <div class="col-md-3 mb-3">
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

                                        <!-- Street -->
                                        <div class="col-md-3 mb-3">
                                            <label for="street" class="form-label">{{ __('Street') }}</label>
                                            <input type="text" class="form-control" id="street" name="street">
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Phone Number -->
                                        <div class="col-md-3 mb-3">
                                            <label for="phone" class="form-label">{{ __('Phone Number') }}</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                            <div class="error-message"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3 -->
                                <div class="form-step tab-pane fade" id="step3">
                                    <h4 class="mb-4">{{ __('Account Setup') }}</h4>

                                    <!-- Faculty (Dropdown) -->
                                    <div class="mb-3">
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
                                    <div class="mb-3">
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

                                    <div class="row">
    <!-- University ID -->
    <div class="col-md-6 mb-3">
        <label for="universityId" class="form-label">{{ __('University ID') }}</label>
        <input type="text" class="form-control" id="universityId" name="universityId" value="{{ old('university_id', optional($user)->university_id) }}">
        <div class="error-message"></div>
    </div>

    <!-- University Email -->
    <div class="col-md-6 mb-3">
        <label for="universityEmail" class="form-label">{{ __('University Email') }}</label>
        <input type="email" class="form-control" id="universityEmail" name="universityEmail" value="{{ old('university_email', optional($user)->university_email) }}">
        <div class="error-message"></div>
    </div>
</div>


                                   

                                   

                                    <!-- GPA/Score -->
                                    <div class="mb-3">
                                        <label for="gpa" class="form-label">{{ __('GPA/Score') }}</label>
                                        <input type="number" class="form-control" id="gpa" name="gpa" step="0.01" min="0" max="4.0" value="{{ old('gpa', optional($user)->gpa) }}">
                                        <div class="error-message"></div>
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
            const citySelect = document.getElementById('city');
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
                    programSelect.setAttribute('disabled', 'true'); // Disable the program dropdown if no faculty is selected
                }
            });
        });
    </script>
@endsection