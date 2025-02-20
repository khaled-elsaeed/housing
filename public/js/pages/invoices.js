$(document).ready(function() {
    const translations = {
        en: {
            studentDetails: "Student Details",
            name: "Name",
            faculty: "Faculty",
            building: "Building",
            apartment: "Apartment",
            room: "Room",
            uploadedPictures: "Uploaded Pictures",
            view: "View",
            rejected: "Rejected",
            download: "Download",
            accept: "Accept",
            reject: "Reject",
            noDetails: "No details or pictures available for this applicant.",
            loadingFailed: "Failed to load details. Please try again later.",
            balance: "Balance",
            paidAmount: "Paid Amount",
            invoiceDetails: "Invoice Details",
            category: "Category",
            amount: "Amount",
            paid: "Paid",
            overpaymentAmount: "Overpayment Amount",
            enterOverpayment: "Enter overpayment amount (if any)",
            adminNotes: "Admin Notes",
            addNotes: "Add any notes about this invoice (optional)",
            areYouSureReject: "You are about to reject this invoice. Are you sure?",
            areYouSureAccept: "You are about to accept this invoice with the selected payments. Are you sure?",
            warning: "Warning",
            selectPayment: "Please select at least one payment detail to accept.",
            negativeAmount: "Overpayment amount cannot be negative.",
            success: "Success",
            error: "Error",
            errorUpdating: "Error updating payment status: ",
            noDataAvailable: "No data available.",
            confirmAction: "Confirm Action",
            cancel: "Cancel",
            proceed: "Proceed",
            paymentProof: "Payment Proof",
            totalAmount: "Total Amount",
            remainingBalance: "Remaining Balance",
            notes: "Notes",
            referenceNumber: 'referenceNumber',
            fee:"Fee",
            insurance:"Insurance",
            enterReferenceNumber: 'enterReferenceNumber',
            optional: "Optional",
        },
        ar: {
            studentDetails: "تفاصيل الطالب",
            name: "الاسم",
            faculty: "الكلية",
            rejected: "مرفوض",
            fee:"مصاريف",
            insurance:"تأمين",
            building: "المبنى",
            apartment: "الشقة",
            referenceNumber: 'الرقم المرجعي',
            enterReferenceNumber: 'أدخل الرقم المرجعي',
            room: "الغرفة",
            uploadedPictures: "الصور المرفوعة",
            view: "عرض",
            download: "تحميل",
            accept: "قبول",
            reject: "رفض",
            noDetails: "لا توجد تفاصيل أو صور لهذا الطالب.",
            loadingFailed: "فشل في تحميل التفاصيل. يرجى المحاولة مرة أخرى لاحقاً.",
            balance: "الرصيد",
            paidAmount: "المبلغ المدفوع",
            invoiceDetails: "تفاصيل الفاتورة",
            category: "الفئة",
            amount: "المبلغ",
            paid: "مدفوع",
            overpaymentAmount: "مبلغ الدفع الزائد",
            enterOverpayment: "أدخل مبلغ الدفع الزائد (إن وجد)",
            adminNotes: "ملاحظات الإدارة",
            addNotes: "أضف أي ملاحظات حول هذه الفاتورة (اختياري)",
            areYouSureReject: "أنت على وشك رفض هذه الفاتورة. هل أنت متأكد؟",
            areYouSureAccept: "أنت على وشك قبول هذه الفاتورة مع المدفوعات المحددة. هل أنت متأكد؟",
            warning: "تحذير",
            selectPayment: "يرجى تحديد تفاصيل دفع واحد على الأقل للقبول.",
            negativeAmount: "لا يمكن أن يكون مبلغ الدفع الزائد سالباً.",
            success: "نجاح",
            error: "خطأ",
            errorUpdating: "خطأ في تحديث حالة الدفع: ",
            noDataAvailable: "لا تتوفر بيانات.",
            confirmAction: "تأكيد الإجراء",
            cancel: "إلغاء",
            proceed: "متابعة",
            paymentProof: "إثبات الدفع",
            totalAmount: "المبلغ الإجمالي",
            remainingBalance: "الرصيد المتبقي",
            notes: "ملاحظات",
            optional: "اختياري",
        },
    };

    const lang = $("html").attr("lang") || "en";

    // Core utility functions
    function getTranslation(key, language = 'en') {
        // Ensure the translations object and language exist
        if (!translations || !translations[language]) {
            console.error(`Translations not loaded for language: ${language}`);
            return key; // Return the key itself as a fallback
        }

        // Get the translation for the key
        const translation = translations[language][key];

        // If the translation is not found, log a warning and return the key
        if (!translation) {
            console.warn(`Translation not found for key: ${key}`);
            return key; // Return the key itself as a fallback
        }

        return translation;
    }

    function getLabels(language = 'en') {
        return {
            studentDetails: getTranslation('studentDetails', language),
            name: getTranslation('name', language),
            faculty: getTranslation('faculty', language),
            building: getTranslation('building', language),
            apartment: getTranslation('apartment', language),
            room: getTranslation('room', language),
            uploadedPictures: getTranslation('uploadedPictures', language),
            view: getTranslation('view', language),
            download: getTranslation('download', language),
            accept: getTranslation('accept', language),
            reject: getTranslation('reject', language),
            noDetails: getTranslation('noDetails', language),
            loadingFailed: getTranslation('loadingFailed', language),
            balance: getTranslation('balance', language),
            paidAmount: getTranslation('paidAmount', language),
            invoiceDetails: getTranslation('invoiceDetails', language),
            category: getTranslation('category', language),
            amount: getTranslation('amount', language),
            paid: getTranslation('paid', language),
            referenceNumber: getTranslation('referenceNumber', language),
            enterReferenceNumber: getTranslation('enterReferenceNumber', language),

            overpaymentAmount: getTranslation('overpaymentAmount', language),
            enterOverpayment: getTranslation('enterOverpayment', language),
            adminNotes: getTranslation('adminNotes', language),
            addNotes: getTranslation('addNotes', language),
            areYouSureReject: getTranslation('areYouSureReject', language),
            areYouSureAccept: getTranslation('areYouSureAccept', language),
            warning: getTranslation('warning', language),
            selectPayment: getTranslation('selectPayment', language),
            negativeAmount: getTranslation('negativeAmount', language),
            success: getTranslation('success', language),
            error: getTranslation('error', language),
            errorUpdating: getTranslation('errorUpdating', language),
            noDataAvailable: getTranslation('noDataAvailable', language),
            confirmAction: getTranslation('confirmAction', language),
            cancel: getTranslation('cancel', language),
            proceed: getTranslation('proceed', language),
            paymentProof: getTranslation('paymentProof', language),
            totalAmount: getTranslation('totalAmount', language),
            remainingBalance: getTranslation('remainingBalance', language),
            notes: getTranslation('notes', language),
            optional: getTranslation('optional', language),
        };
    }

    // DataTable initialization and setup
    const table = $("#default-datatable").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: window.routes.fetchInvoices, // URL to your Laravel route
            data: function(d) {
                d.customSearch = $("#searchBox").val();
                d.gender = $("#genderFilter").val();
            },
        },
        columns: [

            // Other columns
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'national_id',
                name: 'national_id'
            },
            {
                data: 'faculty',
                name: 'faculty'
            },
            {
                data: 'phone',
                name: 'phone'
            }, {
                data: 'reservation_duration',
                name: 'reservation_duration'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'admin_approval',
                name: 'admin_approval'
            },
            // Add the action column first
            // Add the action column first
            {
                data: null, // Use null because this column is not tied to any specific data
                render: function(data, type, row) {
                    // Create the button dynamically
                    return '<button type="button" class="btn btn-sm btn-info-rgba" data-invoice-id="' + row.id + '" id="details-btn" title="More Details"><i class="feather icon-info"></i></button>';
                },

            },
        ],
        language: lang === "ar" ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        } : {},
    });

    $("#searchBox").on("keyup", function() {
        table.ajax.reload();
    });

    $("#genderFilter").on("change", function() {
        table.ajax.reload();
    });

    // Stats and data fetching
    function fetchStats() {
        $.ajax({
            url: window.routes.fetchStats,
            method: "GET",
            dataType: "json",
            success: function(data) {
                // Total Invoices
                $("#totalInvoice").text(data.totalInvoice);
                $("#totalMaleInvoice").text(data.totalMaleInvoice);
                $("#totalFemaleInvoice").text(data.totalFemaleInvoice);

                // Paid Invoices
                $("#totalPaidInvoice").text(data.totalPaidInvoice);
                $("#totalPaidMaleInvoice").text(data.totalPaidMaleInvoice);
                $("#totalPaidFemaleInvoice").text(data.totalPaidFemaleInvoice);

                // Unpaid Invoices
                $("#totalUnpaidInvoice").text(data.totalUnpaidInvoice);
                $("#totalUnpaidMaleInvoice").text(data.totalUnpaidMaleInvoice);
                $("#totalUnpaidFemaleInvoice").text(data.totalUnpaidFemaleInvoice);

                // Accepted Payments
                $("#totalAcceptedPayments").text(data.totalAcceptedPayments);
                $("#totalAcceptedMalePayments").text(data.totalAcceptedMalePayments);
                $("#totalAcceptedFemalePayments").text(data.totalAcceptedFemalePayments);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching summary data:", error);
            },
        });
    }

    // Invoice details handling
    let currentInvoiceDetails = {
        id: null,
        paidDetails: []
    };

    function showInvoiceDetails(invoiceId) {
        const modalBody = $("#applicantDetailsModal .modal-body");
        const labels = getLabels(lang);

        // Reset current invoice tracking
        currentInvoiceDetails = {
            id: invoiceId,
            paidDetails: []
        };

        showLoadingSpinner(modalBody);

        $.ajax({
            url: window.routes.fetchInvoiceDetails.replace(":id", invoiceId),
            method: "GET",
            success: function(response) {
                if (!response.invoiceDetails || response.invoiceDetails.length === 0) {
                    showNoDataMessage(modalBody, labels.noDetails);
                    return;
                }

                const {
                    studentDetails,
                    invoiceDetails,
                    media,
                    status,
                    invoice_id
                } = response;
                renderInvoiceDetails(modalBody, studentDetails, invoiceDetails, media, status, invoice_id, labels);
                attachEventListeners(response, labels);
            },
            error: function() {
                showErrorMessage(modalBody, labels.loadingFailed);
            },
        });

        if (!$("#applicantDetailsModal").hasClass("show")) {
            $("#applicantDetailsModal").modal("show");
        }
    }

    // Helper Functions
    function showLoadingSpinner(modalBody) {
        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    }

    function showNoDataMessage(modalBody, message) {
        modalBody.html(`<p class="text-center text-muted py-5">${message}</p>`);
    }

    function showErrorMessage(modalBody, message) {
        modalBody.html(`<p class="text-center text-danger py-5">${message}</p>`);
    }

    function renderInvoiceDetails(modalBody, studentDetails, invoiceDetails, media, status, invoiceId, labels) {
        const studentDetailsHtml = generateStudentDetailsHtml(studentDetails, labels);
        const invoiceDetailsHtml = generateInvoiceDetailsHtml(invoiceDetails, status);
        const paymentImagesHtml = generatePaymentImagesHtml(media, labels);
        const statusButtonsHtml = status !== 'accepted' ? generateStatusButtonsHtml(invoiceId, labels) : '';

        modalBody.html(`
            <div class="container-fluid">
                ${studentDetailsHtml}
                ${paymentImagesHtml}
                ${invoiceDetailsHtml}
                ${statusButtonsHtml}
            </div>
        `);
    }

    // HTML Generation Functions
    function generateStudentDetailsHtml(studentDetails, labels) {
        const details = ["name", "faculty", "building", "apartment", "room", "balance"];

        // Generate the student details cards dynamically
        const studentDetailsCards = details.map((key) => `
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body p-3">
                        <small class="text-muted text-uppercase fs-12">${labels[key]}</small>
                        <p class="mb-0 fw-semibold fs-6">${studentDetails[key] || "N/A"}</p>
                    </div>
                </div>
            </div>
        `).join("");

        return `
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="card-title mb-0 fs-5">${labels.studentDetails}</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        ${studentDetailsCards}
                    </div>
                </div>
            </div>
        `;
    }

    function generateInvoiceDetailsHtml(invoiceDetails, invoiceStatus) {
        const totalAmount = invoiceDetails.reduce((sum, detail) => sum + parseFloat(detail.amount), 0);
        const showOverpayment = invoiceStatus !== 'accepted';

        return `
            <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">${getTranslation('invoiceDetails',lang)}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>${getTranslation('category',lang)}</th>
                                    <th class="text-end">${getTranslation('amount',lang)}</th>
                                    <th class="text-end">${getTranslation('paid',lang)}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoiceDetails.map((detail) => `
                                    <tr>
                                        <td>${getTranslation(detail.category,lang)}</td>
                                        <td class="fs-5 text-end">${parseFloat(detail.amount).toLocaleString()}</td>
                                        <td class="fs-5 text-end">
                                            <input class="form-check-input border border-secondary invoice-detail-status" 
                                                   type="checkbox" 
                                                   value="${detail.id}"
                                                   ${detail.status == 'paid' ? 'checked disabled' : ''}
                                        </td>
                                    </tr>
                                `).join("")}
                            </tbody>
                        </table>
                    </div>
    
                    ${showOverpayment ? `
                        <!-- Add Reference Number input first -->
                        <div class="mt-3">
                            <label for="reference-number" class="form-label">الرقم المرجعي</label>
                            <input type="text" id="reference-number" class="form-control border border-secondary" 
                                   placeholder="أدخل الرقم المرجعي">
                        </div>
    
                        <div class="mt-3">
                            <label for="overpayment-amount" class="form-label">${getTranslation('overpaymentAmount',lang)}</label>
                            <input type="number" id="overpayment-amount" class="form-control border border-secondary" 
                                   placeholder="${getTranslation('enterOverpayment',lang)}">
                        </div>
    
                        <div class="mt-3">
                            <label for="admin-notes" class="form-label">${getTranslation('adminNotes',lang)}</label>
                            <textarea id="admin-notes" class="form-control border border-secondary" rows="3" 
                                placeholder="${getTranslation('addNotes',lang)}"></textarea>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }

    function generatePaymentImagesHtml(media, labels) {
        if (media.length === 0) return `<p class="text-center text-muted py-3">${labels.noDetails}</p>`;

        return `
            <div class="card mb-4 shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">${labels.uploadedPictures}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        ${media.map((doc) => `
                            <div class="col-md-4">
                                <div class="card border-0 shadow-sm">
                                    <img src="${doc.payment_url}" alt="Payment" class="card-img-top" style="height: 150px; object-fit: cover;" />
                                    <div class="card-body text-center">
                                        <a href="${doc.payment_url}" target="_blank" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fa fa-eye"></i> ${labels.view}
                                        </a>
                                        <a href="${doc.payment_url}" download class="btn btn-sm btn-outline-success">
                                            <i class="fa fa-download"></i> ${labels.download}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `).join("")}
                    </div>
                </div>
            </div>
        `;
    }

    function generateStatusButtonsHtml(invoiceId, labels) {
        return `
            <div class="d-flex justify-content-center gap-3 mt-4">
                <button id="accept-btn" class="btn btn-success" data-invoice-id="${invoiceId}" data-status="accepted">
                    <i class="fa fa-check-circle me-2"></i> ${labels.accept}
                </button>
                <button id="reject-btn" class="btn btn-danger" data-invoice-id="${invoiceId}" data-status="rejected">
                    <i class="fa fa-times-circle me-2"></i> ${labels.reject}
                </button>
            </div>
        `;
    }

    // Event handlers
    function attachEventListeners(response, labels) {
        // Update checkbox handling
        $('.invoice-detail-status').on('change', function() {
            const detailId = $(this).val();
            if ($(this).is(':checked')) {
                if (!currentInvoiceDetails.paidDetails.includes(detailId)) {
                    currentInvoiceDetails.paidDetails.push(detailId);
                }
            } else {
                currentInvoiceDetails.paidDetails = currentInvoiceDetails.paidDetails.filter(id => id !== detailId);
            }
        });

        // Pre-check any existing paid or pending details
        response.invoiceDetails.forEach(detail => {
            if (detail.is_paid || detail.pending) {
                currentInvoiceDetails.paidDetails.push(detail.id);
            }
        });

        // Add event listeners for accept and reject buttons
        $('#accept-btn').on('click', function() {
            handleAcceptButtonClick(response, labels);
        });

        $('#reject-btn').on('click', function() {
            handleRejectButtonClick(response, labels);
        });
    }

    function handleRejectButtonClick(response, labels) {
        swal({
            title: getTranslation('warning', lang),
            text: getTranslation('areYouSureReject', lang),
            type: "warning",
            showCancelButton: true,
            confirmButtonText: labels.reject,
            cancelButtonText: getTranslation('cancel', lang),
        }).then(() => {
            updatePaymentStatus(response.invoice_id, "rejected", null);

        });
    }

    function handleAcceptButtonClick(response, labels) {
        const overPaymentAmount = parseFloat($("#overpayment-amount").val()) || 0;

        const referenceNumber = $("#reference-number").val();

        const unpaidDetails = response.invoiceDetails.filter(d => !d.is_paid);

        // Validate that all required payments are selected
        if (currentInvoiceDetails.paidDetails.length === 0 && overPaymentAmount === 0) {
            swal({
                type: "warning",
                title: getTranslation('warning', lang),
                text: getTranslation('selectPayment', lang),
            });
            return;
        }

        // Should be:
        if (!referenceNumber || referenceNumber.trim() === "") {
            swal({
                icon: "warning",
                title: getTranslation('warning', lang),
                text: getTranslation('enterReferenceNumber', lang),
            });
            return;
        }


        // Validate overpayment amount
        if (overPaymentAmount < 0) {
            swal({
                type: "warning",
                title: getTranslation('warning', lang),
                text: getTranslation('negativeAmount', lang),
            });
            return;
        }

        swal({
            title: getTranslation('warning', lang),
            text: getTranslation('areYouSureAccept', lang),
            type: "warning",
            showCancelButton: true,
            confirmButtonText: labels.accept,
            cancelButtonText: getTranslation('cancel', lang),
        }).then(() => {
            updatePaymentStatus(
                currentInvoiceDetails.id,
                "accepted",
                currentInvoiceDetails.paidDetails,
                overPaymentAmount,
                referenceNumber
            );
        });
    }

    function updatePaymentStatus(invoiceId, status, paidDetails = null, overPaymentAmount = 0, referenceNumber = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        const notes = $("#admin-notes").val();

        const data = {
            _token: csrfToken,
            status: status,
            overPaymentAmount: overPaymentAmount,
            referenceNumber: referenceNumber,
            notes: notes,

        };

        if (status === "accepted" && paidDetails) {
            data.paidDetails = paidDetails;
        }

        $.ajax({
            url: window.routes.updateInvoiceStatus.replace(":paymentId", invoiceId),
            method: "POST",
            data: data,
            success: function(response) {
                swal({
                    type: "success",
                    title: getTranslation('success', lang),
                    text: response.message,
                }).then(() => {
                    showInvoiceDetails(invoiceId);
                    table.ajax.reload();

                });
            },
            error: function(xhr) {
                swal({
                    type: "error",
                    title: getTranslation('error', lang),
                    text: getTranslation('errorUpdating', lang) + xhr.responseJSON.error,
                });
            },
        });
    }


    // Initialize
    fetchStats();

    // Event bindings
    $(document).on("click", "#details-btn", function() {
        const paymentId = $(this).data("invoice-id");
        showInvoiceDetails(paymentId);
    });

    // Collapse functionality to show and hide search & filters
    const toggleButton = document.getElementById("toggleButton");

    if (toggleButton) {
        const icon = toggleButton.querySelector("i");
        document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
            icon.classList.remove("fa-search-plus");
            icon.classList.add("fa-search-minus");
        });

        document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
            icon.classList.remove("fa-search-minus");
            icon.classList.add("fa-search-plus");
        });
    }

});