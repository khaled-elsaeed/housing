$(document).ready(function() {
    // Global variables
    let currentStep = 1;
    let totalSteps = $('.form-step').length;
    const lang = document.documentElement.lang || 'en';

    const validatedSteps = new Set();
    // Translate function


    const translations = {
        phone: {
            required: {
                en: 'Please enter a valid phone number (minimum 11 digits).',
                ar: 'يرجى إدخال رقم هاتف صحيح (11 أرقام على الأقل).',
            },
            pattern: {
                en: 'Please enter a valid phone number (e.g., 01014545865).',
                ar: 'يرجى إدخال رقم هاتف صحيح (مثال: 01014545865).',
            },
        },
        street: {
            required: {
                en: 'Please enter your complete address (minimum 15 characters).',
                ar: 'يرجى إدخال عنوانك الكامل (15 أحرف على الأقل).',
            },
            minLength: {
                en: 'Please enter your complete address (minimum 15 characters).',
                ar: 'يرجى إدخال عنوانك الكامل (15 أحرف على الأقل).',
            },
        },
        governorate: {
            required: {
                en: 'Please select a governorate.',
                ar: 'يرجى اختيار المحافظة.',
            },
        },
        city: {
            required: {
                en: 'Please select a city.',
                ar: 'يرجى اختيار المدينة.',
            },
        },
        faculty: {
            required: {
                en: 'Please select a faculty.',
                ar: 'يرجى اختيار الكلية.',
            },
        },
        program: {
            required: {
                en: 'Please select a program.',
                ar: 'يرجى اختيار البرنامج.',
            },
        },
        parentRelationship: {
            required: {
                en: 'Please select your relationship with the parent.',
                ar: 'يرجى اختيار صلة القرابة.',
            },
        },
        parentName: {
            required: {
                en: 'Please enter the parent’s name.',
                ar: 'يرجى إدخال اسم ولي الأمر.',
            },
            minLength: {
                en: 'Please enter your complete parent name.',
                ar: 'يرجى إدخال اسم ولي الأمر كامل.',
            },
        },
        parentPhone: {
            required: {
                en: 'Please enter a valid phone number.',
                ar: 'يرجى إدخال رقم هاتف صحيح.',
            },
            pattern: {
                en: 'Please enter a valid phone number with the correct format.',
                ar: 'يرجى إدخال رقم هاتف صحيح بالتنسيق المناسب.',
            },
        },
        isParentAbroad: {
            required: {
                en: 'Please specify if the parent is abroad.',
                ar: 'يرجى تحديد ما إذا كان ولي الأمر بالخارج.',
            },
        },
        abroadCountry: {
            required: {
                en: 'Please choose the abroad country.',
                ar: 'يرجى اختيار البلد.',
            },
        },
        parentGovernorate: {
            required: {
                en: 'Please select a governorate.',
                ar: 'يرجى اختيار المحافظة.',
            },
        },
        parentCity: {
            required: {
                en: 'Please select a city.',
                ar: 'يرجى اختيار المدينة.',
            },
        },
        siblingGender: {
            required: {
                en: 'Please specify the gender of your sibling.',
                ar: 'يرجى تحديد جنس الأخ/الأخت.',
            },
        },
        siblingNationalId: {
            required: {
                en: 'Please enter the National ID of your sibling.',
                ar: 'يرجى إدخال الرقم القومي للأخ/الأخت.',
            },
        },
        siblingFaculty: {
            required: {
                en: 'Please select the faculty of your sibling.',
                ar: 'يرجى اختيار كلية الأخ/الأخت.',
            },
        },
        emergencyContactRelationship: {
            required: {
                en: 'Please specify your relationship with the emergency contact.',
                ar: 'يرجى تحديد صلة القرابة بجهة الاتصال الطارئة.',
            },
        },
        emergencyContactName: {
            required: {
                en: 'Please enter the name of the emergency contact.',
                ar: 'يرجى إدخال اسم جهة الاتصال الطارئة.',
            },
        },
        emergencyContactPhone: {
            required: {
                en: 'Please enter a valid phone number for the emergency contact.',
                ar: 'يرجى إدخال رقم هاتف صحيح لجهة الاتصال الطارئة.',
            },
        },
    };

    // Function to safely fetch translations with a fallback message
    function translate(field, rule, lang) {
        return translations[field] && translations[field][rule] && translations[field][rule][lang] ?
            translations[field][rule][lang] :
            `Validation error for ${field} (${rule})`; // Default fallback
    }


    const validationRules = {
        governorate: {
            required: true
        },
        city: {
            required: true
        },
        faculty: {
            required: true
        },
        program: {
            required: true
        },
        phone: {
            required: true,
            pattern: /^01[0-25]\d{8}$/, // Egyptian phone format
        },
        street: {
            required: true,
            minLength: 15,
        },
        parentRelationship: {
            required: true
        },
        parentName: {
            required: true,
            minLength: 15
        },
        parentPhone: {
            required: true,
            pattern: /^(?:\+\d{1,3}\s?\d{6,14}|01[0-25]\d{8})$/,
        },
        isParentAbroad: {
            required: true
        },
        abroadCountry: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 1;
            },
        },
        parentGovernorate: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 0 && Number($("#livingWithParent").val()) === 0;
            },
        },
        parentCity: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 0 && Number($("#livingWithParent").val()) === 0;
            },
        },
        siblingGender: {
            required: function() {
                return Number($("#hasSiblingInDorm").val()) === 1;
            }
        },
        siblingNationalId: {
            required: function() {
                return Number($("#hasSiblingInDorm").val()) === 1;
            }
        },
        siblingFaculty: {
            required: function() {
                return Number($("#hasSiblingInDorm").val()) === 1;
            }
        },
        emergencyContactRelationship: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 1;
            }
        },
        emergencyContactName: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 1;
            }
        },
        emergencyContactPhone: {
            required: function() {
                return Number($("#isParentAbroad").val()) === 1;
            }
        }
    };

    // Validation messages
    const validationMessages = {
        governorate: {
            required: translate('governorate', 'required', lang)
        },
        city: {
            required: translate('city', 'required', lang)
        },
        faculty: {
            required: translate('faculty', 'required', lang)
        },
        program: {
            required: translate('program', 'required', lang)
        },
        phone: {
            required: translate('phone', 'required', lang),
            pattern: translate('phone', 'pattern', lang),
        },
        street: {
            required: translate('street', 'required', lang),
            minLength: translate('street', 'minLength', lang),
        },
        parentRelationship: {
            required: translate('parentRelationship', 'required', lang)
        },
        parentName: {
            required: translate('parentName', 'required', lang),
            minLength: translate('parentName', 'minLength', lang),
        },
        parentPhone: {
            required: translate('parentPhone', 'required', lang),
            pattern: translate('parentPhone', 'pattern', lang),
        },
        isParentAbroad: {
            required: translate('isParentAbroad', 'required', lang)
        },
        abroadCountry: {
            required: translate('abroadCountry', 'required', lang)
        },
        parentGovernorate: {
            required: translate('parentGovernorate', 'required', lang)
        },
        parentCity: {
            required: translate('parentCity', 'required', lang)
        },
        siblingGender: {
            required: translate('siblingGender', 'required', lang)
        },
        siblingNationalId: {
            required: translate('siblingNationalId', 'required', lang)
        },
        siblingFaculty: {
            required: translate('siblingFaculty', 'required', lang)
        },
        emergencyContactRelationship: {
            required: translate('emergencyContactRelationship', 'required', lang)
        },
        emergencyContactName: {
            required: translate('emergencyContactName', 'required', lang)
        },
        emergencyContactPhone: {
            required: translate('emergencyContactPhone', 'required', lang)
        },
    };

    /**
     * Form Validation Functions
     */

    /**
     * Display error message for a form field
     * @param {HTMLElement} element - The form field element
     * @param {string} message - Error message to display
     */
    function showError(element, message) {
        const errorElement = $(element).siblings('.error-message');
        errorElement.text(message).addClass('show');
        $(element).addClass('is-invalid');
    }

    /**
     * Clear error message for a form field
     * @param {HTMLElement} element - The form field element
     */
    function clearError(element) {
        const errorElement = $(element).siblings('.error-message');
        errorElement.text('').removeClass('show');
        $(element).removeClass('is-invalid');
    }

    /**
     * Validate a single form field
     * @param {HTMLElement} field - The form field to validate
     * @returns {boolean} - True if valid, false otherwise
     */
    function validateField(field) {
        const value = field.value;
        const name = field.name;
        const rules = validationRules[name];
        const messages = validationMessages[name];

        if (!rules) return true;

        clearError(field);

        // Properly evaluate `required`
        const isRequired = typeof rules.required === 'function' ? rules.required() : rules.required;
        console.log("field ", field, " type ", typeof rules.required);
        // Check required
        if (isRequired && !value) {
            showError(field, messages.required);
            return false;
        }

        // Check minLength
        if (rules.minLength && value.length < rules.minLength) {
            showError(field, messages.minLength);
            return false;
        }

        // Check pattern
        if (rules.pattern && !rules.pattern.test(value)) {
            showError(field, messages.pattern);
            return false;
        }

        return true;
    }


    /**
     * Validate all fields in a step
     * @param {number} step - The step number to validate
     * @returns {boolean} - True if all fields are valid
     */
    function validateStep(step) {
        const fields = $(`#step${step} input, #step${step} textarea, #step${step} select`).get();
        const isValid = fields.every(field => validateField(field));

        if (isValid) {
            validatedSteps.add(step);
            updateStepStatus(step, true);
        } else {
            validatedSteps.delete(step);
            updateStepStatus(step, false);
        }

        return isValid;
    }

    /**
     * Update the visual status of a step
     * @param {number} step - The step number
     * @param {boolean} isValid - Whether the step is valid
     */
    function updateStepStatus(step, isValid) {
        const stepTab = $(`#step${step}-tab`);
        if (isValid) {
            stepTab
                .removeClass('disabled active')
                .addClass('completed')
                .find('.step-status')
                .html('<i class="fa fa-check"></i>');

            if (step < totalSteps) {
                $(`#step${step + 1}-tab`).removeClass('disabled');
            }
        } else {
            stepTab
                .removeClass('completed')
                .find('.step-status')
                .text(step);
        }
    }

    /**
     * Navigation Functions
     */

    /**
     * Handle emergency contact step navigation
     * @param {string} direction - Navigation direction ('forward' or 'backward')
     */
    function handleEmergencyContactStep(direction) {
        const isParentAbroad = $('#isParentAbroad').val();

        if (currentStep === 5 && direction === 'forward') {
            if (isParentAbroad !== '1') {
                currentStep++;
            }
        }

        if (currentStep === 7 && direction === 'backward') {
            if (isParentAbroad !== '1') {
                currentStep--;
            }
        }
    }

    /**
     * Update the form navigation state
     */
    function updateNavigation() {
        updateNavigationButtons();
        updateTabStates();
    }

    /**
     * Update navigation button states
     */
    function updateNavigationButtons() {
        if (currentStep > 1) {
            $('#navBtnsContainer')
                .removeClass('justify-content-end')
                .addClass('justify-content-between');
        } else {
            $('#navBtnsContainer')
                .addClass('justify-content-end')
                .removeClass('justify-content-between');
        }

        $('#prevBtn').toggle(currentStep > 1);
        $('#nextBtn').toggle(currentStep < totalSteps);
        $('#submitBtn').toggle(currentStep === totalSteps);
    }

    /**
     * Update tab states based on current step
     */
    function updateTabStates() {
        $('.nav-tabs .nav-link').each(function() {
            const stepNum = parseInt($(this).data('step'));
            $(this).removeClass('active');
            updateTabState($(this), stepNum);
        });
    }

    /**
     * Update individual tab state
     * @param {jQuery} tab - The tab element
     * @param {number} stepNum - Step number
     */
    function updateTabState(tab, stepNum) {
        if (stepNum < currentStep) {
            if (validatedSteps.has(stepNum)) {
                tab.addClass('completed');
            }
        } else if (stepNum === currentStep) {
            tab.addClass('active');
            if (validatedSteps.has(stepNum)) {
                tab.addClass('completed');
            }
        } else {
            if (validatedSteps.has(stepNum)) {
                tab.addClass('completed');
            }
            if (!validatedSteps.has(stepNum - 1)) {
                tab.addClass('disabled');
            } else {
                tab.removeClass('disabled');
            }
        }
    }

    /**
     * Event Handlers
     */

    /**
     * Handle next button click
     */
    function handleNextButton() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                handleEmergencyContactStep('forward');
                currentStep++;
                changeStep();
            }
        } else {
            showInvalidFieldsShake();
        }
    }

    /**
     * Handle previous button click
     */
    function handlePreviousButton() {
        if (currentStep > 1) {
            handleEmergencyContactStep('backward');
            currentStep--;
            changeStep();
        }
    }

    /**
     * Handle tab click
     * @param {Event} e - Click event
     */
    function handleTabClick(e) {
        e.preventDefault();
        const clickedStep = parseInt($(this).data('step'));

        if (!$(this).hasClass('disabled')) {
            if (clickedStep < currentStep || validateStep(currentStep)) {
                currentStep = clickedStep;
                changeStep();
                handleEmergencyContactStep('forward');
            }
        }
    }

    /**
     * Change to a new step
     */
    function changeStep() {
        $('.nav-tabs .nav-link').removeClass('active');
        $('.form-step').removeClass('active show');
        $(`#step${currentStep}-tab`).addClass('active').tab('show');
        $(`#step${currentStep}`).addClass('active show');
        updateNavigation();
    }

    /**
     * Add shake effect to invalid fields
     */
    function showInvalidFieldsShake() {
        $('.is-invalid').each(function() {
            $(this).closest('.mb-2').addClass('shake');
            setTimeout(() => {
                $(this).closest('.mb-2').removeClass('shake');
            }, 500);
        });
    }

    /**
     * Form Submission
     */

    /**
     * Handle form submission
     * @param {Event} e - Submit event
     */
    function handleFormSubmission(e) {
        e.preventDefault();

        if (validateAllSteps()) {
            submitForm();
        } else {
            showSubmissionError();
        }
    }

    /**
     * Validate all form steps
     * @returns {boolean} - True if all steps are valid
     */
    function validateAllSteps() {
        for (let i = 1; i <= totalSteps; i++) {
            if (!validateStep(i)) {
                currentStep = i;
                changeStep();
                return false;
            }
        }
        return true;
    }

    /**
 * Submit form data
 */
