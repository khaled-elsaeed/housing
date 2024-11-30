$(document).ready(function() {
    // Toggle button and icon for collapse functionality
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");

    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
        icon.classList.remove("fa-search-minus");
        icon.classList.add("fa-search-plus");
    });

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

    function exportFile(button, url, filename) {
        toggleButtonLoading(button, true);

        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }
            return response.blob();
        })
        .then(blob => {
            const downloadUrl = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.style.display = 'none';
            link.href = downloadUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(downloadUrl);
        })
        .catch(error => {
            console.error('Download error:', error);
            swal('Error!', 'Error downloading the file. Please try again later.', 'error');
        })
        .finally(() => {
            toggleButtonLoading(button, false);
        });
    }

    $('#exportExcel').off('click').on('click', function(e) {
        e.preventDefault();

        const downloadBtn = $('#downloadBtn');
        exportFile(downloadBtn, window.routes.exportExcel, 'applicants-payments.xlsx');
        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,
        ajax: {
            url: window.routes.fetchInvoices, // URL to your Laravel route
            data: function (d) {
                d.customSearch = $('#searchBox').val(); // Add custom search data
                d.gender = $('#genderFilter').val();    // Add gender filter data
            }
        },
        columns: [
            { data: 'name', name: 'student.name_en' },
            { data: 'national_id', name: 'student.national_id' },
            { data: 'faculty', name: 'student.faculty.name_en' },
            { data: 'mobile', name: 'student.mobile' },
            { data: 'invoice_status', name: 'invoice_status', searchable: true },
            { data: 'payment_status', name: 'payment_status', searchable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        drawCallback: function(settings) {
            // After table is drawn, you can handle enabling/disabling buttons dynamically if needed
            $('#default-datatable tbody').find('button').each(function() {
                const row = $(this).closest('tr');
                const invoiceStatus = row.find('td:eq(4)').text().trim(); // Get the invoice status (column index 4)
                const button = $(this);
    
                if (invoiceStatus === 'Paid') {
                    button.prop('disabled', false); // Enable button if invoice is paid
                } else {
                    button.prop('disabled', true); // Disable button if invoice is not paid
                }
            });
        }
    });
    
    // Custom search reload on keyup
    $('#searchBox').on('keyup', function() {
        table.ajax.reload();
    });
    
    // Reload the table when gender filter changes
    $('#genderFilter').on('change', function() {
        table.ajax.reload();
    });
    

    function fetchStats() {
        $.ajax({
            url: window.routes.fetchStats,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                // Total Invoices
                $('#totalInvoice').text(data.totalInvoice);
                $('#totalMaleInvoice').text(data.totalMaleInvoice);
                $('#totalFemaleInvoice').text(data.totalFemaleInvoice);
    
                // Paid Invoices
                $('#totalPaidInvoice').text(data.totalPaidInvoice);
                $('#totalPaidMaleInvoice').text(data.totalPaidMaleInvoice);
                $('#totalPaidFemaleInvoice').text(data.totalPaidFemaleInvoice);
    
                // Unpaid Invoices
                $('#totalUnpaidInvoice').text(data.totalUnpaidInvoice);
                $('#totalUnpaidMaleInvoice').text(data.totalUnpaidMaleInvoice);
                $('#totalUnpaidFemaleInvoice').text(data.totalUnpaidFemaleInvoice);
    
                // Accepted Payments
                $('#totalAcceptedPayments').text(data.totalAcceptedPayments);
                $('#totalAcceptedMalePayments').text(data.totalAcceptedMalePayments);
                $('#totalAcceptedFemalePayments').text(data.totalAcceptedFemalePayments);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching summary data:', error);
            }
        });
    }
    
    

    fetchStats();

    function showPayments(paymentId) {
        // AJAX request to fetch applicant payments and details
        $.ajax({
            url: window.routes.fetchInvoicePayment.replace(':id', paymentId),
            method: 'GET',
            beforeSend: function () {
                $('#applicantDetailsModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
            },
            success: function (data) {
                if (data.payments && data.payments.length > 0) {
                    const studentDetails = data.studentDetails || {};
                    const payments = data.payments;
    
                    // HTML for the first row (Student Details)
                    const studentDetailsHtml = `
                        <div class="card shadow-sm border-secondary mb-4">
                            <div class="card-body">
                                <h4 class="card-title text-primary">Student Details</h4>
                                <div class="d-flex flex-wrap gap-4">
                                    <div class="mb-1 p-2 border border-secondary d-flex align-items-center justify-content-center text-center" style="flex: 1;">
                                        <div><strong class="text-primary">Name:</strong><span class="text-secondary">${studentDetails.name || 'N/A'}</span></div>
                                    </div>
                                    <div class="mb-1 p-2 border border-secondary d-flex align-items-center justify-content-center text-center" style="flex: 1;">
                                        <div><strong class="text-primary">Faculty:</strong><span class="text-secondary">${studentDetails.faculty || 'N/A'}</span></div>
                                    </div>
                                    <div class="mb-1 p-2 border border-secondary d-flex align-items-center justify-content-center text-center" style="flex: 1;">
                                        <div><strong class="text-primary">Building:</strong><span class="text-secondary">${studentDetails.building || 'N/A'}</span></div>
                                    </div>
                                    <div class="mb-1 p-2 border border-secondary d-flex align-items-center justify-content-center text-center" style="flex: 1;">
                                        <div><strong class="text-primary">Apartment:</strong><span class="text-secondary">${studentDetails.apartment || 'N/A'}</span></div>
                                    </div>
                                    <div class="mb-1 p-2 border border-secondary d-flex align-items-center justify-content-center text-center" style="flex: 1;">
                                        <div><strong class="text-primary">Room:</strong><span class="text-secondary">${studentDetails.room || 'N/A'}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
    
                    // HTML for the second row (Payment Images without icons on images themselves)
                    const paymentImagesHtml = `
                        <div class="card shadow-sm border-primary">
                            <div class="card-body text-center">
                                <h4 class="card-title mb-3 text-primary">Uploaded Pictures</h4>
                                <div class="row g-3">
                                    ${payments.map(doc => {
                                        const imagePath = doc.payment_url;
                                        return `
                                        <div class="col-12 col-md-6">
                                            <div class="image-container">
                                                <img src="${imagePath}" alt="Payment" class="img-fluid border rounded shadow-sm w-100" style="height: 200px; object-fit: cover;" onclick="window.open('${imagePath}', '_blank')" />
                                            </div>
                                            <div class="d-flex justify-content-center mt-2">
                                                <a href="${imagePath}" target="_blank" class="btn btn-outline-primary me-2">View</a>
                                                <a href="${imagePath}" download class="btn btn-outline-success">Download</a>
                                            </div>
                                        </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    `;
    
                    // Add the status update buttons (Accept/Reject)
                    const statusButtonsHtml = `
                        <div class="d-flex justify-content-center mt-4">
                            <button id="accept-btn" class="btn btn-success me-3" data-payment-id="${paymentId}" data-status="accepted">Accept</button>
                            <button id="reject-btn" class="btn btn-danger" data-payment-id="${paymentId}" data-status="rejected">Reject</button>
                        </div>
                    `;
    
                    // Combine everything into modal content
                    const modalContent = `
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
                    `;
                    
                    $('#applicantDetailsModal .modal-body').html(modalContent);
                } else {
                    $('#applicantDetailsModal .modal-body').html('<p class="text-center text-muted">No details or pictures available for this applicant.</p>');
                }
            },
            error: function (error) {
                console.error(error);
                $('#applicantDetailsModal .modal-body').html('<p class="text-center text-danger">Failed to load details.</p>');
            }
        });
    
        $('#applicantDetailsModal').modal('show');
    }
    

   
    function updatePaymentStatus(paymentId, status) {
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
        $.ajax({
            url: window.routes.updateInvoiceStatus.replace(':paymentId', paymentId),  // Correct route
            method: 'POST',
            data: {
                _token: csrfToken,  // CSRF token from meta tag
                status: status
            },
            success: function(response) {
                alert(response.message);
    
                // Update the modal UI based on status
                if (status === 'accepted') {
                    $('#applicantDetailsModal .modal-body').prepend('<div class="alert alert-success">Payment Status: Accepted</div>');
                } else {
                    $('#applicantDetailsModal .modal-body').prepend('<div class="alert alert-danger">Payment Status: Rejected</div>');
                }
    
                // Disable buttons after status change
                $('#accept-btn').prop('disabled', true);
                $('#reject-btn').prop('disabled', true);
            },
            error: function(xhr) {
                alert('Error updating payment status: ' + xhr.responseJSON.error);
            }
        });
    }
    
    
    
    $(document).on('click', '#accept-btn', function() {
        const paymentId = $(this).data('payment-id');
        const status = $(this).data('status'); // "accepted"
        updatePaymentStatus(paymentId, status);
    });
    
    $(document).on('click', '#reject-btn', function() {
        const paymentId = $(this).data('payment-id');
        const status = $(this).data('status'); // "rejected"
        updatePaymentStatus(paymentId, status);
    });
    

    $(document).on('click', '#details-btn', function() {
        const paymentId = $(this).data('payment-id');
        showPayments(paymentId);
    });
});
