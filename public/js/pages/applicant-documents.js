$(document).ready(function() {

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
        exportFile(downloadBtn, window.routes.exportExcel, 'applicants-documents.xlsx');
        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    function showDocuments(applicantId) {
        // AJAX request to fetch applicant documents
        $.ajax({
            url: window.routes.getApplicantDocuments.replace(':id', applicantId),
            method: 'GET',
            beforeSend: function () {
                $('#applicantDetailsModal .modal-body').html('<p>Loading pictures...</p>');
            },
            success: function (data) {
                if (data.documents && data.documents.length > 0) {
                    // Group documents by reservation ID
                    const groupedDocuments = data.documents.reduce((acc, doc) => {
                        if (!acc[doc.reservation_id]) {
                            acc[doc.reservation_id] = [];
                        }
                        acc[doc.reservation_id].push(doc);
                        return acc;
                    }, {});
    
                    // Build the HTML content
                    let groupedContent = '<div>';
                    for (const [reservationId, docs] of Object.entries(groupedDocuments)) {
                        groupedContent += `
                            <div style="margin-bottom: 20px; text-align: center;">
                                <h5>Reservation ID: ${reservationId}</h5>
                                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 10px;">
                        `;
    
                        docs.forEach(doc => {
                            const imagePath = `{{ asset('storage') }}/${doc.document_path}`;
                            groupedContent += `
                                <div style="margin: 5px;">
                                    <img src="${imagePath}" alt="Document for Reservation ${reservationId}" style="max-width: 200px; height: auto;" />
                                </div>
                            `;
                        });
    
                        groupedContent += `
                                </div>
                            </div>
                        `;
                    }
                    groupedContent += '</div>';
    
                    $('#applicantDetailsModal .modal-body').html(groupedContent);
                } else {
                    $('#applicantDetailsModal .modal-body').html('<p>No pictures available for this applicant.</p>');
                }
            },
            error: function (error) {
                console.error(error);
                $('#applicantDetailsModal .modal-body').html('<p>Failed to load pictures.</p>');
            }
        });
    
        $('#applicantDetailsModal').modal('show');
    }
    
    

    $(document).on('click', '#details-btn', function() {
        const applicantId = $(this).data('applicant-id');
        showDocuments(applicantId);
    });
});
