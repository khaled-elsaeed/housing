$(document).ready(function() {

    const messages = {
        en: {
            success: "Success!",
            error: "Error!",
            approve: "Approve",
            reject: "Reject",
            acceptedAt: "Accepted At",
            accepted:"Accepted",
        },
        ar: {
            success: "نجاح!",
            error: "خطأ!",
            approve: "موافقة",
            reject: "رفض",
            acceptedAt: "تم القبول في",
            accepted:"مقبول",

        }
    };

    const lang = $("html").attr("lang") || "en";

    // Toggle button functionality
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");

    // Handle the toggle button icon change
    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
        icon.classList.remove("fa-search-minus");
        icon.classList.add("fa-search-plus");
    });

    // Initialize DataTable
    const table = $('#default-datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ordering: false,

        ajax: {
            url: window.routes.fetchRequests,
            data: function(d) {
                d.customSearch = $('#searchBox').val();
            }
        },
        columns: [
            { data: 'user.name', name: 'user.name' },
            { data: 'period_type', name: 'period_type' },
            { data: 'period_duration', name: 'period_duration' },
            { data: 'requested_at', name: 'requested_at' },

            {
                data: 'status',
                render: function(data) {
                    let badgeClass = 'badge badge-';
                    let stat ;
                    switch(data) {
                        case 'pending':
                            badgeClass += 'warning';
                            stat = 'pending' ;
                            break;
                        case 'accepted':
                            badgeClass += 'success';
                            stat = 'accepted' ;

                            break;
                        case 'rejected':
                            badgeClass += 'danger';
                            stat = 'rejected' ;

                            break;
                    }
                    return `<span class="${badgeClass}">${messages[lang][stat]}</span>`;
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.status === 'pending') {
                        return renderPendingActions(data);
                    }
            
                    if (data.status === 'accepted' && data.updated_at) {
                        return renderAcceptedStatus(data);
                    }
            
                    return '';
                }
            }
            
            
            
        ],
        language: lang === "ar" ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        } : {}
    });

    // Helper function to render pending actions
    function renderPendingActions(data) {
        return `
            <div class="action-buttons">
                <button class="btn btn-round btn-success accept-btn" data-id="${data.id}" title="${messages[lang].approve}">
                    <i class="feather icon-check"></i>
                </button>
                <button class="btn btn-round btn-danger reject-btn" data-id="${data.id}" title="${messages[lang].reject}">
                    <i class="feather icon-x"></i>
                </button>
            </div>`;
    }
    
    // Helper function to render accepted status
    function renderAcceptedStatus(data) {
        const formattedDate = data.actions; // Assuming `actions` contains the formatted date
        return `
            <div class="accepted-status">
                <span class="badge badge-success">${messages[lang].acceptedAt}</span>
                <br>
                <large>${formattedDate}</large>
            </div>`;
    }
    // Search functionality
    $('#searchBox').on('keyup', function() {
        table.ajax.reload();
    });

    // Fetch and display summary data
    function fetchSummaryData() {
        $.ajax({
            url: window.routes.getSummary,
            method: 'GET',
            success: function(data) {
                $('#totalRequestsCount').text(data.total);
                $('#pendingRequestsCount').text(data.pending);
                $('#acceptedRequestsCount').text(data.accepted);
                $('#rejectedRequestsCount').text(data.rejected);
            }
        });
    }

    // Load summary on page load
    fetchSummaryData();

