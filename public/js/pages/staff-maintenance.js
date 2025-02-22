// Maintenance Request Handler
class MaintenanceRequestHandler {
    constructor() {
        this.isArabic = $('html').attr('dir') === 'rtl';
        this.lang = $('html').attr('lang') || 'en';
        this.translations = {
            en: {
                pending: "Pending",
                assigned: "Assigned",
                in_progress: "In Progress",
                completed: "Completed",
                approve: "Approve",
                reject: "Reject",
                yes:"Yes",
                No:"No",
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
                "General Housing Issues": "General Housing Issues"
            },
            ar: {
                pending: "معلق",
                assigned: "تم التعيين",
                in_progress: "قيد التنفيذ",
                completed: "مكتمل",
                approve: "موافقة",
                yes:"نعم",
                No:"لا",
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
                "General Housing Issues": "مشاكل السكن العامة"
            }
        };
        
        this.table = null;
        this.init();
    }

    init() {
        this.initializeTooltips();
        this.initializeDataTable();
        this.setupEventListeners();
    }

    initializeTooltips() {
        $('body').tooltip({ selector: '.more-problems' });
    }

    getText(key) {
        return this.translations[this.lang]?.[key] || key;
    }

    formatDate(dateString) {
        if (!dateString) return "N/A";
        const date = new Date(dateString);
        return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    }

    toggleButtonLoading(button, isLoading) {
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

    renderStatusBadge(status) {
        if (!status) return "N/A";
        const badgeClasses = {
            pending: 'badge bg-warning text-dark',
            assigned: 'badge bg-primary',
            in_progress: 'badge bg-info',
            completed: 'badge bg-success',
            default: 'badge bg-secondary'
        };
        const badgeClass = badgeClasses[status] || badgeClasses.default;
        return `<span class="${badgeClass}">${this.getText(status)}</span>`;
    }

    renderAssignedStatus(data) {
        return `
            <button class="btn btn-round btn-success accept-btn" data-id="${data.id}" title="${this.getText('approve')}">
                <i class="feather icon-check"></i>
            </button>
        `;
    }

    renderInProgressStatus(data) {
        return `
            <div class="status-card in-progress-status p-2 mb-2 rounded shadow-sm bg-light">
                <div class="d-flex align-items-center">
                    <i class="fa fa-tools text-info me-2"></i>
                    <div>
                        <span class="badge bg-info text-white fw-normal">${this.getText('staffAcceptedAt')}</span>
                        <div class="text-muted mt-1">${this.formatDate(data.staff_accepted_at)}</div>
                    </div>
                </div>
                <button class="btn btn-success btn-sm complete-btn mt-2" data-id="${data.id}">
                    <i class="feather icon-check"></i> ${this.getText('completed')}
                </button>
            </div>
        `;
    }

    renderCompletedStatus(data) {
        return `
            <div class="status-card completed-status p-2 mb-2 rounded shadow-sm bg-light">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle text-success me-2"></i>
                    <div>
                        <span class="badge bg-success text-white fw-normal">${this.getText('staffCompletedAt')}</span>
                        <div class="text-muted mt-1">${this.formatDate(data.completed_at)}</div>
                    </div>
                </div>
            </div>
        `;
    }

    renderProblems(problems) {
        const problemsList = problems.split(',');
        let displayedProblems = problemsList.slice(0, 3)
            .map(problem => `<span class="badge bg-primary">${problem}</span>`)
            .join(' ');
        
        if (problemsList.length > 3) {
            const moreProblems = problemsList.slice(3).join(', ');
            displayedProblems += ` <span class="text-info more-problems" data-bs-toggle="tooltip" title="${moreProblems}">+${problemsList.length - 3} more</span>`;
        }
        return displayedProblems;
    }

    initializeDataTable() {
        this.table = $('#default-datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: window.routes.fetchRequests,
                type: 'GET',
                data: (d) => {
                    d.customSearch = $('#searchBox').val();
                    d.status = $('#statusFilter').val();
                }
            },
            columns: this.getTableColumns(),
            language: this.isArabic ? { url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json" } : {}
        });
    }

