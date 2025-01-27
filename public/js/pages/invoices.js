$(document).ready(function () {
    // Define translation dictionaries for English and Arabic
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
            loadingFailed: "Failed to load details."
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
            loadingFailed: "فشل في تحميل التفاصيل."
        }
    };

    // Get the current language from the HTML lang attribute
    const lang = $("html").attr("lang") || "en";  // Default to "en" if lang is not defined

    function getTranslation(key) {        
        const [category, status] = key.split("."); 
    
        // Check if category and status exist in the translations object for the current language
        if (translations[lang] && translations[lang][category] && translations[lang][category][status]) {
            return translations[lang][category][status];  // Return the correct translation
        }
    
        console.warn("Translation not found for key:", key);
        return key;  // Return the original key if no translation is found
    }
    
    

    // Toggle button and icon for collapse functionality
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
        const hasClassBtnRound = button.hasClass("btn-round");

        if (isLoading) {
            if (!button.data("original-text")) {
                button.data("original-text", button.html());
            }

            if (hasClassBtnRound) {
                button.html('<i class="fa fa-spinner fa-spin"></i>').addClass("loading").prop("disabled", true);
            } else {
                button.html('<i class="fa fa-spinner fa-spin"></i> Downloading...').addClass("loading").prop("disabled", true);
            }
        } else {
            button.html(button.data("original-text")).removeClass("loading").prop("disabled", false);
            button.removeData("original-text");
        }
    }

    function exportFile(button, url, filename) {
        toggleButtonLoading(button, true);

        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        fetch(url, {
            method: "GET",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-Token": csrfToken,
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok.");
                }
                return response.blob();
            })
            .then((blob) => {
                const downloadUrl = window.URL.createObjectURL(blob);
                const link = document.createElement("a");
                link.style.display = "none";
                link.href = downloadUrl;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(downloadUrl);
            })
            .catch((error) => {
                console.error("Download error:", error);
                swal("Error!", "Error downloading the file. Please try again later.", "error");
            })
            .finally(() => {
                toggleButtonLoading(button, false);
            });
    }

    $("#exportExcel")
        .off("click")
        .on("click", function (e) {
            e.preventDefault();

            const downloadBtn = $("#downloadBtn");
            exportFile(downloadBtn, window.routes.exportExcel, "applicants-payments.xlsx");
            $(downloadBtn).next(".dropdown-menu").removeClass("show");
        });

    

        const table = $("#default-datatable").DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ordering: false,
            ajax: {
                url: window.routes.fetchInvoices, // URL to your Laravel route
                data: function (d) {
                    d.customSearch = $("#searchBox").val();
                    d.gender = $("#genderFilter").val();
                },
            },
            columns: [
                { data: "name", name: "student.name_en" },
                { data: "national_id", name: "student.national_id" },
                { data: "faculty", name: "student.faculty.name_en" },
                { data: "mobile", name: "student.mobile" },
                {
                    data: "invoice_status",
                    name: "invoice_status",
                    searchable: true,
                    render: function (data) {
                        const translation = getTranslation("invoice_status." + data.toLowerCase());
                        return translation || data;
                    }
                },{data: "admin_approval",name:"admin_approval"},
            
                { data: "actions", name: "actions", orderable: false, searchable: false },
            ],
            language: lang === "ar"
                ? {
                    url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
                }
                : {},
        });

    $("#searchBox").on("keyup", function () {
        table.ajax.reload();
    });

    $("#genderFilter").on("change", function () {
        table.ajax.reload();
    });

    function fetchStats() {
        $.ajax({
            url: window.routes.fetchStats,
            method: "GET",
            dataType: "json",
            success: function (data) {
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
            error: function (xhr, status, error) {
                console.error("Error fetching summary data:", error);
            },
        });
    }

    fetchStats();

    function showInvoiceDetails(invoiceId) {
        const modalBody = $("#applicantDetailsModal .modal-body");
        const labels = {
            studentDetails: getTranslation("studentDetails"),
            name: getTranslation("name"),
            faculty: getTranslation("faculty"),
            building: getTranslation("building"),
            apartment: getTranslation("apartment"),
            room: getTranslation("room"),
            uploadedPictures: getTranslation("uploadedPictures"),
            view: getTranslation("view"),
            download: getTranslation("download"),
            accept: getTranslation("accept"),
            reject: getTranslation("reject"),
            noDetails: getTranslation("noDetails"),
            loadingFailed: getTranslation("loadingFailed"),
            paidAmount: getTranslation("paidAmount"),
        };
    
        // Show loading spinner
        modalBody.html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `);
    
        // Fetch invoice details
        $.ajax({
            url: window.routes.fetchInvoiceDetails.replace(":id", invoiceId),
            method: "GET",
            success: function (data) {
                if (!data.invoiceDetails || data.invoiceDetails.length === 0) {
                    modalBody.html(`<p class="text-center text-muted py-5">${labels.noDetails}</p>`);
                    return;
                }
    
                const studentDetails = data.studentDetails || {};
                const invoiceDetails = data.invoiceDetails;
                const media = data.media || [];
    
                // Generate student details card
                const studentDetailsHtml = `
                    <div class="card mb-4 shadow-sm border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">${labels.studentDetails}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                ${["name", "faculty", "building", "apartment", "room"]
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
    
                // Generate invoice details card
                const invoiceDetailsHtml = `
                    <div class="card mb-4 shadow-sm border-secondary">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">Invoice Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${invoiceDetails
                                            .map(
                                                (detail) => `
                                            <tr>
                                                <td>${detail.category}</td>
                                                <td class="fs-5 text-end">${parseFloat(detail.amount).toLocaleString()}</td>
                                            </tr>
                                        `
                                            )
                                            .join("")}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
    
                // Generate payment images card
                let paymentImagesHtml = '';
                if (media.length > 0) {
                    paymentImagesHtml = `
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
                    paymentImagesHtml = `<p class="text-center text-muted py-3">${labels.noDetails}</p>`;
                }
    
                // Generate amount paid input card
                const amountPaidHtml = `
                    <div class="card mb-4 shadow-sm border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">${labels.paidAmount}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="amountPaid" class="form-label">${labels.paidAmount}</label>
                                    <input type="text" class="form-control border border-primary" id="amountPaid" placeholder="Enter amount (e.g., 1,000.00)" />
                                    <small class="text-muted">Enter the amount paid with up to 2 decimal places.</small>
                                    <div class="invalid-feedback">Please enter a valid amount (e.g., 1,000.00).</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
    
                // Generate accept/reject buttons
                const statusButtonsHtml = `
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button id="accept-btn" class="btn btn-success" data-invoice-id="${data.invoice_id}" data-status="accepted">
                            <i class="fa fa-check-circle me-2"></i> ${labels.accept}
                        </button>
                        <button id="reject-btn" class="btn btn-danger" data-invoice-id="${data.invoice_id}" data-status="rejected">
                            <i class="fa fa-times-circle me-2"></i> ${labels.reject}
                        </button>
                    </div>
                `;
    
                // Combine all sections into the modal body
                modalBody.html(`
                    <div class="container-fluid">
                        ${studentDetailsHtml}
                        ${invoiceDetailsHtml}
                        ${paymentImagesHtml}
                        ${amountPaidHtml}
                        ${statusButtonsHtml}
                    </div>
                `);
    
                // Function to format input with commas
                function formatAmountInput(input) {
                    // Remove non-numeric characters except decimal point
                    let value = input.replace(/[^0-9.]/g, "");
    
                    // Split into whole and decimal parts
                    let parts = value.split(".");
                    let wholePart = parts[0];
                    let decimalPart = parts.length > 1 ? "." + parts[1] : "";
    
                    // Add commas as thousand separators
                    wholePart = wholePart.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
                    // Combine and update the input value
                    input = wholePart + decimalPart;
                    return input;
                }
    
                // Validate and format amount paid input
                $("#amountPaid").on("input", function () {
                    const input = $(this);
                    let value = input.val();
    
                    // Format the input with commas
                    value = formatAmountInput(value);
                    input.val(value);
    
                    // Validate the amount
                    const regex = /^\d{1,3}(,\d{3})*(\.\d{1,2})?$/; // Allows commas and up to 2 decimal places
                    if (regex.test(value)) {
                        input.removeClass("is-invalid").addClass("is-valid");
                    } else {
                        input.removeClass("is-valid").addClass("is-invalid");
                    }
                });
    
                // Add event listener for the "Accept" button
                $("#accept-btn").on("click", function () {
                    const amountPaid = $("#amountPaid").val().replace(/,/g, ""); // Remove commas for validation
                    if (!amountPaid || !/^\d+(\.\d{1,2})?$/.test(amountPaid)) {
                        $("#amountPaid").addClass("is-invalid");
                        swal({
                            type: "error",
                            title: "Invalid Amount",
                            text: "Please enter a valid amount with up to 2 decimal places.",
                        });
                        return;
                    }
    
                    swal({
                        title: "Are you sure?",
                        text: "You are about to accept this invoice.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: labels.accept,
                        cancelButtonText: "Cancel",
                    }).then(() => {
                            updatePaymentStatus(data.invoice_id, "accepted", parseFloat(amountPaid).toFixed(2));
                        
                    });
                });
    
                // Add event listener for the "Reject" button
                $("#reject-btn").on("click", function () {
                    swal({
                        title: "Are you sure?",
                        text: "You are about to reject this invoice.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: labels.reject,
                        cancelButtonText: "Cancel",
                    }).then(() => {
                            updatePaymentStatus(data.invoice_id, "rejected");
                        
                    });
                });
            },
            error: function () {
                modalBody.html(`<p class="text-center text-danger py-5">${labels.loadingFailed}</p>`);
            },
        });
    
        // Show the modal
        $("#applicantDetailsModal").modal("show");
    }
    
    function updatePaymentStatus(paymentId, status, paidAmount = null) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
    
        $.ajax({
            url: window.routes.updateInvoiceStatus.replace(":paymentId", paymentId),
            method: "POST",
            data: {
                _token: csrfToken,
                status: status,
                paid_amount: paidAmount,
            },
            success: function (response) {
                swal({
                    type: "success",
                    title: "Success",
                    text: response.message,
                }).then(() => {
                    if (status === "accepted") {
                        $("#applicantDetailsModal .modal-body").prepend('<div class="alert alert-success">Payment Status: Accepted</div>');
                    } else {
                        $("#applicantDetailsModal .modal-body").prepend('<div class="alert alert-danger">Payment Status: Rejected</div>');
                    }
                    $("#accept-btn").prop("disabled", true);
                    $("#reject-btn").prop("disabled", true);
                    window.location.reload();
                });
            },
            error: function (xhr) {
                swal({
                    type: "error",
                    title: "Error",
                    text: "Error updating payment status: " + xhr.responseJSON.error,
                });
            },
        });
    }

    $(document).on("click", "#details-btn", function () {
        const paymentId = $(this).data("invoice-id");
        showInvoiceDetails(paymentId);
    });
});
