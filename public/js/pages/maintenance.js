$(document).ready(function () {
    const isArabic = $('html').attr('dir') === 'rtl';

    // Initialize DataTable
    const table = $('#default-datatable').DataTable({
        responsive: true,
        language: isArabic ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        } : {},

    });

    // Reference to the toggle button and icon
    const toggleButton = document.getElementById("toggleButton");
    if (toggleButton) {
        const icon = toggleButton.querySelector("i");
        document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function () {
            icon.classList.remove("fa-search-plus");
            icon.classList.add("fa-search-minus");
        });
    
        document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function () {
            icon.classList.remove("fa-search-minus");
            icon.classList.add("fa-search-plus");
        });
    }
    function toggleButtonLoading(button, isLoading) {
        const hasClassBtnRound = button.hasClass('btn-round');

        if (isLoading) {
            if (!button.data('original-text')) {
                button.data('original-text', button.html());
            }

            if (hasClassBtnRound) {
                button.html('<i class="fa fa-spinner fa-spin"></i>')
                    .addClass('loading')
                    .prop('disabled', true);
            } else {
                button.html('<i class="fa fa-spinner fa-spin"></i> Downloading...')
                    .addClass('loading')
                    .prop('disabled', true);
            }
        } else {
            button.html(button.data('original-text'))
                .removeClass('loading')
                .prop('disabled', false);
            button.removeData('original-text');
        }
    }

   

    function updateStatus(requestId, status) {
        let updateStatusUrl = window.routes.updateStatus.replace(':id', requestId);

        const formData = {
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: updateStatusUrl,
            type: 'PUT',
            data: formData,
            beforeSend: function () {
                toggleButtonLoading($('#in-progress-status-btn-' + requestId), true);
                toggleButtonLoading($('#reject-status-btn-' + requestId), true);
            },
            success: function (response) {
                if (response.success) {
                    swal('Success!', response.message || 'Status updated successfully.', 'success');
                    location.reload();
                }
            },
            error: function (xhr) {
                swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
            },
            complete: function () {
                toggleButtonLoading($('#in-progress-status-btn-' + requestId), false);
                toggleButtonLoading($('#reject-status-btn-' + requestId), false);
            }
        });
    }

    $(document).on('click', '[id^="in-progress-status-btn-"]', function () {
        const button = $(this);
        const requestId = button.attr('id').split('-').pop();
        updateStatus(requestId, 'in_progress');
    });

    $(document).on('click', '[id^="reject-status-btn-"]', function () {
        const button = $(this);
        const requestId = button.attr('id').split('-').pop();
        updateStatus(requestId, 'rejected');
    });

    $(document).on('click', '[id^="complete-status-btn-"]', function () {
        const button = $(this);
        const requestId = button.attr('id').split('-').pop();
        updateStatus(requestId, 'completed');
    });

    $('#viewRequestModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var requestId = button.data('request-id');
    
        // Define the mapping of issue types and descriptions in both English and Arabic
        var issueTypeMapping = {
            'electrical_issues': {
                en: 'Electrical Issues',
                ar: 'مشاكل كهربائية'
            },
            'water_issues': {
                en: 'Water Issues',
                ar: 'مشاكل مائية'
            },
            'housing_issues': {
                en: 'Housing Issues',
                ar: 'مشاكل سكنية'
            },
            'General Housing': {
                en: 'General Housing',
                ar: 'مشاكل السكن العامة'
            }
        };
    
    var descriptionMapping = {
    'leakage': {
        en: 'Water Leakage',
        ar: 'تسرب مياه'
    },
    'sewage_problem': {
        en: 'Sewage Problem',
        ar: 'مشكلة في الصرف الصحي'
    },
    'plumbing_problem': {
        en: 'Plumbing Problem',
        ar: 'مشكلة في السباكة'
    },
    'bulb_replacement': {
        en: 'Bulb Replacement',
        ar: 'استبدال مصباح'
    },
    'fan_issue': {
        en: 'Fan Issue',
        ar: 'مشكلة في المروحة'
    },
    'water_heater_issue': {
        en: 'Water Heater Issue',
        ar: 'مشكلة في سخان المياه'
    },
    'electricity_problem': {
        en: 'Electricity Problem',
        ar: 'مشكلة كهربائية'
    },
    'furniture_damage': {
        en: 'Furniture Damage',
        ar: 'تلف الأثاث'
    },
    'appliance_issue': {
        en: 'Appliance Issue',
        ar: 'مشكلة في الأجهزة'
    },
    'door_window_issue': {
        en: 'Door/Window Problem',
        ar: 'مشكلة في الباب/النافذة'
    }
};

    
        // Detect current language of the page (check <html> tag's lang attribute)
        var currentLang = $('html').attr('lang') || 'en'; // Default to 'en' if lang attribute is not set
    
        $.ajax({
            url: window.routes.getIssues.replace(':id', requestId),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                $('#issueList').empty();
    
                if (response.issues.length > 0) {
                    response.issues.forEach(function (issue) {
                        var issueTypeDescription = issueTypeMapping[issue.issue_type] && issueTypeMapping[issue.issue_type][currentLang] || issue.issue_type;
                        var issueDescription = descriptionMapping[issue.description] && descriptionMapping[issue.description][currentLang] || issue.description;
    
                        $('#issueList').append('<li class="list-group-item">' + issueTypeDescription + ': ' + issueDescription + '</li>');
                    });
                } else {
                    $('#issueList').append('<li class="list-group-item">' + (currentLang === 'ar' ? 'لا توجد مشكلات' : 'No issues available') + '</li>'); // Adjust based on language
                }
    
                $('#additionalInfo').text(response.additional_info || (currentLang === 'ar' ? 'لا توجد معلومات إضافية' : 'No additional information available'));
            },
            error: function () {
                alert(currentLang === 'ar' ? 'حدث خطأ أثناء جلب المشكلات' : 'Error fetching issues');
            }
        });
    });
    

   

   
});
