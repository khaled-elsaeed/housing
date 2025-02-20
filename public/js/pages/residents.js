$(document).ready(function () {
    const lang = $("html").attr("lang") || "en"; // Get the current language (default to 'en')

    // Function to toggle the icon when the collapse is shown or hidden
    function toggleIconOnCollapse() {
        const toggleButton = document.getElementById("toggleButton");
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
    toggleIconOnCollapse();

    // Initialize DataTable with server-side processing
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        responsive: true,
        ajax: {
            url: window.routes.fetchResidents,
            data: function (d) {
                d.customSearch = $('#searchBox').val();
                d.building_number = $('#buildingFilter').val();
                d.apartment_number = $('#apartmentFilter').val();
                return d;
            },
            error: function (xhr, error, thrown) {
                console.error('Error fetching data:', error);
                alert(lang === "ar" ? "فشل جلب البيانات. يرجى المحاولة مرة أخرى." : "Failed to fetch data. Please try again.");
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'national_id', name: 'national_id' },
            { data: 'location', name: 'location' },
            { data: 'faculty', name: 'faculty' },
            { data: 'phone', name: 'phone' },
            
        ],
        language: lang === "ar" ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        } : {}
    });
    
    // Event listener for "Fetch More" button
    $(document).on('click', '.fetch-more-btn', function () {
        const userId = $(this).data('user-id'); // Get the user ID from the button
        fetchMoreData(userId);
    });
    
    // Function to fetch more data for a specific user
    function fetchMoreData(userId) {
        $.ajax({
            url: window.routes.fetchMoreData, // Replace with your backend endpoint
            method: 'GET',
            data: { user_id: userId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Display the additional data (customize this part based on your needs)
                    alert(lang === "ar" ? "تم جلب البيانات بنجاح: " + JSON.stringify(response.data) : "Data fetched successfully: " + JSON.stringify(response.data));
                } else {
                    alert(lang === "ar" ? "فشل جلب البيانات." : "Failed to fetch data.");
                }
            },
            error: function () {
                alert(lang === "ar" ? "حدث خطأ أثناء جلب البيانات." : "An error occurred while fetching data.");
            }
        });
    }

    // Function to set up event listeners for filters
    function setupFilterListeners() {
        $('#searchBox').on('keyup', function () {
            table.ajax.reload();
        });

        $('#buildingFilter').on('change', function () {
            resetApartmentFilter();
            table.ajax.reload();
        });

        $('#apartmentFilter').on('change', function () {
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
            success: function (data) {
                if (data.error) {
                    handleSummaryError();
                } else {
                    updateSummaryData(data);
                }
            },
            error: function () {
                handleSummaryError();
            }
        });
    }

    // Function to handle errors in summary data fetching
    function handleSummaryError() {
        $('#totalResidents').text(lang === "ar" ? "خطأ" : "Error");
        $('#totalMaleCount').text(lang === "ar" ? "خطأ" : "Error");
        $('#totalFemaleCount').text(lang === "ar" ? "خطأ" : "Error");
        $('#lastUpdateOverall').text(lang === "ar" ? "خطأ" : "Error");
        $('#lastUpdateMaleResidents').text(lang === "ar" ? "خطأ" : "Error");
        $('#lastUpdateFemaleResidents').text(lang === "ar" ? "خطأ" : "Error");
        alert(lang === "ar" ? "حدث خطأ أثناء جلب البيانات." : "Error while fetching data.");
    }

    // Function to update the summary data in the UI
    function updateSummaryData(data) {
        $('#totalResidents').text(data.totalResidents);
        $('#totalMaleCount').text(data.totalMaleCount);
        $('#totalFemaleCount').text(data.totalFemaleCount);

        $('#lastUpdateOverall').text(data.lastUpdateOverall ? data.lastUpdateOverall : lang === "ar" ? "غير متاح" : "N/A");
        $('#lastUpdateMaleResidents').text(data.lastUpdateMaleResidents ? data.lastUpdateMaleResidents : lang === "ar" ? "غير متاح" : "N/A");
        $('#lastUpdateFemaleResidents').text(data.lastUpdateFemaleResidents ? data.lastUpdateFemaleResidents : lang === "ar" ? "غير متاح" : "N/A");
    }

  
    // Fetch summary data on page load
    fetchSummaryData();
});