$(document).ready(function() {
    const translations = {
        en: {
            invoice_status: {
                paid: "Paid",
                unpaid: "Unpaid",
                pending: "Pending"
            },
            payment_status: {
                success: "Success",
                failed: "Failed",
                pending: "Pending"
            },
            studentDetails: "Student Details",
            name: "Name",
            faculty: "Faculty",
            building: "Building",
            apartment: "Apartment",
            room: "Room",
            uploadedPictures: "Uploaded Pictures",
            view: "View",
            download: "Download",
            accept: "Accept",
            reject: "Reject",
            noDetails: "No details or pictures available for this applicant.",
            loadingFailed: "Failed to load details.",
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
            areYouSureReject: "You are about to reject this invoice.",
            areYouSureAccept: "You are about to accept this invoice with the selected payments.",
            warning: "Warning",
            selectPayment: "Please select at least one payment detail to accept.",
            negativeAmount: "Overpayment amount cannot be negative.",
            success: "Success",
            error: "Error",
            errorUpdating: "Error updating payment status: "
        },
        ar: {
            invoice_status: {
                paid: "مدفوع",
                unpaid: "غير مدفوع",
                pending: "قيد الانتظار"
            },
            payment_status: {
                success: "ناجح",
                failed: "فشل",
                pending: "قيد الانتظار"
            },
            studentDetails: "تفاصيل الطالب",
            name: "الاسم",
            faculty: "الكلية",
            building: "المبنى",
            apartment: "الشقة",
            room: "الغرفة",
            uploadedPictures: "الصور المرفوعة",
            view: "عرض",
            download: "تحميل",
            accept: "قبول",
            reject: "رفض",
            noDetails: "لا توجد تفاصيل أو صور لهذا الطالب.",
            loadingFailed: "فشل في تحميل التفاصيل.",
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
            areYouSureReject: "أنت على وشك رفض هذه الفاتورة.",
            areYouSureAccept: "أنت على وشك قبول هذه الفاتورة مع المدفوعات المحددة.",
            warning: "تحذير",
            selectPayment: "يرجى تحديد تفاصيل دفع واحد على الأقل للقبول.",
            negativeAmount: "لا يمكن أن يكون مبلغ الدفع الزائد سالباً.",
            success: "نجاح",
            error: "خطأ",
            errorUpdating: "خطأ في تحديث حالة الدفع: "
        }
    };

    const lang = $("html").attr("lang") || "en";

    // Core utility functions
    function getTranslation(key) {
        const [category, status] = key.split(".");
        if (translations[lang] && translations[lang][category] && translations[lang][category][status]) {
            return translations[lang][category][status];
        }
        console.warn("Translation not found for key:", key);
        return key;
    }

    function getLabels() {
        return {
            studentDetails: getTranslation("studentDetails"),
            name: getTranslation("name"),
            faculty: getTranslation("faculty"),
            building: getTranslation("building"),
            apartment: getTranslation("apartment"),
            room: getTranslation("room"),
            balance: getTranslation('balance'),
            uploadedPictures: getTranslation("uploadedPictures"),
            view: getTranslation("view"),
            accept: getTranslation("accept"),
            reject: getTranslation("reject"),
            noDetails: getTranslation("noDetails"),
            loadingFailed: getTranslation("loadingFailed"),
            paidAmount: getTranslation("paidAmount"),
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
        columns: [{
                data: "name",
                name: "student.name_en"
            },
            {
                data: "national_id",
                name: "student.national_id"
            },
            {
                data: "faculty",
                name: "student.faculty.name_en"
            },
            {
                data: "mobile",
                name: "student.mobile"
            },
            {
                data: "invoice_status",
                name: "invoice_status",
                searchable: true,
                render: function(data) {
                    const translation = getTranslation("invoice_status." + data.toLowerCase());
                    return translation || data;
                }
            }, {
                data: "admin_approval",
                name: "admin_approval"
            },

            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false
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
        const labels = getLabels();

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
                    modalBody.html(`<p class="text-center text-muted py-5">${labels.noDetails}</p>`);
                    return;
                }

                const studentDetails = response.studentDetails || {};
                const invoiceDetails = response.invoiceDetails;
                const media = response.media || [];

                const studentDetailsHtml = generateStudentDetailsHtml(studentDetails, labels);
                const invoiceDetailsHtml = generateInvoiceDetailsHtml(invoiceDetails,response.status);
                const paymentImagesHtml = generatePaymentImagesHtml(media, labels);
                const statusButtonsHtml = generateStatusButtonsHtml(response.invoice_id, labels);

               
                if(response.status == 'accepted'){
                    modalBody.html(`
                        <div class="container-fluid">
                            ${studentDetailsHtml}
                            ${paymentImagesHtml}
                            ${invoiceDetailsHtml}
                        </div>
                    `);
                }else{
                    modalBody.html(`
                        <div class="container-fluid">
                            ${studentDetailsHtml}
                            ${paymentImagesHtml}
                            ${invoiceDetailsHtml}
                            ${statusButtonsHtml}
                        </div>
                    `);
                }

                attachEventListeners(response, labels);
            },
            error: function() {
                modalBody.html(`<p class="text-center text-danger py-5">${labels.loadingFailed}</p>`);
            },
        });

        if (!$("#applicantDetailsModal").hasClass("show")) {
            $("#applicantDetailsModal").modal("show");
        }

    }

    function showLoadingSpinner(modalBody) {
        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    }

    // HTML Generation functions
    function generateStudentDetailsHtml(studentDetails, labels) {
        return `
            <div class="card mb-4 shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">${labels.studentDetails}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        ${["name", "faculty", "building", "apartment", "room",'balance']
                            .map(
                                (key) => `
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted">${labels[key]}</small>
                                    <p class="mb-0 fw-bold">${studentDetails[key] || "N/A"}</p>
                                </div>
                            </div>
                        `
                            )
                            .join("")}
                    </div>
                </div>
            </div>
        `;
    }

    function generateInvoiceDetailsHtml(invoiceDetails, $invoiceStatus) {
        const totalAmount = invoiceDetails.reduce((sum, detail) => sum + parseFloat(detail.amount), 0);
        const showOverpayment = $invoiceStatus !== 'accepted';
    
        return `
            <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">${getTranslation('invoiceDetails')}</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>${getTranslation('category')}</th>
                                    <th class="text-end">${getTranslation('amount')}</th>
                                    <th class="text-end">${getTranslation('paid')}</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${invoiceDetails.map((detail) => `
                                    <tr>
                                        <td>${detail.category}</td>
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
                        <div class="mt-3">
                            <label for="overpayment-amount" class="form-label">${getTranslation('overpaymentAmount')}</label>
                            <input type="number" id="overpayment-amount" class="form-control border border-secondary" 
                                   placeholder="${getTranslation('enterOverpayment')}">
                        </div>

                        <div class="mt-3">
                            <label for="admin-notes" class="form-label">${getTranslation('adminNotes')}</label>
                            <textarea id="admin-notes" class="form-control border border-secondary" rows="3" 
                                placeholder="${getTranslation('addNotes')}"></textarea>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    

    function generatePaymentImagesHtml(media, labels) {
        if (media.length > 0) {
            return `
                <div class="card mb-4 shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">${labels.uploadedPictures}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            ${media
                                .map(
                                    (doc) => `
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
                            `
                                )
                                .join("")}
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `<p class="text-center text-muted py-3">${labels.noDetails}</p>`;
        }
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
            title: getTranslation('warning'),
            text: getTranslation('areYouSureReject'),
            type: "warning",
            showCancelButton: true,
            confirmButtonText: labels.reject,
            cancelButtonText: getTranslation('cancel'),
        }).then(() => {
            updatePaymentStatus(response.invoice_id, "rejected", null);

        });
    }

    function handleAcceptButtonClick(response, labels) {
        const overPaymentAmount = parseFloat($("#overpayment-amount").val()) || 0;
        const unpaidDetails = response.invoiceDetails.filter(d => !d.is_paid);

        // Validate that all required payments are selected
        if (currentInvoiceDetails.paidDetails.length === 0 && overPaymentAmount === 0) {
            swal({
                type: "warning",
                title: getTranslation('warning'),
                text: getTranslation('selectPayment'),
            });
            return;
        }

        // Validate overpayment amount
        if (overPaymentAmount < 0) {
            swal({
                type: "warning",
                title: getTranslation('warning'),
                text: getTranslation('negativeAmount'),
            });
            return;
        }

        swal({
            title: getTranslation('warning'),
            text: getTranslation('areYouSureAccept'),
            type: "warning",
            showCancelButton: true,
            confirmButtonText: labels.accept,
            cancelButtonText: getTranslation('cancel'),
        }).then(() => {
            updatePaymentStatus(
                currentInvoiceDetails.id, 
                "accepted", 
                currentInvoiceDetails.paidDetails,
                overPaymentAmount
            );
        });
    }

    function updatePaymentStatus(invoiceId, status, paidDetails = null, overPaymentAmount = 0) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        const notes = $("#admin-notes").val();
        
        const data = {
            _token: csrfToken,
            status: status,
            overPaymentAmount: overPaymentAmount,
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
                    title: getTranslation('success'),
                    text: response.message,
                }).then(() => {
                    showInvoiceDetails(invoiceId);
                    table.ajax.reload();
    
                });
            },
            error: function(xhr) {
                swal({
                    type: "error",
                    title: getTranslation('error'),
                    text: getTranslation('errorUpdating') + xhr.responseJSON.error,
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