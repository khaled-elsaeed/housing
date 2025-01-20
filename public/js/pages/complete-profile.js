$(document).ready(function() {
    let currentStep = 1;
    let totalSteps = $('.form-step').length;
    const validatedSteps = new Set();

    // Validation rules remain the same
    const validationRules = {
        firstName: {
            required: true,
            minLength: 2,
            pattern: /^[A-Za-z\s]+$/,
            message: 'Please enter a valid first name (minimum 2 characters, letters only)'
        },
        lastName: {
            required: true,
            minLength: 2,
            pattern: /^[A-Za-z\s]+$/,
            message: 'Please enter a valid last name (minimum 2 characters, letters only)'
        },
        birthDate: {
            required: true,
            message: 'Please select your date of birth'
        },
        email: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Please enter a valid email address'
        },
        phone: {
            required: true,
            pattern: /^\+?[\d\s-]{10,}$/,
            message: 'Please enter a valid phone number (minimum 10 digits)'
        },
        address: {
            required: true,
            minLength: 10,
            message: 'Please enter your complete address (minimum 10 characters)'
        }
    };

    // Enhanced validation feedback
    function showError(element, message) {
        const errorElement = $(element).siblings('.error-message');
        errorElement.text(message).addClass('show');
        $(element).addClass('is-invalid');
    }

    function clearError(element) {
        const errorElement = $(element).siblings('.error-message');
        errorElement.text('').removeClass('show');
        $(element).removeClass('is-invalid');
    }

    // Enhanced field validation
    function validateField(field) {
        const value = field.value;
        const name = field.name;
        const rules = validationRules[name];

        if (!rules) return true;

        clearError(field);

        if (rules.required && !value) {
            showError(field, rules.message);
            return false;
        }

        if (rules.minLength && value.length < rules.minLength) {
            showError(field, rules.message);
            return false;
        }

        if (rules.pattern && !rules.pattern.test(value)) {
            showError(field, rules.message);
            return false;
        }

        if (rules.match) {
            const matchValue = document.getElementById(rules.match).value;
            if (value !== matchValue) {
                showError(field, rules.message);
                return false;
            }
        }

        return true;
    }

    // Enhanced step validation
    function validateStep(step) {
        const fields = $(`#step${step} input, #step${step} textarea`).get();
        const isValid = fields.every(field => validateField(field));

        if (isValid) {
            validatedSteps.add(step);
            $(`#step${step}-tab`)
                .removeClass('disabled')
                .removeClass('active')
                .addClass('completed')
                .find('.step-status')
                .html('<i class="fa fa-check"></i>');

            // Enable next tab if it exists
            if (step < totalSteps) {
                $(`#step${step + 1}-tab`).removeClass('disabled');
            }
        } else {
            validatedSteps.delete(step);
            $(`#step${step}-tab`)
                .removeClass('completed')
                .find('.step-status')
                .text(step);
        }

        return isValid;
    }

    function updateNavigation() {
       
        if(currentStep > 1){
            $('#navBtnsContainer').removeClass('justify-content-end');
            $('#navBtnsContainer').addClass('justify-content-between');
        }else{
            $('#navBtnsContainer').addClass('justify-content-end');
            $('#navBtnsContainer').removeClass('justify-content-between');
        }

        $('#prevBtn').toggle(currentStep > 1);
        $('#nextBtn').toggle(currentStep < totalSteps);
        $('#submitBtn').toggle(currentStep === totalSteps);
    
        // Update tab states 
        $('.nav-tabs .nav-link').each(function() {
            const stepNum = parseInt($(this).data('step'));

            $(this).removeClass('active');  
            

            if (stepNum < currentStep) {
                // Keep the completed state if step was previously validated
                if (validatedSteps.has(stepNum)) {
                    $(this).addClass('completed');
                }
            } else if (stepNum === currentStep) {
                $(this).addClass('active');
                // Keep completed state if current step was validated
                if (validatedSteps.has(stepNum)) {
                    $(this).addClass('completed');
                }
            } else {
                // For future steps, keep completed state if validated
                if (validatedSteps.has(stepNum)) {
                    $(this).addClass('completed');
                }
                // Only disable if previous step isn't validated
                if (!validatedSteps.has(stepNum - 1)) {
                    $(this).addClass('disabled');
                } else {
                    $(this).removeClass('disabled');
                }
            }
        });
    }

    // Enhanced real-time validation
    $('input, textarea').on('input change', function() {
        validateField(this);

    });

    // Enhanced next button handler
    $('#nextBtn').click(function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                handleEmergencyContactStep('forward');
                currentStep++;
                $('.nav-tabs .nav-link').removeClass('active');
                $('.form-step').removeClass('active show');
                $(`#step${currentStep}-tab`)
                    .addClass('active')
                    .removeClass('disabled')
                    .tab('show');
                $(`#step${currentStep}`).addClass('active show');
                updateNavigation();
            }
        } else {
            // Shake effect on invalid fields
            $('.is-invalid').each(function() {
                $(this).closest('.mb-2').addClass('shake');

                // Remove the shake class after animation ends to allow re-triggering
                setTimeout(() => {
                    $(this).closest('.mb-2').removeClass('shake');
                }, 500); // Matches the animation duration
            });

        }
    });

    function handleEmergencyContactStep(navDirection) {
        const isParentAbroad = $('#isParentAbroad').val();

        if (currentStep === 5 && navDirection === 'forward') {
            if (isParentAbroad !== 'yes') {
                currentStep++;
            }
        }

        if (currentStep === 8 && navDirection === 'backward') { 
            if (isParentAbroad !== 'yes') {
                currentStep--;
            }
        }
    }

    // Enhanced previous button handler
    $('#prevBtn').click(function() {
        if (currentStep > 1) {
            handleEmergencyContactStep('backward');
            currentStep--;
            $('.nav-tabs .nav-link').removeClass('active');
            $('.form-step').removeClass('active show');
            $(`#step${currentStep}-tab`).addClass('active').tab('show');
            $(`#step${currentStep}`).addClass('active show');
            updateNavigation();
        }
    });

    // Enhanced tab click handler with validation
    $('.nav-tabs .nav-link').click(function(e) {
        e.preventDefault();
        const clickedStep = parseInt($(this).data('step'));

        // Allow clicking if:
        // 1. Tab is not disabled
        // 2. Going to a previous step
        // 3. Going to next step only if current step is validated

        if (!$(this).hasClass('disabled')) {
            if (clickedStep < currentStep || validateStep(currentStep)) {
                currentStep = clickedStep;
                $('.nav-tabs .nav-link').removeClass('active');
                $('.form-step').removeClass('active show');
                $(this).addClass('active');
                $(`#step${currentStep}`).addClass('active show');
                handleEmergencyContactStep('forward');
                updateNavigation();
                console.log('Current step:', currentStep);
                console.log('emergencyContack: forward');

            }
            if(clickedStep > currentStep || validateStep(currentStep)){
                currentStep = clickedStep;
                $('.nav-tabs .nav-link').removeClass('active');
                $('.form-step').removeClass('active show');
                $(this).addClass('active');
                $(`#step${currentStep}`).addClass('active show');
                handleEmergencyContactStep('backward');
                updateNavigation();
                console.log('Current step:', currentStep);
                console.log('emergencyContack: backward');
            }
        }
    });

    // Enhanced form submission handler
    $('#submitBtn').on('click', function(e) {
        console.log('form is submit');
        e.preventDefault();

        if (formValidated()) {
            // Show loading state
            $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Submitting...');

            const formData = {};
            $(this).serializeArray().forEach(item => {
                formData[item.name] = item.value;
            });

            // Simulate API call
            setTimeout(() => {
                console.log('Form submitted:', formData);

                // Show success message
                swal({
                    type: 'success',
                    title: 'Success!',
                    text: 'Your registration has been completed successfully.',
                    confirmButtonColor: '#198754'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect or reset form as needed
                        window.location.reload();
                    }
                });
            }, 1500);
        } else {
            
            swal({
                type: 'error',
                title: 'Oops...',
                text: 'Please complete all required fields correctly before submitting.',
                confirmButtonColor: '#dc3545'
            });
        }
    });

    // Enhanced complete form validation
    function formValidated() {
        let isValid = true;
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                isValid = false;
                if (currentStep !== i) {
                    currentStep = i;
                    $('.nav-tabs .nav-link').removeClass('active');
                    $('.form-step').removeClass('active show');
                    $(`#step${currentStep}-tab`).addClass('active').tab('show');
                    $(`#step${currentStep}`).addClass('active show');
                    updateNavigation();
                }
                break;
            }
        }
        return isValid;
    }

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Initialize navigation
    updateNavigation();

    function toggleAbroadFields() {
        const isParentAbroad = document.getElementById('isParentAbroad').value;
        const abroadCountryDiv = document.getElementById('abroadCountryDiv');
        const livingWithParentDiv = document.getElementById('livingWithParentDiv');
        const parentGovernorateCityDiv = document.getElementById('parentGovernorateCityDiv');
        const emergencyContactStep = document.getElementById('step6');
        const emergencyContactStepTab = document.getElementById('step6-tab');


        
        if (isParentAbroad === 'yes') {
            abroadCountryDiv.classList.remove('d-none');
            emergencyContactStep.classList.remove('d-none')
            emergencyContactStepTab.classList.remove('d-none')
            livingWithParentDiv.classList.add('d-none');
            parentGovernorateCityDiv.classList.add('d-none');

        } else if (isParentAbroad === 'no') {
            abroadCountryDiv.classList.add('d-none');
            livingWithParentDiv.classList.remove('d-none');
            parentGovernorateCityDiv.classList.add('d-none');
            emergencyContactStep.classList.add('d-none');
            emergencyContactStepTab.classList.add('d-none');
        } else {
            abroadCountryDiv.classList.add('d-none');
            livingWithParentDiv.classList.add('d-none');
            parentGovernorateCityDiv.classList.add('d-none');
            emergencyContactStep.classList.add('d-none');
            emergencyContactStepTab.classList.add('d-none');
        }
    }
    
   

    const isParentAbroad = document.getElementById('isParentAbroad');
    
    isParentAbroad.addEventListener('change', toggleAbroadFields);
});