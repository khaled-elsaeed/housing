$(document).ready(function() {

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

    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: window.routes.fetchResidents,
            data: function (d) {
                d.customSearch = $('#searchBox').val();
            }
        },
        columns: [
            { data: 'name', name: 'name', searchable: true },
            { data: 'national_id', name: 'national_id', searchable: true },
            { data: 'faculty', name: 'faculty', searchable: true },
            { data: 'email', name: 'email', searchable: true },
            { data: 'mobile', name: 'mobile', searchable: true },
            { data: 'registration_date', name: 'registration_date', searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]
    });

    $('#searchBox').on('keyup', function() {
        table.ajax.reload();
    });

    function fetchSummaryData() {
        $.ajax({
            url: window.routes.getSummary,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalApplicantsCount').text(data.totalApplicants);
                $('#maleCount').text(data.totalMaleCount);
                $('#femaleCount').text(data.totalFemaleCount);
                $('#totalPendingCount').text(data.totalPendingCount);
                $('#malePendingCount').text(data.malePendingCount);
                $('#femalePendingCount').text(data.femalePendingCount);
                $('#totalPreliminaryAcceptedCount').text(data.totalPreliminaryAcceptedCount);
                $('#malePreliminaryAcceptedCount').text(data.malePreliminaryAcceptedCount);
                $('#femalePreliminaryAcceptedCount').text(data.femalePreliminaryAcceptedCount);
                $('#totalFinalAcceptedCount').text(data.totalFinalAcceptedCount);
                $('#maleFinalAcceptedCount').text(data.maleFinalAcceptedCount);
                $('#femaleFinalAcceptedCount').text(data.femaleFinalAcceptedCount);
            }
        });
    }

    fetchSummaryData();

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

    function showMoreDetails(applicantId) {
        toggleButtonLoading($('#details-btn'), true);
        $.ajax({
            url: window.routes.getApplicantMoreDetails.replace(':id', applicantId),
            type: 'GET',
            success: function(response) {
                populateApplicantDetails(response.data);
                $('#details-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Details fetch error:', error);
                alert('Failed to fetch applicant details. Please try again.');
            },
            complete: function() {
                toggleButtonLoading($('#details-btn'), false);
            }
        });
    }

    function populateApplicantDetails(data) {
        $('#faculty').val(data.faculty);
        $('#program').val(data.program);
        $('#score').val(data.score);
        $('#percent').val(data.percent);
        $('#governorate').val(data.governorate);
        $('#city').val(data.city);
        $('#street').val(data.street);
        $('#applicantDetailsModal').modal('show');
    }

    $('#exportExcel').off('click').on('click', function(e) {
        e.preventDefault();
        const downloadBtn = $('#downloadBtn');
        exportFile(downloadBtn, window.routes.exportExcel, 'applicants.xlsx');
        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        exportFile($('#downloadBtn'), window.routes.exportPdf, 'applicants.pdf');
    });

    $(document).on('click', '#details-btn', function() {
        const applicantId = $(this).data('applicant-id');
        showMoreDetails(applicantId);
    });
});