function submitForm() {
    const submitBtn = $('#submitBtn');
    if (lang === 'ar') {
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>جاري الحفظ...');
    } else {
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Submitting...');
    }

    const formData = {};
    $('#multiStepForm').serializeArray().forEach(item => {
        formData[item.name] = item.value;
    });

    $.ajax({
        method: 'POST',
        url: $('#multiStepForm').attr('action'),
        data: formData,
        success: handleSubmitSuccess,
        error: handleSubmitError,
        complete: () => {
            if (lang === 'ar') {
                submitBtn.prop('disabled', false).html('حفظ');
            } else {
                submitBtn.prop('disabled', false).html('Submit');
            }
        }
    });
}

/**
 * Handle successful form submission
 * @param {Object} response - Server response
 */
function handleSubmitSuccess(response) {
    swal({
        type: 'success',
        title: lang === 'ar' ? 'تم بنجاح!' : 'Success!',
        text: response.message,
        confirmButtonText: lang === 'ar' ? 'موافق' : 'OK',
        confirmButtonColor: '#198754',
        allowOutsideClick: false,     
        allowEscapeKey: false,   
    }).then((result) => {
        if (response.success && response.redirect) {
            window.location.href = response.redirect;
        }
    });
}

/**
 * Handle form submission error
 * @param {Object} error - Error object
 */
