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
                {
                    data: "payment_status",
                    name: "payment_status",
                    searchable: true,
                    render: function (data) {
                        return getTranslation("payment_status." + data) || data;
                    },
                },
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

    // Modify modal and other UI components similarly
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
        };
    
        const textAlignClass = lang === "ar" ? "text-end" : "text-start";
        const justifyClass = lang === "ar" ? "justify-content-end" : "justify-content-start";
    
        // Show loading spinner
        modalBody.html(
            `<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>`
        );
    
        // Fetch payment details
        $.ajax({
            url: window.routes.fetchInvoiceDetails.replace(":id", invoiceId),
            method: "GET",
            success: function (data) {
                if (!data.invoiceDetails || data.invoiceDetails.length === 0) {
                    modalBody.html(`<p class="text-center text-muted">${labels.noDetails}</p>`);
                    return;
                }
    
                const studentDetails = data.studentDetails || {};
                const invoiceDetails = data.invoiceDetails;
                const media = data.media || [];
    
                // Generate student details card
                const studentDetailsHtml = `
                    <div class="card shadow-sm border-secondary mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary ${textAlignClass}">${labels.studentDetails}</h4>
                            <div class="d-flex flex-wrap gap-4 ${justifyClass}">
                                ${["name", "faculty", "building", "apartment", "room"]
                                    .map(
                                        (key) => `
                                        <div class="p-2 border border-secondary text-center flex-grow-1">
                                            <strong class="text-primary">${labels[key]}:</strong>
                                            <span class="text-primary">${studentDetails[key] || "N/A"}</span>
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
                    <div class="card shadow-sm border-primary mb-4">
                        <div class="card-body">
                            <h4 class="card-title text-primary ${textAlignClass}">${labels.invoiceDetails}</h4>
                            <ul class="list-group">
                                ${invoiceDetails
                                    .map(
                                        (detail) => `
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span><strong>${detail.category}:</strong> ${detail.description || "N/A"}</span>
                                            <span class="text-primary">${detail.amount}</span>
                                        </li>
                                    `
                                    )
                                    .join("")}
                            </ul>
                        </div>
                    </div>
                `;
    
                // Generate payment images card
                let paymentImagesHtml = '';
                if (media.length > 0) {
                    paymentImagesHtml = `
                        <div class="card shadow-sm border-primary">
                            <div class="card-body">
                                <h4 class="card-title text-primary ${textAlignClass}">${labels.uploadedPictures}</h4>
                                <div class="row g-3">
                                    ${media
                                        .map(
                                            (doc) => `
                                        <div class="col-12 col-md-6">
                                            <div class="image-container">
                                                <img src="${doc.payment_url}" alt="Payment" class="img-fluid border rounded shadow-sm w-100" style="height: 200px; object-fit: cover;" onclick="window.open('${doc.payment_url}', '_blank')" />
                                            </div>
                                            <div class="d-flex ${justifyClass} mt-2">
                                                <a href="${doc.payment_url}" target="_blank" class="btn btn-outline-primary me-2">${labels.view}</a>
                                                <a href="${doc.payment_url}" download class="btn btn-outline-success">${labels.download}</a>
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
                    paymentImagesHtml = `<p class="text-center text-muted">${labels.noDetails}</p>`;
                }
    
                // Generate accept/reject buttons (assuming you will add logic for the invoice status)
                const statusButtonsHtml = `
                    <div class="d-flex justify-content-center mt-4">
                        <button id="accept-btn" class="btn btn-success me-3" data-invoice-id="${data.invoice_id}" data-status="accepted">${labels.accept}</button>
                        <button id="reject-btn" class="btn btn-danger" data-invoice-id="${data.invoice_id}" data-status="rejected">${labels.reject}</button>
                    </div>
                `;
    
                // Combine all sections into the modal body
                modalBody.html(`
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">${studentDetailsHtml}</div>
                        </div>
                        <div class="row">
                            <div class="col-12">${invoiceDetailsHtml}</div>
                        </div>
                        <div class="row">
                            <div class="col-12">${paymentImagesHtml}</div>
                        </div>
                        <div class="row">
                            <div class="col-12">${statusButtonsHtml}</div>
                        </div>
                    </div>
                `);
            },
            error: function () {
                modalBody.html(`<p class="text-center text-danger">${labels.loadingFailed}</p>`);
            },
        });
    
        // Show the modal
        $("#applicantDetailsModal").modal("show");
    }
    
    

    function updatePaymentStatus(paymentId, status) {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        $.ajax({
            url: window.routes.updateInvoiceStatus.replace(":paymentId", paymentId), // Correct route
            method: "POST",
            data: {
                _token: csrfToken, // CSRF token from meta tag
                status: status,
            },
            success: function (response) {
                alert(response.message);

                // Update the modal UI based on status
                if (status === "accepted") {
                    $("#applicantDetailsModal .modal-body").prepend('<div class="alert alert-success">Payment Status: Accepted</div>');
                } else {
                    $("#applicantDetailsModal .modal-body").prepend('<div class="alert alert-danger">Payment Status: Rejected</div>');
                }

                // Disable buttons after status change
                $("#accept-btn").prop("disabled", true);
                $("#reject-btn").prop("disabled", true);
                window.location.reload();
            },
            error: function (xhr) {
                alert("Error updating payment status: " + xhr.responseJSON.error);
            },
        });
    }

    $(document).on("click", "#accept-btn", function () {
        const paymentId = $(this).data("payment-id");
        const status = $(this).data("status"); // "accepted"
        updatePaymentStatus(paymentId, status);
    });

    $(document).on("click", "#reject-btn", function () {
        const paymentId = $(this).data("payment-id");
        const status = $(this).data("status"); // "rejected"
        updatePaymentStatus(paymentId, status);
    });

    $(document).on("click", "#details-btn", function () {
        const paymentId = $(this).data("invoice-id");
        showInvoiceDetails(paymentId);
    });
});
