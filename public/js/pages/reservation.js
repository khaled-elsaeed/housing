$(document).ready(function() {
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");

    // Handle the toggle button icon change when the search filter is shown/hidden
    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
        icon.classList.remove("fa-search-minus");
        icon.classList.add("fa-search-plus");
    });

    const isArabic = $("html").attr("dir") === "rtl";


    // Initialize DataTable
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: window.routes.fetchReservations,
            data: function(d) {
                d.customSearch = $('#searchBox').val();
            }
        },
        columns: [
            { data: 'reservation_id', name: 'reservation_id' },
            { data: 'name', name: 'name', searchable: true },
            { data: 'location', name: 'location', searchable: true },
            { data: 'start_date', name: 'start_date', searchable: true },
            { data: 'end_date', name: 'end_date', searchable: true },
            { 
                data: 'status', 
                name: 'status', 
                searchable: true, 
                render: function (data) {
                    // Normalize data to lowercase and fetch translation
                    return window.translations.status[data] || data; 
                },
            }
        ],language: isArabic
        ? {
              url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",

          }
        : {},
        
        
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
                $('#totalReservationsCount').text(data.totalReservations);
                $('#maleReservationsCount').text(data.totalMaleCount);
                $('#femaleReservationsCount').text(data.totalFemaleCount);

                $('#totalPendingReservationsCount').text(data.totalPendingCount);
                $('#malePendingReservationsCount').text(data.malePendingCount);
                $('#femalePendingReservationsCount').text(data.femalePendingCount);

                $('#totalConfirmedReservationsCount').text(data.totalConfirmedCount);
                $('#maleConfirmedReservationsCount').text(data.maleConfirmedCount);
                $('#femaleConfirmedReservationsCount').text(data.femaleConfirmedCount);

                $('#totalCancelledReservationsCount').text(data.totalCancelledCount);
                $('#maleCancelledReservationsCount').text(data.maleCancelledCount);
                $('#femaleCancelledReservationsCount').text(data.femaleCancelledCount);
            }
        });
    }

    fetchSummaryData();

    // Button loading state handling
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

    // Export to Excel function
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

    // Show more details of a reservation
    function showMoreDetails(reservationId) {
        toggleButtonLoading($('#details-btn'), true);
        $.ajax({
            url: window.routes.getReservationMoreDetails.replace(':id', reservationId),
            type: 'GET',
            success: function(response) {
                populateReservationDetails(response.data);
                $('#reservationDetailsModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Details fetch error:', error);
                alert('Failed to fetch reservation details. Please try again.');
            },
            complete: function() {
                toggleButtonLoading($('#details-btn'), false);
            }
        });
    }

    // Populate modal with reservation details
    function populateReservationDetails(data) {
        $('#room').val(data.room);
        $('#start_date').val(data.start_date);
        $('#end_date').val(data.end_date);
        $('#status').val(data.status);
    }

    // Export to Excel functionality
    $('#exportExcel').off('click').on('click', function(e) {
        e.preventDefault();
        const downloadBtn = $('#downloadBtn');
        exportFile(downloadBtn, window.routes.exportExcel, 'reservations.xlsx');
        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    // Show reservation details when "Show More" button is clicked
    $(document).on('click', '#details-btn', function() {
        const reservationId = $(this).data('reservation-id');
        showMoreDetails(reservationId);
    });
});
