@extends('layouts.student') @section('title', __('Student Dashboard')) @section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-white text-primary overflow-hidden position-relative">
                <div class="card-body d-flex align-items-center p-3 p-md-4">
                    <div class="d-flex align-items-center w-100">
                        <div class="flex-shrink-0 me-3 me-md-4">
                            <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fa fa-user-circle fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h1 class="h4 mb-1 fw-bold">{{ __('Welcome') }}, {{ $user->getUsernameEnAttribute() }}</h1>
                            <p class="text-primary-50 mb-0 d-none d-md-block">{{ __('Manage your housing and stay updated with your recent activities') }}</p>
                        </div>
                        <div class="flex-shrink-0 d-none d-md-block">
                            <div class="text-primary-50 text-end">
                                <small>{{ date('F d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dashboard Content -->
    <div class="row">
        <!-- Reservation Overview -->
        <div class="col-lg-8">
            @if($reservation)
            <div class="card border-0 mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h4 class="card-title mb-0 fs-5 fw-semibold">
            <i class="fa fa-hotel text-primary me-2"></i>{{ __('Active Reservation') }}
        </h4>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Reservation Details Cards -->
            <div class="col-md-4 border">
                <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                    <div class="me-3">
                        <i class="fa fa-building fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fs-6">{{ __('Building') }}</h6>
                        <p class="h5 mb-0 fw-bold">{{ $reservation->room->apartment->building->number }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                    <div class="me-3">
                        <i class="fa fa-home fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fs-6">{{ __('Apartment') }}</h6>
                        <p class="h5 mb-0 fw-bold">{{ $reservation->room->apartment->number }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex align-items-center bg-gradient-light p-3 rounded hover-scale">
                    <div class="me-3">
                        <i class="fa fa-bed fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 fs-6">{{ __('Room') }}</h6>
                        <p class="h5 mb-0 fw-bold">{{ $reservation->room->number }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            @endif
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="card-title mb-0"><i class="fa fa-history text-primary me-2"></i>{{ __('Recent Activities') }}</h4>
                </div>
                <div class="card-body p-0">
                    @if($activities->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($activities as $activity)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($activity->activity_type === 'reservation Confirmed')
                                    <i class="fa fa-check-circle text-success"></i>
                                    @elseif($activity->activity_type === 'Documents Uploaded')
                                    <i class="fa fa-upload text-primary"></i>
                                    @elseif($activity->activity_type === 'Room Assignment')
                                    <i class="fa fa-building text-info"></i>
                                    @elseif($activity->activity_type === 'Term Registration')
                                    <i class="fa fa-calendar text-warning"></i>
                                    @else
                                    <i class="fa fa-circle text-secondary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $activity->activity_type }}</h6>
                                    <small class="text-muted">{{ $activity->description }}</small>
                                </div>
                            </div>
                            <span class="text-muted small">{{ $activity->created_at->diffForHumans() }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="p-3 text-center text-muted">
                        {{ __('No recent activities found.') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Reservation Details and Quick Actions -->
        <div class="col-lg-4 mt-4 mt-md-0">
            @if($reservation)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="card-title mb-0"><i class="fa fa-info-circle text-primary me-2"></i>{{ __('Reservation Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-muted">{{ __('Status') }}</h6>
                        <span
                            class="badge 
                     {{ $reservation->status === 'pending' ? 'bg-warning' : 
                     ($reservation->status === 'active' ? 'bg-success' : 'bg-danger') }}"
                        >
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <h6 class="text-muted">{{ __('Term') }}</h6>
                        <p class="mb-0">{{ ucfirst($reservation->academicTerm->name) }}</p>
                    </div>
                    <div>
                        <h6 class="text-muted">{{ __('Year') }}</h6>
                        <p class="mb-0">{{ $reservation->academicTerm->academic_year }}</p>
                    </div>
                </div>
            </div>
            @endif
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="card-title mb-0"><i class="fa fa-bolt text-primary me-2"></i>{{ __('Quick Actions') }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal"><i class="fa fa-plus-circle me-2"></i>{{ __('New Reservation') }}</button>
                        <button class="btn btn-outline-secondary"><i class="fa fa-file-text-o me-2"></i>{{ __('View Documents') }}</button>
                        <button class="btn btn-outline-info"><i class="fa fa-support me-2"></i>{{ __('Help & Support') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Reservation Modal -->
<div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReservationModalLabel">@lang('Add New Reservation')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <div class="modal-body">
                <form id="addReservationForm" method="POST" action="{{ route('student.reservation.request') }}">
                    @csrf
                    <!-- Reservation Period Type -->
                    <div class="mb-3">
                        <label for="reservationType" class="form-label">@lang('Reservation Period Type')</label>
                        <select name="reservation_period_type" id="reservationType" class="form-select" required>
                            <option value="" disabled selected>@lang('Select Reservation Period Type')</option>
                            <option value="long">@lang('Long Period')</option>
                            <option value="short">@lang('Short Period')</option>
                        </select>
                    </div>

                    <!-- Long Term Details (Academic Terms) -->
                    <div id="longPeriodDetails" class="mb-3 d-none">
                        <label for="reservationTerm" class="form-label">@lang('Select Academic Term')</label>
                        <select name="reservation_academic_term_id" id="reservationTerm" class="form-select" required>
                            <option value="" disabled selected>@lang('Select Term')</option>
                            @foreach($availableTerms as $term)
                            <option value="{{ $term->id }}">@lang($term->name) - {{$term->academic_year}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Short Term Details (Day/Week/Month) -->
                    <div id="shortPeriodDetails" class="d-none">
                        <label for="shortPeriodDuration" class="form-label">@lang('Select Duration')</label>
                        <select name="short_duration" id="shortPeriodDuration" class="form-select" required>
                            <option value="" disabled selected>@lang('Select Duration')</option>
                            <option value="day">@lang('Day')</option>
                            <option value="week">@lang('Week')</option>
                            <option value="month">@lang('Month')</option>
                        </select>

                        <!-- Dates for short term -->
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <label for="startDate" class="form-label">@lang('Start Date')</label>
                                <input type="date" class="form-control" id="startDate" name="start_date" required />
                            </div>
                            <div class="col-md-6 mb-3" id="endDateContainer" style="display: none;">
                                <label for="endDate" class="form-label">@lang('End Date')</label>
                                <input type="date" class="form-control" id="endDate" name="end_date" readonly />
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary w-100" id="submitReservation" disabled>
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true" id="submitSpinner"></span>
                            <span id="submitText">@lang('Add Reservation')</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection @section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("addReservationForm");
        const reservationType = document.getElementById("reservationType");
        const shortPeriodDetails = document.getElementById("shortPeriodDetails");
        const longPeriodDetails = document.getElementById("longPeriodDetails");
        const submitReservation = document.getElementById("submitReservation");
        const startDate = document.getElementById("startDate");
        const endDate = document.getElementById("endDate");
        const endDateContainer = document.getElementById("endDateContainer");
        const reservationTerm = document.getElementById("reservationTerm");
        const shortPeriodDuration = document.getElementById("shortPeriodDuration");

        // Handle the Reservation Period Type change (Long Term / Short Term)
        reservationType.addEventListener("change", function () {
            shortPeriodDetails.classList.add("d-none");
            longPeriodDetails.classList.add("d-none");
            submitReservation.disabled = true;

            if (this.value === "") {
                return;
            }

            if (this.value === "long") {
                longPeriodDetails.classList.remove("d-none");
                shortPeriodDetails.classList.add("d-none");
                shortPeriodDuration.removeAttribute("required");
                startDate.removeAttribute("required");
                // Optionally populate long-term terms dynamically
                reservationTerm.value = ""; // Reset term selection if switching
            } else if (this.value === "short") {
                shortPeriodDetails.classList.remove("d-none");
                longPeriodDetails.classList.add("d-none");
                shortPeriodDuration.setAttribute("required", "required");
                startDate.setAttribute("required", "required");
                reservationTerm.removeAttribute("required");
                // Show or hide the end date field based on short term duration
                endDateContainer.style.display = shortPeriodDuration.value === "day" ? "none" : "block";
            }
        });
        startDate.addEventListener("change", () => {
   const startDateValue = startDate.value;
   const startDateObject = new Date(startDateValue);
   
   const shortPeriodType = shortPeriodDuration.value;

   if (shortPeriodType === 'week') {
       const endDateObject = new Date(startDateObject);
       endDateObject.setDate(startDateObject.getDate() + 6);
       
       const endDateFormatted = endDateObject.toISOString().split('T')[0];
       endDate.value = endDateFormatted;
   } else if (shortPeriodType === 'month') {
       const endDateObject = new Date(startDateObject);
       endDateObject.setMonth(startDateObject.getMonth() + 1);
       endDateObject.setDate(0); // Last day of the month
       
       const endDateFormatted = endDateObject.toISOString().split('T')[0];
       endDate.value = endDateFormatted;
   }
});

        // Handle the short-term duration selection (Day/Week/Month)
        shortPeriodDuration.addEventListener("change", function () {
            endDateContainer.style.display = this.value === "day" ? "none" : "block";
            if (this.value === "day") {
                endDate.removeAttribute("required");
            } else {
                endDate.setAttribute("required", "required");
            }
            
            // Set min date to today for start date
            const today = new Date().toISOString().split('T')[0];
            startDate.setAttribute('min', today);
            
            updateSubmitButton();
        });

        // Validate Dates
        function validateDates() {
            if (reservationType.value === "short") {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (shortPeriodDuration.value === "day") {
                    const selectedDate = new Date(startDate.value);
                    return selectedDate >= today;
                }

                if (startDate.value && endDate.value) {
                    const start = new Date(startDate.value);
                    const end = new Date(endDate.value);
                    return start >= today && start < end;
                }
                return false;
            }
            return true;
        }

        // Enable submit button when all required fields are filled
        function updateSubmitButton() {
            const islongPeriod = reservationType.value === "long";
            const isshortPeriod = reservationType.value === "short";

            let isValid = false;

            if (islongPeriod) {
                isValid = reservationTerm.value !== "";
            } else if (isshortPeriod) {
                if (shortPeriodDuration.value === "day") {
                    isValid = startDate.value !== "";
                } else if (shortPeriodDuration.value) {
                    isValid = startDate.value !== "" && endDate.value !== "" && validateDates();
                }
            }

            submitReservation.disabled = !isValid;
        }

        // Add event listeners for form validation
        const formInputs = form.querySelectorAll("select, input");
        formInputs.forEach((input) => {
            input.addEventListener("change", updateSubmitButton);
            input.addEventListener("input", updateSubmitButton);
        });

        startDate.addEventListener("change", function () {
            // Prevent selection of past dates
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(this.value);
            
            if (selectedDate < today) {
                swal({
                    title: '@lang("Invalid Date")',
                    text: '@lang("Please select a present or future date")',
                    type: "warning",
                });
                this.value = '';
                updateSubmitButton();
                return;
            }

            // Update end date based on duration type
            if (shortPeriodDuration.value !== "day") {
                const startDateValue = this.value;
                const startDateObject = new Date(startDateValue);
                
                if (shortPeriodDuration.value === 'week') {
                    const endDateObject = new Date(startDateObject);
                    endDateObject.setDate(startDateObject.getDate() + 6);
                    endDate.value = endDateObject.toISOString().split('T')[0];
                } else if (shortPeriodDuration.value === 'month') {
                    const endDateObject = new Date(startDateObject);
                    endDateObject.setMonth(startDateObject.getMonth() + 1);
                    endDateObject.setDate(0);
                    endDate.value = endDateObject.toISOString().split('T')[0];
                }
            }
            
            updateSubmitButton();
        });

        endDate.addEventListener("change", function () {
            if (startDate.value) {
                validateDates();
            }
            updateSubmitButton();
        });

        // Submit Form Handling (for demonstration)
        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                return;
            }

            const submitBtn = document.getElementById("submitReservation");
            const spinner = document.getElementById("submitSpinner");
            const submitText = document.getElementById("submitText");

            // Disable button and show loading state
            submitBtn.disabled = true;
            spinner.classList.remove("d-none");
            submitText.textContent = '@lang("Processing...")';

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        Accept: "application/json",
                    },
                });

                const data = await response.json();

                if (response.ok) {
                    await swal({
                        title: '@lang("Success")',
                        text: data.message || '@lang("Reservation has been added successfully!")',
                        type: "success",
                    });

                    // Reset form and close modal
                    form.reset();
                    $("#addReservationModal").modal("hide");

                    // Optionally reload the page or update the UI
                    window.location.reload();
                } else {
                    throw new Error(data.message || '@lang("Something went wrong!")');
                }
            } catch (error) {
                await swal({
                    title: '@lang("Error")',
                    text: error.message,
                    type: "error",
                });
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                spinner.classList.add("d-none");
                submitText.textContent = '@lang("Add Reservation")';
            }
        });
    });
</script>
@endsection
