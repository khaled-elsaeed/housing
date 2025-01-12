$(document).ready(function() {
    let currentStep = 1;
    const totalSteps = 3;
    const validatedSteps = new Set();

    // Validation rules remain the same
    const validationRules = {
        // firstName: {
        //     required: true,
        //     minLength: 2,
        //     pattern: /^[A-Za-z\s]+$/,
        //     message: 'Please enter a valid first name (minimum 2 characters, letters only)'
        // },
        // lastName: {
        //     required: true,
        //     minLength: 2,
        //     pattern: /^[A-Za-z\s]+$/,
        //     message: 'Please enter a valid last name (minimum 2 characters, letters only)'
        // },
        // birthDate: {
        //     required: true,
        //     message: 'Please select your date of birth'
        // },
        // email: {
        //     required: true,
        //     pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        //     message: 'Please enter a valid email address'
        // },
        // phone: {
        //     required: true,
        //     pattern: /^\+?[\d\s-]{10,}$/,
        //     message: 'Please enter a valid phone number (minimum 10 digits)'
        // },
        // address: {
        //     required: true,
        //     minLength: 10,
        //     message: 'Please enter your complete address (minimum 10 characters)'
        // },
        // username: {
        //     required: true,
        //     minLength: 4,
        //     pattern: /^[A-Za-z0-9_]+$/,
        //     message: 'Username must be at least 4 characters (letters, numbers, and underscore only)'
        // },
        // password: {
        //     required: true,
        //     minLength: 8,
        //     pattern: /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/,
        //     message: 'Password must be at least 8 characters with at least one letter, one number, and one special character'
        // },
        // confirmPassword: {
        //     required: true,
        //     match: 'password',
        //     message: 'Passwords do not match'
        // }
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

    // Enhanced navigation update
    function updateNavigation() {
        $('#prevBtn').toggle(currentStep > 1);
        $('#nextBtn').toggle(currentStep < totalSteps);
        $('#submitBtn').toggle(currentStep === totalSteps);

        // Update tab states
        $('.nav-tabs .nav-link').each(function() {
            const stepNum = $(this).data('step');
            if (stepNum < currentStep) {
                $(this).addClass('completed').removeClass('disabled');
            } else if (stepNum === currentStep) {
                $(this).addClass('active').removeClass('disabled');
            } else {
                $(this).removeClass('active completed');
                if (!validatedSteps.has(stepNum - 1)) {
                    $(this).addClass('disabled');
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
                $(this).closest('.mb-3').addClass('shake');

                // Remove the shake class after animation ends to allow re-triggering
                setTimeout(() => {
                    $(this).closest('.mb-3').removeClass('shake');
                }, 500); // Matches the animation duration
            });

        }
    });

    // Enhanced previous button handler
    $('#prevBtn').click(function() {
        if (currentStep > 1) {
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

        // Only allow clicking if the tab is not disabled
        if (!$(this).hasClass('disabled')) {
            if (clickedStep < currentStep || (clickedStep === currentStep + 1 && validatedSteps.has(currentStep))) {
                currentStep = clickedStep;
                $('.nav-tabs .nav-link').removeClass('active');
                $('.form-step').removeClass('active show');
                $(this).addClass('active').tab('show');
                $(`#step${currentStep}`).addClass('active show');
                updateNavigation();
            }
        } else {
            // Add shake effect to indicate tab is locked
            $(this).effect('shake', {
                times: 2,
                distance: 5
            }, 500);
        }
    });

    // Enhanced form submission handler
    $('#multiStepForm').on('submit', function(e) {
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
            // Show error message for invalid form
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

});