function handleSubmitError(error) {
    console.error('Error:', error);
    swal({     
        type: 'error',
        title: lang === 'ar' ? 'فشل الإرسال' : 'Submission Failed',
        text: error.responseJSON.message,
        confirmButtonText: lang === 'ar' ? 'موافق' : 'OK',
        confirmButtonColor: '#dc3545'
    });
}

/**
 * Show submission validation error
 */
function showSubmissionError() {
    swal({
        type: 'error',
        title: lang === 'ar' ? 'عذراً...' : 'Oops...',
        text: lang === 'ar' 
            ? 'يرجى إكمال جميع الحقول المطلوبة بشكل صحيح قبل الإرسال.' 
            : 'Please complete all required fields correctly before submitting.',
        confirmButtonText: lang === 'ar' ? 'موافق' : 'OK',
        confirmButtonColor: '#dc3545'
    });
}

    /**
     * National ID Processing
     */

    /**
     * Generate gender and birthdate from national ID
     * @param {string} nationalId - National ID number
     * @returns {Object} - Gender and birthdate information
     */
    function generateGenderAndBirthdate(nationalId) {
        if (typeof nationalId !== 'string' || nationalId.length !== 14 || !/^\d{14}$/.test(nationalId)) {
            return {
                error: "Please enter a valid 14-digit national ID."
            };
        }

        const yearPart = nationalId.substring(1, 3);
        const monthPart = nationalId.substring(3, 5);
        const dayPart = nationalId.substring(5, 7);
        const firstDigit = nationalId.charAt(0);

        let fullYear;
        if (firstDigit === '2') {
            fullYear = `19${yearPart}`;
        } else if (firstDigit === '3') {
            fullYear = `20${yearPart}`;
        } else {
            return {
                error: "Invalid year identifier in national ID."
            };
        }

        const month = parseInt(monthPart, 10);
        const day = parseInt(dayPart, 10);
        if (month < 1 || month > 12 || day < 1 || day > 31) {
            return {
                error: "Invalid date in national ID."
            };
        }

        const birthdate = `${fullYear}-${monthPart}-${dayPart}`;
        const genderDigit = parseInt(nationalId.charAt(12), 10);
        const gender = genderDigit % 2 === 1 ? "Male" : "Female";

        return {
            gender,
            birthdate
        };
    }

    /**
     * Location and Program Selection Handlers
     */

    /**
     * Handle governorate selection change
     * @param {Event} e - Change event
     */
    function handleGovernorateChange(e) {

        const selectedGovernorateId = e.target.value;
        const citySelect = e.target.id === 'parentGovernorate' ?
            $('#parentCity') : $('#city');
        console.log(citySelect);
        updateCityOptions(citySelect, selectedGovernorateId);
    }

    /**
     * Update city dropdown options
     * @param {jQuery} citySelect - City select element
     * @param {string} governorateId - Selected governorate ID
     */
    function updateCityOptions(citySelect, governorateId) {

        if (lang == 'ar') {
            citySelect.prop('disabled', false).html('<option value="">أختر المدينة</option>');
        } else {
            citySelect.prop('disabled', false).html('<option value="">Select City</option>');
        }
        const cityOptions = $('#city-list option').filter(function() {
            return $(this).data('governorate-id') == governorateId;
        });

        cityOptions.each(function() {
            const option = document.createElement('option');
            option.value = $(this).data('city-id');
            option.text = $(this).val();
            citySelect.append(option);
        });
    }

    /**
     * Handle faculty selection change
     */
    function handleFacultyChange() {
        const facultyId = $(this).val();
        const programSelect = $('#program');
        updateProgramOptions(programSelect, facultyId);
    }

    /**
     * Update program dropdown options
     * @param {jQuery} programSelect - Program select element
     * @param {string} facultyId - Selected faculty ID
     */
    function updateProgramOptions(programSelect, facultyId) {
        if (lang == 'ar') {
            programSelect.html('<option value="">أختر البرنامج</option>').prop('disabled', !facultyId);
        } else {
            programSelect.html('<option value="">Select Program</option>').prop('disabled', !facultyId);
        }
        if (facultyId) {
            $('#faculty-programs option').filter(function() {
                return $(this).data('faculty-id') == facultyId;
            }).each(function() {
                const option = document.createElement('option');
                option.value = $(this).data('program-id');
                option.text = $(this).val();
                programSelect.append(option);
            });
        }
    }

    /**
     * Initialize Event Listeners
     */
    function initializeEventListeners() {
        // Form field validation
        $('input, textarea, select').on('input change', function() {
            validateField(this);
        });

        // Navigation buttons
        $('#nextBtn').click(handleNextButton);
        $('#prevBtn').click(handlePreviousButton);
        $('.nav-tabs .nav-link').click(handleTabClick);
        $('#submitBtn').click(handleFormSubmission);

        // National ID processing
        $('#nationalId').on('input', function() {
            $(this).siblings('.error-message').text('');
            $('#birthDate').siblings('.error-message').text('');
            $('#gender').siblings('.error-message').text('');

            const result = generateGenderAndBirthdate($(this).val());

            if (result.error) {
                $(this).siblings('.error-message').text(result.error);
                $('#birthDate').val('');
                $('#gender').val('');
            } else {
                $('#birthDate').val(result.birthdate);
                $('#gender').val(result.gender.toLowerCase());
            }
        });

        // Location selections
        $('#governorate, #parentGovernorate').on('change', handleGovernorateChange);

        $('#faculty').on('change', handleFacultyChange);

        // Parent abroad handling
        $('#isParentAbroad').on('change', function() {
            toggleAbroadFields();
        });

        // Living with parent handling
        $('#livingWithParent').on('change', function() {
            const livingWithParent = $(this).val();
            $('#parentGovernorateCityDiv').toggleClass('d-none', livingWithParent !== '0');
        });

        // Sibling in dorm handling
        $('#hasSiblingInDorm').on('change', function() {
            const hasSibling = $(this).val();
            $('#siblingInfoSection').toggleClass('d-none', hasSibling !== '1');
        });
    }

    /**
     * Field Visibility Functions
     */

    /**
     * Toggle fields based on parent abroad status
     */
    function toggleAbroadFields() {
        const isParentAbroad = $('#isParentAbroad').val();
        resetAbroadFields();

        if (isParentAbroad === '1') {
            $('#abroadCountryDiv, #step6, #step6-tab').removeClass('d-none');
        } else if (isParentAbroad === '0') {
            $('#livingWithParentDiv').removeClass('d-none');
        }
    }

    /**
     * Reset all abroad-related fields to hidden state
     */
    function resetAbroadFields() {
        $('#abroadCountryDiv, #livingWithParentDiv, #parentGovernorateCityDiv, #step6, #step6-tab')
            .addClass('d-none');
    }

    /**
     * Initialize Form
     */
    function initializeForm() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Initialize navigation
        updateNavigation();

        // Initialize event listeners
        initializeEventListeners();

        // Trigger initial field states
        $('#nationalId').trigger('input');
        $('#isParentAbroad').trigger('change');
        $('#hasSiblingInDorm').trigger('change');
        $('#livingWithParent').trigger('change');
    }

    // Start initialization
    initializeForm();
});