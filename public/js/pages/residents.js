$(document).ready(function() {
    // Function to toggle the icon when the collapse is shown or hidden
    function toggleIconOnCollapse() {
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
    }
    toggleIconOnCollapse();

    // Initialize DataTable with server-side processing
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        responsive: true,
        ajax: {
            url: window.routes.fetchResidents,
            data: function(d) {
                d.customSearch = $('#searchBox').val();
                d.building_number = $('#buildingFilter').val();
                d.apartment_number = $('#apartmentFilter').val();
                return d;
            },
            error: function(xhr, error, thrown) {
                console.error('Error fetching data:', error);
                alert('Failed to fetch data. Please try again.');
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'national_id', name: 'national_id' },
            { data: 'location', name: 'location' },
            { data: 'faculty', name: 'faculty' },
            { data: 'mobile', name: 'mobile' }
        ]
    });

    // Function to set up event listeners for filters
    function setupFilterListeners() {
        $('#searchBox').on('keyup', function() {
            table.ajax.reload();
        });


        $('#buildingFilter').on('change', function() {
            resetApartmentFilter();
            table.ajax.reload();
        });

        $('#apartmentFilter').on('change', function() {
            table.ajax.reload();
        });
    }
    setupFilterListeners();

    // Function to reset the apartment filter
    function resetApartmentFilter() {
        $('#apartmentFilter').val('');
    }

    // Function to fetch and display summary data for residents
    function fetchSummaryData() {
        $.ajax({
            url: window.routes.getSummary,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.error) {
                    handleSummaryError();
                } else {
                    updateSummaryData(data);
                }
            },
            error: function() {
                handleSummaryError();
            }
        });
    }

    // Function to handle errors in summary data fetching
    function handleSummaryError() {
        $('#totalResidents').text('Error');
        $('#totalMaleCount').text('Error');
        $('#totalFemaleCount').text('Error');
        $('#lastUpdateOverall').text('Error');
        $('#lastUpdateMaleResidents').text('Error');
        $('#lastUpdateFemaleResidents').text('Error');
        alert('Error while fetching data');
    }

    // Function to update the summary data in the UI
    function updateSummaryData(data) {
        $('#totalResidents').text(data.totalResidents);
        $('#totalMaleCount').text(data.totalMaleCount);
        $('#totalFemaleCount').text(data.totalFemaleCount);

        $('#lastUpdateOverall').text(data.lastUpdateOverall ? formatDate(data.lastUpdateOverall) : 'N/A');
        $('#lastUpdateMaleResidents').text(data.lastUpdateMaleResidents ? formatDate(data.lastUpdateMaleResidents) : 'N/A');
        $('#lastUpdateFemaleResidents').text(data.lastUpdateFemaleResidents ? formatDate(data.lastUpdateFemaleResidents) : 'N/A');
    }

    // Helper function to format the date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    fetchSummaryData();


});