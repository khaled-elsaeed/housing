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
            anErrorOccurred: 'An error occurred',
            assignedAt: 'Assigned at',
            staffAcceptedAt: 'Staff accepted at',
            staffCompletedAt: 'Staff completed at' // Added missing key
        },
        ar: {
            approve: 'موافقة',
            reject: 'رفض',
            acceptedAt: 'تم القبول في',
            noIssues: 'لا توجد مشكلات',
            noAdditionalInfo: 'لا توجد معلومات إضافية',
            errorFetchingIssues: 'حدث خطأ أثناء جلب المشكلات',
            statusUpdatedSuccessfully: 'تم تحديث الحالة بنجاح',
            anErrorOccurred: 'حدث خطأ',
            assignedAt: 'تم التعيين في',
            staffAcceptedAt: 'تم قبول الموظف في',
            staffCompletedAt: 'تم اكتمال الصيانة من قبل الموظف في' // Added missing key
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
    
                    let problems;
                    try {
                        problems = JSON.parse(data); // Convert JSON string to JS object
                    } catch (e) {
                        console.error("Invalid JSON format in problems field:", e);
                        return ''; // Return empty if JSON is malformed
                    }
    
                    if (!Array.isArray(problems)) return ''; // Ensure it's an array
    
                    let displayedProblems = problems.slice(0, 3) // Show only first 3 problems
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
            {
                data: 'status',
                name: 'status',
                render: function (data) {
                    if (!data) return 'N/A'; // Handle null values
    
                    let badgeClass = '';
                    switch (data) {
                        case 'pending':
                            badgeClass = 'badge bg-warning text-dark';
                            break;
                        case 'assigned':
                            badgeClass = 'badge bg-primary';
                            break;
                        case 'in_progress':
                            badgeClass = 'badge bg-info';
                            break;
                        case 'completed':
                            badgeClass = 'badge bg-success';
                            break;
                        default:
                            badgeClass = 'badge bg-secondary';
                            break;
                    }
    
                    return `<span class="${badgeClass}">${data.replace('_', ' ')}</span>`;
                }
            },
            { data: 'assigned_staff', name: 'assigned_staff' },
            {
                data: 'created_at',
                name: 'created_at',
                render: function (data) {
                    if (!data) return 'N/A'; // Handle null values safely
    
                    let date = new Date(data);
                    return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
                }
            },
            {
                data: 'has_photos',
                name: 'has_photos',
                render: function (data) {
                    // Display "Yes" or "No" based on whether photos exist
                    return data === 'Yes' ? `<span class="badge bg-success">Yes</span>` : `<span class="badge bg-secondary">No</span>`;
                }
            },
            {
                data: 'photos',
                name: 'photos',
                render: function (data) {
                    if (data === 'No photos') {
                        return data; // Return "No photos" if no photos exist
                    }
    
                    // Render buttons for each photo
                    return data;
                }
            },
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
    
                    if (data.status === 'assigned' && data.updated_at) {
                        return renderAssignedStatus(data);
                    }
    
                    if (data.status === 'in_progress') {
                        return renderInProgressStatus(data);
                    }
    
                    if (data.status === 'completed') {
                        return renderCompletedStatus(data);
                    }
    
                    return '';
                }
            }
        ],
        language: isArabic ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json"
        } : {}
    });

    function renderAcceptedStatus(data) {
        const lang = $('html').attr('lang') || 'en';
        let formattedDate = formatDate(data.actions); // Format the date
    
        return `
            <div class="accepted-status">
                <span class="badge badge-success">${gettext('acceptedAt', lang)}</span>
                <br>
                <large>${formattedDate}</large>
            </div>`;
    }

    function renderCompletedStatus(data) {
        const lang = $('html').attr('lang') || 'en';
        let formattedDate = formatDate(data.updated_at); 
        let assignedStaff = data.assigned_staff ? data.assigned_staff : 'N/A';
    
        return `
            <div class="completed-status">
                
                <strong>${gettext('staffCompletedAt', lang)}:</strong> ${formattedDate}
            </div>`;
    }
    
    function renderInProgressStatus(data) {
        const lang = $('html').attr('lang') || 'en';
        let formattedDate = formatDate(data.updated_at); 
        let assignedStaff = data.assigned_staff ? data.assigned_staff : 'N/A';
    
        return `
            <div class="in-progress-status">
               
                <strong>${gettext('staffAcceptedAt', lang)}:</strong> ${formattedDate}
            </div>`;
    }
    
    
    function renderAssignedStatus(data) {
        const lang = $('html').attr('lang') || 'en';
        let formattedDate = formatDate(data.updated_at); // Format the date
    
        return `
            <div class="assigned-status">
                <span class="badge badge-info">${gettext('assignedAt', lang)}</span>
                <br>
                <large>${formattedDate}</large>
            </div>`;
    }
    
    // **Helper function to format dates**
    function formatDate(dateString) {
        if (!dateString) return 'N/A'; // Handle null values safely
    
        let date = new Date(dateString);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
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

        const requestId = $('#maintenancAssignModal').data('requestId');
        const $submitButton = $(this).find('button[type="submit"]');
        const formData = {
            request_id: requestId,
            staff_id: $('#optionsSelect').val(),
            notes: $('#maintenanceNotes').val()
        };

        // Show loading state
        toggleButtonLoading($submitButton, true);

        // Submit the form
        $.ajax({
            url: window.routes.acceptRequest.replace(":id", formData.request_id),
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Fixing CSRF token retrieval
            },
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