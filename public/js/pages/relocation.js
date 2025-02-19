/**
 * Show the selected form (relocation or swap) and highlight the selected card.
 * @param {string} formType - The type of form to show ('relocate' or 'swap').
 * @param {HTMLElement} selectedCard - The card element that was clicked.
 */
function showForm(formType, selectedCard) {
    // Remove active state from all cards
    document.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('active-card');
        card.querySelector('.card-overlay').classList.remove('show');
    });

    // Add active state to the selected card
    selectedCard.classList.add('active-card');
    selectedCard.querySelector('.card-overlay').classList.add('show');

    // Show the appropriate form
    document.getElementById('relocationForm').style.display = formType === 'relocate' ? 'block' : 'none';
    document.getElementById('swapForm').style.display = formType === 'swap' ? 'block' : 'none';

    // Fetch empty buildings if the relocation form is shown
    if (formType === 'relocate') {
        fetchEmptyBuildings();
    }
}

/**
 * Fetch empty buildings from the server.
 */
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
            showError(getText('error_fetching_buildings'));
        }
    } catch (error) {
        console.error('Error fetching buildings:', error);
        showError(getText('error_fetching_buildings'));
    }
}

/**
 * Populate the building select dropdown.
 * @param {Array} buildings - List of buildings.
 */
function populateBuildingSelect(buildings) {
    const buildingSelect = $('#building_select');
    buildingSelect.empty().append(`<option value="">${getText('select_building')}</option>`);

    buildings.forEach(building => {
        buildingSelect.append(`<option value="${building.id}">${getText('building')} ${building.number}</option>`);
    });

    buildingSelect.on('change', function () {
        const selectedBuilding = $(this).val();
        if (selectedBuilding) {
            fetchEmptyApartments(selectedBuilding);
        }
    });
}

/**
 * Fetch empty apartments for a selected building.
 * @param {string} buildingId - The ID of the selected building.
 */
async function fetchEmptyApartments(buildingId) {
    try {
        const url = window.routes.fetchEmptyApartments(buildingId);
        const response = await $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
        });

        if (response.success && response.apartments.length > 0) {
            populateApartmentSelect(response.apartments);
        } else {
            showError(getText('error_fetching_apartments'));
        }
    } catch (error) {
        console.error('Error fetching apartments:', error);
        showError(getText('error_fetching_apartments'));
    }
}

/**
 * Populate the apartment select dropdown.
 * @param {Array} apartments - List of apartments.
 */
function populateApartmentSelect(apartments) {
    const apartmentSelect = $('#apartment_select');
    apartmentSelect.empty().append(`<option value="">${getText('select_apartment')}</option>`);

    apartments.forEach(apartment => {
        apartmentSelect.append(`<option value="${apartment.id}">${getText('apartment')} ${apartment.number}</option>`);
    });

    apartmentSelect.on('change', function () {
        const selectedApartment = $(this).val();
        if (selectedApartment) {
            fetchEmptyRooms(selectedApartment);
        }
    });
}

/**
 * Fetch empty rooms for a selected apartment.
 * @param {string} apartmentId - The ID of the selected apartment.
 */
async function fetchEmptyRooms(apartmentId) {
    try {
        const url = window.routes.fetchEmptyRooms(apartmentId);
        const response = await $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
        });

        if (response.success && response.rooms) {
            populateRoomSelect(response.rooms);
        } else {
            showError(getText('error_fetching_rooms'));
        }
    } catch (error) {
        console.error('Error fetching rooms:', error);
        showError(getText('error_fetching_rooms'));
    }
}

/**
 * Populate the room select dropdown.
 * @param {Array} rooms - List of rooms.
 */
function populateRoomSelect(rooms) {
    const roomSelect = $('#room_select');

    if (rooms.length > 0) {
        roomSelect.empty().append(`<option value="">${getText('select_room')}</option>`);
        rooms.forEach(room => {
            roomSelect.append(`<option value="${room.id}">${getText('room')} ${room.number}</option>`);
        });
    } else {
        roomSelect.empty().append(`<option value="">${getText('no_empty_rooms')}</option>`);
    }
}