// Handle Auto-Reserve button
$('#autoReserveBtn').on('click', function() {
    const btn = $(this);

    toggleButtonLoading(btn, true);

    $.ajax({
        url: window.routes.autoReserve,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF Token
        },
        success: function(response) {
            swal({
                title: messages[lang].success,
                text: response.message,
                type: "success",
                showConfirmButton: true
            });
            table.ajax.reload();
            fetchSummaryData();
        },
        error: function(xhr) {
            let errorMessage = xhr.responseJSON?.message || messages[lang].error;

            swal({
                title: messages[lang].error,
                text: errorMessage,
                type: "error",
                showConfirmButton: true
            });
        },
        complete: function() {
            toggleButtonLoading(btn, false);
        }
    });
});


    // Handle Accept Request button
    $(document).on('click', '.accept-btn', function() {
        const requestId = $(this).data('id');
        $('#requestId').val(requestId);
        $('#acceptRequestModal').modal('show');
        loadBuildings();
    });

    // Handle Reject Request button
    $(document).on('click', '.reject-btn', function() {
        const requestId = $(this).data('id');
        $('#rejectRequestId').val(requestId);
        $('#rejectRequestModal').modal('show');
    });

    // Load Buildings
    function loadBuildings() {
        $.ajax({
            url: window.routes.fetchEmptyBuildings,
            method: 'GET',
            success: function(data) {
                const select = $('#building');
                select.empty().append('<option value="">Select Building</option>');
                data.buildings.forEach(function(building) {
                    select.append(`<option value="${building.id}">${building.number}</option>`);
                });
            }
        });
    }

    // Handle Building Selection
    $('#building').on('change', function() {
        const buildingId = $(this).val();
        const apartmentSelect = $('#apartment');
        const roomSelect = $('#room');

        apartmentSelect.empty().append('<option value="">Select Apartment</option>').prop('disabled', true);
        roomSelect.empty().append('<option value="">Select Room</option>').prop('disabled', true);

        if (buildingId) {
            apartmentSelect.prop('disabled', false);
            const url = window.routes.fetchEmptyApartments.replace(':buildingId', buildingId);
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    data.apartments.forEach(function(apartment) {
                        apartmentSelect.append(`<option value="${apartment.id}">${apartment.number}</option>`);
                    });
                }
            });
        }
    });

    // Handle Apartment Selection
    $('#apartment').on('change', function() {
        const apartmentId = $(this).val();
        const roomSelect = $('#room');

        roomSelect.empty().append('<option value="">Select Room</option>').prop('disabled', true);

        if (apartmentId) {
            roomSelect.prop('disabled', false);
            const url = window.routes.fetchEmptyRooms.replace(':apartmentId', apartmentId);
            $.ajax({
                url: url,
                method: 'GET',
                success: function(data) {
                    data.rooms.forEach(function(room) {
                        roomSelect.append(`<option value="${room.id}">${room.number}</option>`);
                    });
                }
            });
        }
    });

    // Handle Accept Confirmation
    $('#confirmAccept').on('click', function() {
        const btn = $(this);
        const form = $('#acceptRequestForm');
        const requestId = $('#requestId').val();
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        toggleButtonLoading(btn, true);

        const url = window.routes.acceptRequest.replace(':id', requestId);
        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#acceptRequestModal').modal('hide');
                swal({
                    title: messages[lang].success,
                    text: response.message,
                    type: "success",
                    showConfirmButton: true
                });
                table.ajax.reload();
                fetchSummaryData();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || messages[lang].error;

                swal({
                    title: messages[lang].error,
                    text: errorMessage,
                    type: "error",
                    showConfirmButton: true
                });
            },
            complete: function() {
                toggleButtonLoading(btn, false);
            }
        });
    });

    // Handle Reject Confirmation
    $('#confirmReject').on('click', function() {
        const btn = $(this);
        const form = $('#rejectRequestForm');
        const requestId = $('#rejectRequestId').val();

        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        toggleButtonLoading(btn, true);

        const url = window.routes.rejectRequest.replace(':id', requestId);
        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                $('#rejectRequestModal').modal('hide');
                swal({
                    title: messages[lang].success,
                    text: response.message,
                    type: "success",
                    showConfirmButton: true
                });
                table.ajax.reload();
                fetchSummaryData();
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || messages[lang].error;

                swal({
                    title: messages[lang].error,
                    text: errorMessage,
                    type: "error",
                    showConfirmButton: true
                });
            },
            complete: function() {
                toggleButtonLoading(btn, false);
            }
        });
    });

    // Utility function for button loading state
    function toggleButtonLoading(button, isLoading) {
        if (isLoading) {
            // Store the full button HTML (including the icon) before changing it
            let loading = lang == 'en' ? 'Loading...' : 'تحميل...';
            button.data('original-html', button.html())
                .html(`<i class="fa fa-spinner fa-spin"></i> ${loading}`)
                .prop('disabled', true);
        } else {
            // Restore the full original HTML (with the icon)
            button.html(button.data('original-html'))
                .prop('disabled', false);
        }
    }
    
    
});