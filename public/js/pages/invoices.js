$(document).ready(function () {
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

    const isArabic = $("html").attr("dir") === "rtl";

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
                    var normalizedData = data.toLowerCase();
                    return window.translations.invoice_status[normalizedData] || data;
                },
            },
            {
                data: "payment_status",
                name: "payment_status",
                searchable: true,
                render: function (data) {
                    return window.translations.payment_status[data] || data; // Translate payment status
                },
            },
            { data: "actions", name: "actions", orderable: false, searchable: false },
        ],
        language: isArabic
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

    function showPayments(paymentId) {
        const modalBody = $("#applicantDetailsModal .modal-body");
        const isRTL = $("html").attr("dir") === "rtl";
        const labels = isRTL
            ? {
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
              }
            : {
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
              };
    
        const textAlignClass = isRTL ? "text-end" : "text-start";
        const justifyClass = isRTL ? "justify-content-end" : "justify-content-start";
    
        // Show loading spinner
        modalBody.html(
            `<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>`
        );
    
        // Fetch payment details
        $.ajax({
            url: window.routes.fetchInvoicePayment.replace(":id", paymentId),
            method: "GET",
            success: function (data) {
                if (!data.payments || data.payments.length === 0) {
                    modalBody.html(`<p class="text-center text-muted">${labels.noDetails}</p>`);
                    return;
                }
    
                const studentDetails = data.studentDetails || {};
                const payments = data.payments;
    
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
    
                // Generate payment images card
                const paymentImagesHtml = `
                    <div class="card shadow-sm border-primary">
                        <div class="card-body">
                            <h4 class="card-title text-primary ${textAlignClass}">${labels.uploadedPictures}</h4>
                            <div class="row g-3">
                                ${payments
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
    
                // Generate accept/reject buttons
                const statusButtonsHtml = `
                    <div class="d-flex justify-content-center mt-4">
                        <button id="accept-btn" class="btn btn-success me-3" data-payment-id="${paymentId}" data-status="accepted">${labels.accept}</button>
                        <button id="reject-btn" class="btn btn-danger" data-payment-id="${paymentId}" data-status="rejected">${labels.reject}</button>
                    </div>
                `;
    
                // Combine all sections into the modal body
                modalBody.html(`
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">${studentDetailsHtml}</div>
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
        const paymentId = $(this).data("payment-id");
        showPayments(paymentId);
    });
});
