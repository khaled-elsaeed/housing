function showForm(formType, selectedCard) {
    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('active-card');
        card.querySelector('.card-overlay').classList.remove('show');
    });

    selectedCard.classList.add('active-card');
    selectedCard.querySelector('.card-overlay').classList.add('show');

    document.getElementById('relocationForm').style.display = formType === 'relocate' ? 'block' : 'none';
    document.getElementById('swapForm').style.display = formType === 'swap' ? 'block' : 'none';

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
            swal({
                title: 'Error',
                text: 'Error fetching buildings!',
                type: 'error',
                confirmButtonText: 'OK',
            });
        }
    } catch (error) {
        console.error('Error fetching buildings:', error);
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
            swal({
                title: 'Error',
                text: 'Error fetching apartments!',
                type: 'error',
                confirmButtonText: 'OK',
            });
        }
    } catch (error) {
        console.error('Error fetching apartments:', error);
        swal({
            title: 'Error',
            text: 'Error fetching apartments!',
            type: 'error',
            confirmButtonText: 'OK',
        });
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

        if (response.success && response.rooms) {
            populateRoomSelect(response.rooms);
        } else{
            swal({
                title: 'Error',
                text: 'Error fetching rooms!',
                type: 'error',
                confirmButtonText: 'OK',
            });
        }
    } catch (error) {
        console.error('Error fetching rooms:', error);
        swal({
            title: 'Error',
            text: 'Error fetching rooms!',
            type: 'error',
            confirmButtonText: 'OK',
        });
    }
}

function populateRoomSelect(rooms) {
    const roomSelect = $('#room_select');

    if(rooms.length > 0 ){
        roomSelect.empty().append('<option value="">Select Room</option>');

        rooms.forEach(room => {
            roomSelect.append(`<option value="${room.id}">Room ${room.number}</option>`);
        });
    }else{
        roomSelect.empty().append('<option value="">No Empty Rooms</option>');

    }
    
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

function handleRelocationFormSubmission(event) {
    event.preventDefault();
    
    const newRoom = $('#room_select').val();
    const reservationId = $('#reservation_id_1').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    if (!newRoom || !reservationId) {
        swal({
            title: 'Error',
            text: 'Please select a room and provide the reservation details.',
            type: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    $.ajax({
        url: window.routes.relocation,
        method: 'POST',
        data: {
            _token: csrfToken, 
            room_id: newRoom,
            reservation_id: reservationId  
        },
        success: function(response) {
            if (response.success) {
                swal({
                    title: 'Success',                
                    text: 'Relocation successful!',  
                    type: 'success',                 
                    confirmButtonText: 'OK',         
                }).then(() => {
                    window.location.reload(true);    
                });
            } else {
                swal({
                    title: 'Error',
                    text: response.error || 'Relocation failed. Please try again.',
                    type: 'error',
                    confirmButtonText: 'OK',
                });
            }
        },
        error: function(jqXHR) {
            swal({
                title: 'Error',
                text: 'Error during relocation! Please try again.',
                type: 'error',
                confirmButtonText: 'OK',
            });
        }
    });
}

function handleSwapFormSubmission(event) {
    event.preventDefault();
    const reservation1Id = $('#reservation_id_1_swap').val();  
    const reservation2Id = $('#reservation_id_2_swap').val();  
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: window.routes.swapReservation,
        method: 'POST',
        data: {
            _token: csrfToken,
            reservation_id_1: reservation1Id,
            reservation_id_2: reservation2Id
        },
        success: function() {
            if (response.success) {
                swal({
                    title: 'Success',                
                    text: 'The Reservations Swaped successful!',  
                    type: 'success',                 
                    confirmButtonText: 'OK',         
                }).then(() => {
                    window.location.reload(true);    
                });
            } 
        },
        error: function(error) {
    const errorMessage = error.responseJSON?.error || 'An unknown error occurred';

        swal({
            title: 'Error',
            text: errorMessage,
            type: 'error',
            confirmButtonText: 'OK',
        });
            }
    });
}

$(document).ready(function() {
    fetchResidentDetails($('#resident_nid_1'), $('#residentDetails_1'), $('#reservation_id_1'));
    fetchResidentDetails($('#resident_nid_1_swap'), $('#residentDetailsSwap1'), $('#reservation_id_1_swap'), true);
    fetchResidentDetails($('#resident_nid_2_swap'), $('#residentDetailsSwap2'), $('#reservation_id_2_swap'), true);

    $('#relocationForm form').on('submit', handleRelocationFormSubmission);
    $('#swapForm form').on('submit', handleSwapFormSubmission);
});