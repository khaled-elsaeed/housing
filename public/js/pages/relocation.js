// Centralized error handling and notification
function showNotification(message, isError = false) {
    const notificationClass = isError ? 'text-danger' : 'text-success';
    const notification = $('<div>')
        .addClass(`alert ${notificationClass} mt-3`)
        .text(message)
        .appendTo('#notifications')
        .fadeIn()
        .delay(3000)
        .fadeOut(function() { $(this).remove(); });
}

function showSwalNotification(message, isError = false) {
    const title = isError ? 'Error' : 'Success';
    const type = isError ? 'error' : 'success';
    
    swal({
        title: title,
        text: message,
        type: type,
        confirmButtonText: 'OK',
    });
}


function showForm(formType, selectedCard) {
    // Reset all cards
    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('active-card');
        card.querySelector('.card-overlay').classList.remove('show');
    });

    // Highlight selected card
    selectedCard.classList.add('active-card');
    selectedCard.querySelector('.card-overlay').classList.add('show');

    // Show corresponding form
    document.getElementById('relocationForm').style.display = formType === 'relocate' ? 'block' : 'none';
    document.getElementById('swapForm').style.display = formType === 'swap' ? 'block' : 'none';

    // Fetch data if needed
    if (formType === 'relocate') {
        fetchEmptyBuildings();
    }
}

async function fetchEmptyBuildings() {
    try {
        const response = await $.ajax({
            url: window.routes.fetchEmptyBuildings,
            method: 'GET',
            dataType: 'json',
        });

        if (response.success && response.buildings.length > 0) {
            populateBuildingSelect(response.buildings);
        } else {
            showNotification('No buildings available!', true);
        }
    } catch (error) {
        console.error('Error fetching buildings:', error);
        showNotification('Error fetching buildings!', true);
    }
}

function populateBuildingSelect(buildings) {
    const buildingSelect = $('#building_select');
    buildingSelect.empty().append('<option value="">Select Building</option>');
    
    buildings.forEach(building => {
        buildingSelect.append(`<option value="${building.id}">Building ${building.number}</option>`);
    });
    
    buildingSelect.on('change', function () {
        const selectedBuilding = $(this).val();
        if (selectedBuilding) {
            fetchEmptyApartments(selectedBuilding);
        }
    });
}

async function fetchEmptyApartments(buildingId) {
    try {
        const url = window.routes.fetchEmptyApartments(buildingId);
        const response = await $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json'
        });

        if (response.success && response.apartments.length > 0) {
            populateApartmentSelect(response.apartments);
        } else {
            showNotification('No apartments available in this building!', true);
        }
    } catch (error) {
        console.error('Error fetching apartments:', error);
        showSwalNotification('Error fetching apartments!', true);
    }
}

function populateApartmentSelect(apartments) {
    const apartmentSelect = $('#apartment_select');
    apartmentSelect.empty().append('<option value="">Select Apartment</option>');
    
    apartments.forEach(apartment => {
        apartmentSelect.append(`<option value="${apartment.id}">Apartment ${apartment.number}</option>`);
    });
    
    apartmentSelect.on('change', function () {
        const selectedApartment = $(this).val();
        if (selectedApartment) {
            fetchEmptyRooms(selectedApartment);
        }
    });
}

async function fetchEmptyRooms(apartmentId) {
    try {
        const url = window.routes.fetchEmptyRooms(apartmentId);
        const response = await $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json'
        });

        if (response.success && response.rooms.length > 0) {
            populateRoomSelect(response.rooms);
        } else {
            showNotification('No rooms available in this apartment!', true);
        }
    } catch (error) {
        console.error('Error fetching rooms:', error);
        showSwalNotification('Error fetching rooms!', true);
    }
}

function populateRoomSelect(rooms) {
    const roomSelect = $('#room_select');
    roomSelect.empty().append('<option value="">Select Room</option>');
    
    rooms.forEach(room => {
        roomSelect.append(`<option value="${room.id}">Room ${room.number}</option>`);
    });
}

function fetchResidentDetails(nationalIdInput, detailsContainer, reservationIdInput, swapMode = false) {
    nationalIdInput.on('keyup', function () {
        const nid = $(this).val();
        if (nid.length === 14) {
            const url = window.routes.residentDetails(nid);

            $.get(url, function (data) {
                if (data.success) {
                    const detailsHtml = `
                        <div class="card border-info">
                            <div class="card-body">
                                <p><strong>Name:</strong> ${data.student.name_en}</p>
                                <p><strong>Faculty:</strong> ${data.student.faculty}</p>
                                <p><strong>Building:</strong> B-${data.reservation.building_number}</p>
                                <p><strong>Apartment:</strong> A-${data.reservation.apartment_number}</p>
                                <p><strong>Room:</strong> R-${data.reservation.room_number}</p>
                            </div>
                        </div>
                    `;
                    detailsContainer.html(detailsHtml);
                    reservationIdInput.val(data.reservation.id);
                } 
            }).fail(function (jqXHR) {
                if (jqXHR.status === 404) {
                    detailsContainer.html('<p class="text-danger">No active reservation found for this National ID.</p>');
                } else {
                    detailsContainer.html('<p class="text-danger">Error fetching resident details. Please try again.</p>');
                }
            });
        } else {
            detailsContainer.html('');
            if (!swapMode) {
                $('#new_room').html('<option value="">Select a room</option>');
            }
        }
    });
}


// Usage
fetchResidentDetails($('#resident_nid_1'), $('#residentDetails_1'), $('#reservation_id_1'));
fetchResidentDetails($('#resident_nid_1_swap'), $('#resident1Details'), $('#reservation_id_1_swap'), true);
fetchResidentDetails($('#resident_nid_2_swap'), $('#resident2Details'), $('#reservation_id_2_swap'), true);

// Form Submissions
$('#relocationForm form').on('submit', function(event) {
    event.preventDefault();
    
    // Get form data
    const newRoom = $('#room_select').val();
    const reservationId = $('#reservation_id_1').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Fetch CSRF token from meta tag

    // Validate required fields
    if (!newRoom || !reservationId) {
        showSwalNotification('Please select a room and provide the reservation details.', true);
        return;
    }

    // AJAX request for relocation
    $.ajax({
        url: window.routes.relocation, // API endpoint
        method: 'POST',
        data: {
            _token: csrfToken, // Pass the CSRF token
            room_id: newRoom,
            reservation_id: reservationId  
        },
        success: function(response) {
            if (response.success) {
                showSwalNotification('Relocation successful!');
                window.location.reload(true);
            } else {
                showSwalNotification(response.message || 'Relocation failed. Please try again.', true);
            }
        },
        error: function(jqXHR) {
                showSwalNotification('Error during relocation! Please try again.', true);
        }
    });
});


$('#swapForm form').on('submit', function(event) {
    event.preventDefault();
    const reservation1Id = $('#reservation_id_1_swap').val();  
    const reservation2Id = $('#reservation_id_2_swap').val();  
    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Fetch CSRF token from meta tag

    
    $.ajax({
        url: window.routes.swapReservation,
        method: 'POST',
        data: {
            _token: csrfToken, // Pass the CSRF token
            reservation_id_1: reservation1Id,
            reservation_id_2: reservation2Id
        },
        success: function() {
            showSwalNotification('Swap successful!');
            window.location.reload(true);

        },
        error: function() {
            showSwalNotification('Error during swap!', true);
        }
    });
});