/**
 * Fetch resident details by national ID.
 * @param {jQuery} nationalIdInput - The input field for the national ID.
 * @param {jQuery} detailsContainer - The container to display resident details.
 * @param {jQuery} reservationIdInput - The input field to store the reservation ID.
 * @param {boolean} swapMode - Whether the function is used in swap mode.
 */
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
                                <p><strong>${getText('name')}:</strong> ${data.student.name_en}</p>
                                <p><strong>${getText('faculty')}:</strong> ${data.student.faculty}</p>
                                <p><strong>${getText('building')}:</strong>   ${data.reservation.building_number}</p>
                                <p><strong>${getText('apartment')}:</strong> ${data.reservation.apartment_number}</p>
                                <p><strong>${getText('room')}:</strong> ${data.reservation.room_number}</p>
                            </div>
                        </div>
                    `;
                    detailsContainer.html(detailsHtml);
                    reservationIdInput.val(data.reservation.id);
                }
            }).fail(function (jqXHR) {
                if (jqXHR.status === 404) {
                    detailsContainer.html(`<p class="text-danger">${getText('no_active_reservation')}</p>`);
                } else {
                    detailsContainer.html(`<p class="text-danger">${getText('error_fetching_details')}</p>`);
                }
            });
        } else {
            detailsContainer.html('');
            if (!swapMode) {
                $('#new_room').html(`<option value="">${getText('select_room')}</option>`);
            }
        }
    });
}

/**
 * Handle relocation form submission.
 * @param {Event} event - The form submission event.
 */
function handleRelocationFormSubmission(event) {
    event.preventDefault();

    const btn = $(event.target).find("button[type='submit']");

    toggleButtonLoading(btn, true);

    const newRoom = $('#room_select').val();
    const reservationId = $('#reservation_id_1').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    if (!newRoom || !reservationId) {
        showError(getText('select_room_and_reservation'));
        return;
    }

    $.ajax({
        url: window.routes.relocation,
        method: 'POST',
        data: {
            _token: csrfToken,
            room_id: newRoom,
            reservation_id: reservationId,
        },
        success: function (response) {
            if (response.success) {
                // Extract new room details from the response
                const newRoomNumber = response.new_room_details.room_number;
                const newApartmentNumber = response.new_room_details.apartment_number;
                const newBuildingNumber = response.new_room_details.building_number;

                // Construct the success message with new room details
                const successMessage = `${getText('relocation_success')}\n\n` +
                    `${getText('new_room_details')}:\n` +
                    `${getText('building')}: ${newBuildingNumber}\n` +
                    `${getText('apartment')}: ${newApartmentNumber}\n` +
                    `${getText('room')}: ${newRoomNumber}`;

                // Show the success message
                showSuccess(successMessage);
            } else {
                showError(response.error || getText('relocation_failed'));
            }
        },
        error: function (response) {
            const errorMessage = response.responseJSON?.error || getText('relocation_error');
            showError(errorMessage);
        },
        complete:function(){
            toggleButtonLoading(btn, false);

        }
    });
}

/**
 * Handle swap form submission.
 * @param {Event} event - The form submission event.
 */
function handleSwapFormSubmission(event) {
    event.preventDefault();

    const btn = $(event.target).find("button[type='submit']");

    toggleButtonLoading(btn, true);

    const reservation1Id = $('#reservation_id_1_swap').val();
    const reservation2Id = $('#reservation_id_2_swap').val();
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: window.routes.swapReservation,
        method: 'POST',
        data: {
            _token: csrfToken,
            reservation_id_1: reservation1Id,
            reservation_id_2: reservation2Id,
        },
        success: function (response) {
            if (response.success) {
                showSuccess(getText('swap_success'));
            } else {
                showError(response.error || getText('swap_failed'));
            }
        },
        error: function (error) {
            const errorMessage = error.responseJSON?.error || getText('swap_error');
            showError(errorMessage);
        },
        complete:function(){
            toggleButtonLoading(btn, false);

        }
    });
}

/**
 * Show a success message.
 * @param {string} message - The success message to display.
 */
function showSuccess(message) {
    swal({
        title: getText('success'),
        text: message,
        type: 'success',
        confirmButtonText: getText('ok'),
    }).then(() => {
        window.location.reload(true);
    });
}

/**
 * Show an error message.
 * @param {string} message - The error message to display.
 */
function showError(message) {
    swal({
        title: getText('error'),
        text: message,
        type: 'error',
        confirmButtonText: getText('ok'),
    });
}

function toggleButtonLoading(button, isLoading) {
    if (isLoading) {
        // Store the full button HTML (including the icon) before changing it
        let loading = getText('loading');
        button.data('original-html', button.html())
            .html(`<i class="fa fa-spinner fa-spin"></i> ${loading}`)
            .prop('disabled', true);
    } else {
        // Restore the full original HTML (with the icon)
        button.html(button.data('original-html'))
            .prop('disabled', false);
    }
}

/**
 * Get translated text based on the current language.
 * @param {string} key - The key for the text to translate.
 * @returns {string} - The translated text.
 */
function getText(key) {
    const lang = document.documentElement.lang || 'en';
    const translations = {
        en: {
            select_building: 'Select Building',
            select_apartment: 'Select Apartment',
            select_room: 'Select Room',
            new_room_details: 'New Room Details',
            no_empty_rooms: 'No Empty Rooms',
            error_fetching_buildings: 'Error fetching buildings!',
            error_fetching_apartments: 'Error fetching apartments!',
            error_fetching_rooms: 'Error fetching rooms!',
            name: 'Name',
            faculty: 'Faculty',
            building: 'Building',
            apartment: 'Apartment',
            room: 'Room',
            no_active_reservation: 'No active reservation found for this National ID.',
            error_fetching_details: 'Error fetching resident details. Please try again.',
            select_room_and_reservation: 'Please select a room and provide the reservation details.',
            relocation_success: 'Relocation successful!',
            relocation_failed: 'Relocation failed. Please try again.',
            relocation_error: 'Error during relocation! Please try again.',
            swap_success: 'The reservations were swapped successfully!',
            swap_failed: 'Swap failed. Please try again.',
            swap_error: 'An unknown error occurred during the swap.',
            success: 'Success',
            error: 'Error',
            ok: 'OK',
        },
        ar: {
            select_building: 'اختر المبنى',
            select_apartment: 'اختر الشقة',
            select_room: 'اختر الغرفة',
            no_empty_rooms: 'لا توجد غرف فارغة',
            new_room_details: 'تفاصيل الغرفة الجديدة',

            error_fetching_buildings: 'خطأ في جلب المباني!',
            error_fetching_apartments: 'خطأ في جلب الشقق!',
            error_fetching_rooms: 'خطأ في جلب الغرف!',
            name: 'الاسم',
            faculty: 'الكلية',
            building: 'المبنى',
            apartment: 'الشقة',
            room: 'الغرفة',
            no_active_reservation: 'لا يوجد حجز نشط لهذا الرقم القومي.',
            error_fetching_details: 'خطأ في جلب تفاصيل المقيم. يرجى المحاولة مرة أخرى.',
            select_room_and_reservation: 'يرجى اختيار غرفة وتوفير تفاصيل الحجز.',
            relocation_success: 'تمت عملية تبديل الغرف بنجاح!',
            relocation_failed: 'فشلت عملية تبديل الغرف. يرجى المحاولة مرة أخرى.',
            relocation_error: 'حدث خطأ أثناء عملية تبديل الغرف! يرجى المحاولة مرة أخرى.',
            swap_success: 'تم تبديل الحجوزات بنجاح!',
            swap_failed: 'فشل التبديل. يرجى المحاولة مرة أخرى.',
            swap_error: 'حدث خطأ غير معروف أثناء التبديل.',
            success: 'نجاح',
            error: 'خطأ',
            ok: 'موافق',
        },
    };

    return translations[lang][key] || key;
}

// Initialize on document ready
$(document).ready(function () {
    fetchResidentDetails($('#resident_nid_1'), $('#residentDetails_1'), $('#reservation_id_1'));
    fetchResidentDetails($('#resident_nid_1_swap'), $('#residentDetailsSwap1'), $('#reservation_id_1_swap'), true);
    fetchResidentDetails($('#resident_nid_2_swap'), $('#residentDetailsSwap2'), $('#reservation_id_2_swap'), true);

    $('#relocationForm form').on('submit', handleRelocationFormSubmission);
    $('#swapForm form').on('submit', handleSwapFormSubmission);
});