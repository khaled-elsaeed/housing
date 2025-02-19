$(document).ready(function () {
    const isArabic = $('html').attr('dir') === 'rtl';
    $('body').tooltip({ selector: '.more-problems' });

    // Translation dictionary
    const translations = {
        en: {
            approve: 'Approve',
            reject: 'Reject',
            acceptedAt: 'Accepted at',
            noIssues: 'No issues available',
            noAdditionalInfo: 'No additional information available',
            errorFetchingIssues: 'Error fetching issues',
            statusUpdatedSuccessfully: 'Status updated successfully',
            anErrorOccurred: 'An error occurred'
        },
        ar: {
            approve: 'موافقة',
            reject: 'رفض',
            acceptedAt: 'تم القبول في',
            noIssues: 'لا توجد مشكلات',
            noAdditionalInfo: 'لا توجد معلومات إضافية',
            errorFetchingIssues: 'حدث خطأ أثناء جلب المشكلات',
            statusUpdatedSuccessfully: 'تم تحديث الحالة بنجاح',
            anErrorOccurred: 'حدث خطأ'
        }
    };

    // Helper function to translate text
    function gettext(key, lang) {
        return translations[lang]?.[key] || key; // Fallback to the key if translation is missing
    }

    // Initialize DataTable
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: window.routes.fetchRequests,
            type: 'GET',
            data: function (d) {
                d.customSearch = $('#searchBox').val();
                d.status = $('#statusFilter').val();
            }
        },
        columns: [
            { data: 'resident_name', name: 'resident_name' },
            { data: 'resident_location', name: 'resident_location' },
            { data: 'resident_phone', name: 'resident_phone' },
            { data: 'category', name: 'category' },
            {
                data: 'problems',
                name: 'problems',
                render: function (data) {
                    if (!data) return ''; // Handle null values safely

                    let problems = JSON.parse(data); // Convert JSON string to JS object
                    if (!Array.isArray(problems)) return ''; // Ensure it's an array

                    let displayedProblems = problems.slice(0, 3) // Show only first 3
                        .map(problem => `<span class="badge bg-primary">${problem.name}</span>`)
                        .join(' ');

                    if (problems.length > 3) {
                        let moreProblems = problems.slice(3) // Remaining problems
                            .map(problem => problem.name).join(', ');

                        displayedProblems += ` <span class="text-info more-problems" 
                                                data-bs-toggle="tooltip" 
                                                title="${moreProblems}">
                                                +${problems.length - 3} more
                                            </span>`;
                    }

                    return displayedProblems;
                }
            },
            { data: 'status', name: 'status' },
            { data: 'assigned_staff', name: 'assigned_staff' },
            { data: 'created_at', name: 'created_at' },
            {
                data: null,
                render: function (data) {
                    const lang = $('html').attr('lang') || 'en';
                    if (data.status === 'pending') {
                        return renderPendingActions(data);
                    }
                    if (data.status === 'accepted' && data.updated_at) {
                        return renderAcceptedStatus(data);
                    }
                    return '';
                }
            }
        ],
        language: isArabic ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json"
        } : {}
    });

    // Helper function to render accepted status
    function renderAcceptedStatus(data) {
        const lang = $('html').attr('lang') || 'en';
        const formattedDate = data.actions; // Assuming `actions` contains the formatted date
        return `
            <div class="accepted-status">
                <span class="badge badge-success">${gettext('acceptedAt', lang)}</span>
                <br>
                <large>${formattedDate}</large>
            </div>`;
    }

    // Reload table on search or filter change
    $('#searchBox').on('keyup', function () {
        table.ajax.reload();
    });

    $('#statusFilter').on('change', function () {
        table.ajax.reload();
    });

    // Toggle button icon for search collapse
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

    // Function to toggle button loading state
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
                button.html('<i class="fa fa-spinner fa-spin"></i> Loading...')
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

    // Modified renderPendingActions function to include category data
    function renderPendingActions(data) {
        const lang = $('html').attr('lang') || 'en';
        return `
            <div class="action-buttons">
                <button class="btn btn-round btn-success accept-btn" 
                    data-id="${data.id}" 
                    data-category="${data.category || 'maintenance'}"
                    title="${gettext('approve', lang)}">
                    <i class="feather icon-check"></i>
                </button>
                <button class="btn btn-round btn-danger reject-btn" 
                    data-id="${data.id}" 
                    title="${gettext('reject', lang)}">
                    <i class="feather icon-x"></i>
                </button>
            </div>`;
    }

    // Handle accept button click
    $(document).on('click', '.accept-btn', function () {
        const requestId = $(this).data('id');
        const category = $(this).data('category');

        // Open modal and pre-select category
        $('#maintenancAssignModal').modal('show');
        $('#categorySelect').val(category).trigger('change');
        $('#maintenancAssignModal').data('requestId', requestId);
    });

    // Handle category change to load available options
    $('#categorySelect').on('change', function () {
        const selectedCategory = $(this).val();
        const optionsSelect = $('#optionsSelect');

        // Clear current options
        optionsSelect.empty().append('<option>Loading...</option>');

        // Fetch available options for the selected category
        $.ajax({
            url: window.routes.fetchStaff,
            method: 'GET',
            data: { category: selectedCategory },
            success: function (response) {
                optionsSelect.empty();
                if (response.staff && response.staff.length > 0) {
                    response.staff.forEach(staff => {
                        optionsSelect.append(`<option value="${staff.id}">${staff.name}</option>`);
                    });
                } else {
                    optionsSelect.append('<option value="">No options available</option>');
                }
            },
            error: function () {
                optionsSelect.empty().append('<option value="">Error loading options</option>');
            }
        });
    });

    // Handle maintenance form submission
    $('#maintenanceForm').on('submit', function (e) {
        e.preventDefault();

        const requestId = $('#maintenanceModal').data('requestId');
        const $submitButton = $(this).find('button[type="submit"]');
        const formData = {
            request_id: requestId,
            category: $('#categorySelect').val(),
            option_id: $('#optionsSelect').val(),
            notes: $('#maintenanceNotes').val()
        };

        // Show loading state
        toggleButtonLoading($submitButton, true);

        // Submit the form
        $.ajax({
            url: window.routes.acceptRequest,
            method: 'POST',
            data: formData,
            success: function (response) {
                $('#maintenanceModal').modal('hide');
                table.ajax.reload();

                // Show success message using SweetAlert2
                swal({
                    type: 'success',
                    title: gettext('statusUpdatedSuccessfully', $('html').attr('lang') || 'en'),
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                // Show error message using SweetAlert2
                swal({
                    type: 'error',
                    title: gettext('anErrorOccurred', $('html').attr('lang') || 'en'),
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            complete: function () {
                toggleButtonLoading($submitButton, false);
            }
        });
    });
});