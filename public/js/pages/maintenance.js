$(document).ready(function () {
    // Detect language direction
    const isArabic = $('html').attr('dir') === 'rtl';
    const lang = $('html').attr('lang') || 'en';

    // Enable tooltips for more problems
    $('body').tooltip({ selector: '.more-problems' });

    // Translation dictionary
    const translations = {
        en: {
            pending: "Pending",
            assigned: "Assigned",
            in_progress: "In Progress",
            completed: "Completed",
            approve: "Approve",
            reject: "Reject",
            acceptedAt: "Accepted at",
            noIssues: "No issues available",
            noAdditionalInfo: "No additional information available",
            errorFetchingIssues: "Error fetching issues",
            statusUpdatedSuccessfully: "Status updated successfully",
            anErrorOccurred: "An error occurred",
            assignedAt: "Assigned at",
            staffAcceptedAt: "Staff accepted at",
            staffCompletedAt: "Staff completed at",
            "Water and Sanitary Issues": "Water and Sanitary Issues",
            "Electrical Issues": "Electrical Issues",
            "General Housing Issues": "General Housing Issues",
            selectCategory: "Select a category",
            selectStaff: "Select a staff member",
            noStaffAvailable: "No staff available"
        },
        ar: {
            pending: "معلق",
            assigned: "تم التعيين",
            in_progress: "قيد التنفيذ",
            completed: "مكتمل",
            approve: "موافقة",
            reject: "رفض",
            acceptedAt: "تم القبول في",
            noIssues: "لا توجد مشكلات",
            noAdditionalInfo: "لا توجد معلومات إضافية",
            errorFetchingIssues: "حدث خطأ أثناء جلب المشكلات",
            statusUpdatedSuccessfully: "تم تحديث الحالة بنجاح",
            anErrorOccurred: "حدث خطأ",
            assignedAt: "تم التعيين في",
            staffAcceptedAt: "تم قبول الموظف في",
            staffCompletedAt: "تم اكتمال الصيانة من قبل الموظف في",
            "Water and Sanitary Issues": "مشاكل المياه والصرف الصحي",
            "Electrical Issues": "مشاكل الكهرباء",
            "General Housing Issues": "مشاكل السكن العامة",
            selectCategory: "اختر فئة",
            selectStaff: "اختر موظفًا",
            noStaffAvailable: "لا يوجد موظفون متاحون"
        }
    };
    

    // Translate text based on key and language
    function gettext(key) {
        return translations[lang]?.[key] || key;
    }

    // Format date for display
    function formatDate(dateString) {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    }

    // Toggle button loading state
    function toggleButtonLoading(button, isLoading) {
        const hasClassBtnRound = button.hasClass('btn-round');
        if (isLoading) {
            if (!button.data('original-text')) {
                button.data('original-text', button.html());
            }
            button.html(hasClassBtnRound ? '<i class="fa fa-spinner fa-spin"></i>' : '<i class="fa fa-spinner fa-spin"></i> Loading...')
                  .addClass('loading')
                  .prop('disabled', true);
        } else {
            button.html(button.data('original-text'))
                  .removeClass('loading')
                  .prop('disabled', false)
                  .removeData('original-text');
        }
    }

    // Render action buttons for pending status
    function renderPendingActions(data) {
        return `
            <div class="action-buttons">
                <button class="btn btn-round btn-success accept-btn" data-id="${data.id}" data-category="${data.category || 'maintenance'}" title="${gettext('approve')}">
                    <i class="feather icon-check"></i>
                </button>
                <button class="btn btn-round btn-danger reject-btn" data-id="${data.id}" title="${gettext('reject')}">
                    <i class="feather icon-x"></i>
                </button>
            </div>`;
    }

// Render status details
function renderAssignedStatus(data) {
    return `
        <div class="status-card assigned-status p-2 mb-2 rounded shadow-sm bg-light">
            <div class="d-flex align-items-center">
                <i class="fa fa-user-check text-primary me-2"></i>
                <div>
                    <span class="badge bg-primary text-white fw-normal">${gettext('assignedAt')}</span>
                    <div class="text-muted mt-1">${formatDate(data.assigned_at)}</div>
                </div>
            </div>
        </div>
    `;
}

function renderInProgressStatus(data) {
    return `
        <div class="status-card in-progress-status p-2 mb-2 rounded shadow-sm bg-light">
            <div class="d-flex align-items-center">
                <i class="fa fa-tools text-info me-2"></i>
                <div>
                    <span class="badge bg-info text-white fw-normal">${gettext('staffAcceptedAt')}</span>
                    <div class="text-muted mt-1">${formatDate(data.staff_accepted_at)}</div>
                </div>
            </div>
        </div>
    `;
}

function renderCompletedStatus(data) {
    return `
        <div class="status-card completed-status p-2 mb-2 rounded shadow-sm bg-light">
            <div class="d-flex align-items-center">
                <i class="fa fa-check-circle text-success me-2"></i>
                <div>
                    <span class="badge bg-success text-white fw-normal">${gettext('staffCompletedAt')}</span>
                    <div class="text-muted mt-1">${formatDate(data.completed_at)}</div>
                </div>
            </div>
        </div>
    `;
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
            { data: 'category', name: 'category',render: function (data) {
                return gettext(data,)
            } },
            {
                data: 'problems',
                name: 'problems',
                render: function (data) {
                    const problems = data.split(',');
                    let displayedProblems = problems.slice(0, 3)
                        .map(problem => `<span class="badge bg-primary">${problem}</span>`)
                        .join(' ');
                    if (problems.length > 3) {
                        const moreProblems = problems.slice(3).join(', ');
                        displayedProblems += ` <span class="text-info more-problems" data-bs-toggle="tooltip" title="${moreProblems}">+${problems.length - 3} more</span>`;
                    }
                    return displayedProblems;
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function (data) {
                    if (!data) return "N/A";
                    let badgeClass;
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
                    }
                    return `<span class="${badgeClass}">${gettext(data)}</span>`;
                }
            },
            { data: 'assigned_staff', name: 'assigned_staff' },
            {
                data: 'created_at',
                name: 'created_at',
                render: function (data) {
                    return data ? formatDate(data) : "N/A";
                }
            },
            {
                data: 'has_photos',
                name: 'has_photos',
                render: function (data) {
                    return data === 'Yes' ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                }
            },
            {
                data: 'photos',
                name: 'photos',
                render: function (data) {
                    return data === 'No photos' ? data : data;
                }
            },
            {
                data: null,
                render: function (data) {
                    switch (data.status) {
                        case 'pending':
                            return renderPendingActions(data);
                        case 'assigned':
                            return data.assigned_at ? renderAssignedStatus(data) : '';
                        case 'in_progress':
                            return renderInProgressStatus(data);
                        case 'completed':
                            return renderCompletedStatus(data);
                        default:
                            return '';
                    }
                }
            }
        ],
        language: isArabic ? { url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json" } : {}
    });

    // Event handlers for search and filter
    $('#searchBox').on('keyup', function () {
        table.ajax.reload();
    });

    $('#statusFilter').on('change', function () {
        table.ajax.reload();
    });

    // Toggle search collapse icon
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

    // Handle accept button click
    $(document).on('click', '.accept-btn', function () {
        const requestId = $(this).data('id');
        const category = $(this).data('category');
        $('#maintenancAssignModal').modal('show')
            .data('requestId', requestId);
            console.log(category);
        $('#categorySelect').val(category).trigger('change');
    });

    // Fetch staff options on category change
    $('#categorySelect').on('change', function () {
        const selectedCategory = $(this).val();
        const $optionsSelect = $('#optionsSelect');
        $optionsSelect.empty().append(`<option value="">${gettext('selectStaff')}</option>`);

        if (selectedCategory) {
            $.ajax({
                url: window.routes.fetchStaff,
                method: 'GET',
                data: { category: selectedCategory },
                success: function (response) {
                    $optionsSelect.empty().append(`<option value="">${gettext('selectStaff')}</option>`);
                    if (response.staff && response.staff.length > 0) {
                        response.staff.forEach(staff => {
                            $optionsSelect.append(`<option value="${staff.id}">${staff.name}</option>`);
                        });
                    } else {
                        $optionsSelect.empty().append(`<option value="">${gettext('noStaffAvailable')}</option>`);
                    }
                },
                error: function () {
                    $optionsSelect.empty().append(`<option value="">${gettext('errorFetchingIssues')}</option>`);
                }
            });
        }
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

        toggleButtonLoading($submitButton, true);

        $.ajax({
            url: window.routes.acceptRequest.replace(":id", formData.request_id),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: formData,
            success: function () {
                $('#maintenancAssignModal').modal('hide');
                table.ajax.reload();
                swal({
                    type: 'success',
                    title: gettext('statusUpdatedSuccessfully'),
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            error: function () {
                swal({
                    type: 'error',
                    title: gettext('anErrorOccurred'),
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