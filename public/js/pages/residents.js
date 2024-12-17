$(document).ready(function() {
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");

    // Toggle icon when collapse is shown
    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    // Toggle icon when collapse is hidden
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
            { data: 'location', name: 'location', searchable: true },
            { data: 'faculty', name: 'faculty', searchable: true },
            { data: 'mobile', name: 'mobile', searchable: true },
            { data: 'registration_date', name: 'registration_date', searchable: false },
        ]
    });

    $('#searchBox').on('keyup', function() {
        table.ajax.reload();
    });

    fetchSummaryData();

    // Fetch summary data for residents
    function fetchSummaryData() {
        $.ajax({
            url: window.routes.getSummary,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    $('#totalResidents').text('Error');
                    $('#totalMaleCount').text('Error');
                    $('#totalFemaleCount').text('Error');
                    alert('Failed to load data');
                } else {
                    $('#totalResidents').text(data.totalResidents);
                    $('#totalMaleCount').text(data.totalMaleCount);
                    $('#totalFemaleCount').text(data.totalFemaleCount);
                }
            },
            error: function() {
                $('#totalResidents').text('Error');
                $('#totalMaleCount').text('Error');
                $('#totalFemaleCount').text('Error');
                alert('Error while fetching data');
            }
        });
    }

    // Toggle loading state on button
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

    // Export file (Excel/PDF)
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

    // Show more details of the selected resident
    function showMoreDetails(residentId) {
        const url = window.routes.getResidentMoreDetails.replace(':id', residentId);

        toggleButtonLoading($('#details-btn'), true);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                populateResidentDetails(response.data);
                $('#details-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Details fetch error:', error);
                alert('Failed to fetch resident details. Please try again.');
            },
            complete: function() {
                toggleButtonLoading($('#details-btn'), false);
            }
        });
    }

    // Populate resident details into the modal form
    function populateResidentDetails(data) {
        $('#faculty').val(data.faculty);
        $('#program').val(data.program);
        $('#score').val(data.score);
        $('#percent').val(data.percent);
        $('#governorate').val(data.governorate);
        $('#city').val(data.city);
        $('#street').val(data.street);

        $('#residentDetailsModal').modal('show');
    }

    // Export to Excel when button is clicked
    $('#exportExcel').off('click').on('click', function(e) {
        e.preventDefault();

        const downloadBtn = $('#downloadBtn');
        exportFile(downloadBtn, window.routes.exportExcel, 'residents.xlsx');
        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    // Export to PDF when button is clicked
    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        exportFile($('#downloadBtn'), window.routes.exportPdf, 'residents.pdf');
    });

    // Show details when "Details" button is clicked
    $(document).on('click', '#details-btn', function() {
        const residentId = $(this).data('resident-id');
        showMoreDetails(residentId);
    });
});
