$(document).ready(function() {
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");
    const lang = $("html").attr("lang") || "en";  // Default to "en" if lang is not defined


    // Handle the toggle button icon change when the search filter is shown/hidden
    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
        icon.classList.remove("fa-search-minus");
        icon.classList.add("fa-search-plus");
    });

  

    

    // Initialize DataTable with dynamic language support
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering:false,
        ajax: {
            url: window.routes.fetchReservations,
            data: function(d) {
                d.customSearch = $('#searchBox').val();
            }
        },
        columns: [
            { data: 'name', name: 'name', searchable: true },
            {data:'national_id',name:'national_id'},
            { data: 'location', name: 'location', searchable: true },
            { data: 'period', name: 'period', searchable: true },
            { data: 'duration', name: 'duration', searchable: true },
            { data: 'status', name: 'status', searchable: true}
        ],
        language: lang === "ar"
        ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        }
        : {},
    });

    // Language-specific functionality (search box, etc.)
    $('#searchBox').on('keyup', function() {
        table.ajax.reload();
    });

    // Function to fetch and display summary data
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

    // Fetch and display summary data on page load
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
                button.html('<i class="fa fa-spinner fa-spin"></i> ' + getTranslation("exporting"))
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

    
});