    getTableColumns() {
        return [
            { data: 'resident_name', name: 'resident_name' },
            { data: 'resident_location', name: 'resident_location' },
            { data: 'resident_phone', name: 'resident_phone' },
            { 
                data: 'category', 
                name: 'category',
                render: (data) => this.getText(data)
            },
            {
                data: 'problems',
                name: 'problems',
                render: (data) => this.renderProblems(data)
            },
            {
                data: 'status',
                name: 'status',
                render: (data) => this.renderStatusBadge(data)
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: (data) => this.formatDate(data)
            },
            {
                data: 'has_photos',
                name: 'has_photos',
                render: (data) => data === 'Yes' ? 
                    `<span class="badge bg-success">${this.getText('yes')}</span>` : 
                    `<span class="badge bg-secondary">${this.getText('No')}</span>`
            }
,            
            
            {
                data: 'photos',
                name: 'photos',
                render: (data) => data === this.getText('No photos') ? data : data
            },
            {
                data: null,
                render: (data) => {
                    switch (data.status) {
                        case 'assigned':
                            return data.assigned_at ? this.renderAssignedStatus(data) : '';
                        case 'in_progress':
                            return this.renderInProgressStatus(data);
                        case 'completed':
                            return this.renderCompletedStatus(data);
                        default:
                            return '';
                    }
                }
            }
        ];
    }

    setupEventListeners() {
        $('#searchBox').on('keyup', () => this.table.ajax.reload());
        $('#statusFilter').on('change', () => this.table.ajax.reload());
        this.setupSearchCollapseToggle();
        this.setupAcceptButtonHandler();
        this.setupCompleteButtonHandler();

        
    }

    setupSearchCollapseToggle() {
        const toggleButton = document.getElementById("toggleButton");
        if (toggleButton) {
            const icon = toggleButton.querySelector("i");
            document.getElementById("collapseExample").addEventListener("shown.bs.collapse", () => {
                icon.classList.remove("fa-search-plus");
                icon.classList.add("fa-search-minus");
            });
            document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", () => {
                icon.classList.remove("fa-search-minus");
                icon.classList.add("fa-search-plus");
            });
        }
    }

    setupAcceptButtonHandler() {
        $(document).on('click', '.accept-btn', (event) => {
            const button = $(event.currentTarget);
            const requestId = button.data('id');
            this.handleAcceptRequest(button, requestId);
        });
    }

    setupCompleteButtonHandler() {
        $(document).on('click', '.complete-btn', (event) => {
            const button = $(event.currentTarget);
            this.handleCompleteRequest(button, button.data('id'));
        });
    }

    handleAcceptRequest(button, requestId) {
        const formData = { request_id: requestId };
        this.toggleButtonLoading(button, true);

        $.ajax({
            url: window.routes.acceptRequest.replace(":id", requestId),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: formData,
            success: () => {
                $('#maintenancAssignModal').modal('hide');
                this.table.ajax.reload();
                this.showNotification('success', this.getText('statusUpdatedSuccessfully'));
            },
            error: () => {
                this.showNotification('error', this.getText('anErrorOccurred'));
            },
            complete: () => {
                this.toggleButtonLoading(button, false);
            }
        });
    }

    handleCompleteRequest(button, requestId) {
        this.toggleButtonLoading(button, true);
        $.ajax({
            url: window.routes.completeRequest.replace(":id", requestId),
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: () => {
                this.table.ajax.reload();
                this.showNotification('success', this.getText('statusUpdatedSuccessfully'));
            },
            error: () => {
                this.showNotification('error', this.getText('anErrorOccurred'));
            },
            complete: () => {
                this.toggleButtonLoading(button, false);
            }
        });
    }


    showNotification(type, message) {
        swal({
            type: type,
            title: message,
            showConfirmButton: false,
            timer: 1500
        });
    }
}

// Initialize the handler when document is ready
$(document).ready(() => {
    new MaintenanceRequestHandler();
});