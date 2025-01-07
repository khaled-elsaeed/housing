@extends('layouts.admin')

@section('title', __('Add Resident'))

@section('links')
<!-- Include necessary CSS files -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Add Resident') }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="#" enctype="multipart/form-data">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="national_id">{{ __('National ID') }}</label>
                            <input type="text" class="form-control border-secondary" id="national_id" name="national_id" required>
                        </div>
                    </div>
                </div>

                <div class="d-none" id="student-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_en">{{ __('English Name') }}</label>
                                <input type="text" class="form-control border-secondary" id="name_en" name="name_en" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name_ar">{{ __('Arabic Name') }}</label>
                                <input type="text" class="form-control border-secondary" id="name_ar" name="name_ar" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile">{{ __('Mobile Number') }}</label>
                                <input type="text" class="form-control border-secondary" id="mobile" name="mobile" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="governorate_id">{{ __('Governorate') }}</label>
                            <select class="form-control border-secondary" id="governorate_id" name="governorate_id">
                                @foreach ($governorates as $governorate)
                                <option value="{{ $governorate->id }}">
                                    {{ $governorate->name_en }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city_id">{{ __('City') }}</label>
                            <select class="form-control border-secondary" id="city_id" name="city_id" disabled>
                                <option value="">{{ __('Select City') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="street">{{ __('Street') }}</label>
                            <input type="text" class="form-control border-secondary" id="street" name="street">
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="faculty_id">{{ __('Faculty') }}</label>
                            <select class="form-control border-secondary" id="faculty_id" name="faculty_id">
                                @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}">
                                    {{ $faculty->name_en }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="program_id">{{ __('Program') }}</label>
                            <select class="form-control border-secondary" id="program_id" name="program_id" disabled>
                                <option value="">{{ __('Select Program') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="building_id">{{ __('Building') }}</label>
                            <select class="form-control border-secondary" id="building_id" name="building_id" required>
                                <option value="">{{ __('Select Building') }}</option>
                                @foreach ($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->number }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="apartment_select">{{ __('Apartment') }}</label>
                            <select class="form-control border-secondary" id="apartment_select" name="apartment_id" disabled>
                                <option value="">{{ __('Select Apartment') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="room_id">{{ __('Room') }}</label>
                            <select class="form-control border-secondary" id="room_id" name="room_id" disabled>
                                <option value="">{{ __('Select Room') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">{{ __('Save Resident') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pages/add-residents.js') }}"></script>
<script>
    const cities = @json($cities);
    const programs = @json($programs);

    window.routes = {
        exportExcel: "{{ route('admin.residents.export-excel') }}",
        getResidentMoreDetails: "{{ route('admin.residents.more-details', ':id') }}",
        fetchResidents: "{{ route('admin.residents.fetch') }}",
        getSummary: "{{ route('admin.residents.get-summary') }}"
    };

    const form = $('form');
    const studentInfoSection = $('#student-info');
    const nameEnInput = $('#name_en');
    const nameArInput = $('#name_ar');
    const academicIdInput = $('#academic_id');
    const academicEmailInput = $('#academic_email');

    // Handle National ID input for auto-fetching details
    $('#national_id').on('input', function () {
        const nationalId = $(this).val();

        // Check if the National ID has exactly 14 digits
        if (nationalId.length === 14 && /^\d+$/.test(nationalId)) {
            // AJAX request to fetch resident details
            $.ajax({
                url: "{{ route('admin.residents.fetch-details') }}",
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                data: { national_id: nationalId },
                success: function (response) {
                    if (response.success) {
                        // Populate fields with the returned data
                        nameEnInput.val(response.resident.name_en || '');
                        nameArInput.val(response.resident.name_ar || '');
                        academicIdInput.val(response.resident.academic_id || '');
                        academicEmailInput.val(response.resident.academic_email || '');

                        // Show the student info section
                        studentInfoSection.removeClass('d-none');
                    } else {
                        swal('Error', response.message || "{{ __('No details found for this National ID.') }}", 'error');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching resident details:", error);
                    swal('Error', "{{ __('An error occurred while fetching resident details.') }}", 'error');
                },
            });
        } else {
            // Hide the student info section if input is invalid
            studentInfoSection.addClass('d-none');
        }
    });

    // Handle form submission via AJAX
    form.on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this); // Collect form data

        // AJAX request to submit the form
        $.ajax({
            url: "{{ route('admin.residents.store') }}", // Update with your form action route
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function (response) {
                if (response.success) {
                    swal('Success', response.message || "{{ __('Resident added successfully!') }}", 'success');
                    // Optionally redirect or reset the form
                    form[0].reset();
                    studentInfoSection.addClass('d-none'); // Hide student info section
                } else {
                    swal('Error', response.message || "{{ __('Failed to add resident.') }}", 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error submitting form:", error);
                swal('Error', "{{ __('An error occurred while submitting the form.') }}", 'error');
            },
        });
    });

    // Handle building selection and fetching empty apartments
    $('#building_id').on('change', function () {
        const buildingId = $(this).val();
        if (buildingId) {
            $('#apartment_select').prop('disabled', false);
            fetchEmptyApartments(buildingId);
        } else {
            $('#apartment_select').prop('disabled', true);
            $('#room_id').prop('disabled', true);
        }
    });

    // Handle apartment selection and fetching empty rooms
    $('#apartment_select').on('change', function () {
        const apartmentId = $(this).val();
        if (apartmentId) {
            $('#room_id').prop('disabled', false);
            fetchEmptyRooms(apartmentId);
        } else {
            $('#room_id').prop('disabled', true);
        }
    });

    // Fetch empty apartments
    async function fetchEmptyApartments(buildingId) {
        try {
            const url = `{{ route('admin.unit.apartment.fetch-empty', ['buildingId' => ':BUILDING_ID']) }}`
                .replace(':BUILDING_ID', buildingId);
            const response = await $.ajax({ url: url, method: 'GET', dataType: 'json' });
            if (response.success && response.apartments.length > 0) {
                populateApartmentSelect(response.apartments);
            } else {
                swal('Error', "{{ __('No apartments available for this building!') }}", 'error');
            }
        } catch (error) {
            console.error('Error fetching apartments:', error);
            swal('Error', "{{ __('Error fetching apartments!') }}", 'error');
        }
    }

    // Fetch empty rooms
    async function fetchEmptyRooms(apartmentId) {
        try {
            const url = `{{ route('admin.unit.room.fetch-empty', ['apartmentId' => ':APARTMENT_ID']) }}`
                .replace(':APARTMENT_ID', apartmentId);
            const response = await $.ajax({ url: url, method: 'GET', dataType: 'json' });
            if (response.success && response.rooms.length > 0) {
                populateRoomSelect(response.rooms);
            } else {
                const roomSelect = $('#room_id');
                roomSelect.empty();
                roomSelect.append('<option value="">{{ __("No available rooms") }}</option>');
                swal('Error', "{{ __('No available rooms!') }}", 'error');
            }
        } catch (error) {
            console.error('Error fetching rooms:', error);
            swal('Error', "{{ __('Error fetching rooms!') }}", 'error');
        }
    }

    // Populate apartment select options
    function populateApartmentSelect(apartments) {
        const apartmentSelect = $('#apartment_select');
        apartmentSelect.empty();
        apartmentSelect.append('<option value="">{{ __("Select Apartment") }}</option>');
        apartments.forEach(apartment => {
            apartmentSelect.append(`<option value="${apartment.id}">${apartment.number}</option>`);
        });
    }

    // Populate room select options
    function populateRoomSelect(rooms) {
        const roomSelect = $('#room_id');
        roomSelect.empty();
        roomSelect.append('<option value="">{{ __("Select Room") }}</option>');
        rooms.forEach(room => {
            roomSelect.append(`<option value="${room.id}">${room.number}</option>`);
        });
    }
</script>
@endsection
