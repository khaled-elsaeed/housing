@extends('layouts.admin')

@section('title', 'System Settings - NMU Housing System')

@section('content')
<!-- Start row -->
<div class="row">
    <!-- Start col for settings content -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-light rounded">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">System Settings</h5>
            </div>
            <div class="card-body">
                <!-- Horizontal Tabs -->
                <ul class="nav nav-pills mb-4" id="settings-tabs" role="tablist">
                    <!-- Reservation Settings Tab -->
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="reservation-settings-tab" data-bs-toggle="pill" href="#reservation-settings" role="tab" aria-controls="reservation-settings" aria-selected="true">
                            <i class="feather icon-calendar me-2"></i> Reservation Settings
                        </a>
                    </li>
                    <!-- Other Settings Tab -->
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="other-settings-tab" data-bs-toggle="pill" href="#other-settings" role="tab" aria-controls="other-settings" aria-selected="false">
                            <i class="feather icon-settings me-2"></i> Other Settings
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="settings-tab-content">
                    <!-- Reservation Settings Tab Content -->
                    <div class="tab-pane fade show active" id="reservation-settings" role="tabpanel" aria-labelledby="reservation-settings-tab">
                        <div class="card shadow-sm border-secondary rounded">
                            <div class="card-header bg-secondary">
                                <h5 class="card-title mb-0 text-white">Reservation Settings</h5>
                            </div>
                            <div class="card-body">
                                <!-- Form to update reservation settings -->
                                <form action="{{ route('admin.setting.update-reservation') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="reservation_start_datetime" class="form-label">Reservation Start Date & Time</label>
                                            <input type="datetime-local" class="form-control border-primary" id="reservation_start_datetime" name="reservation_start_datetime" 
                                                   value="{{ old('reservation_start_datetime', $settings['reservation_start_datetime'] ?? '') }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="reservation_end_datetime" class="form-label">Reservation End Date & Time</label>
                                            <input type="datetime-local" class="form-control border-primary" id="reservation_end_datetime" name="reservation_end_datetime" 
                                                   value="{{ old('reservation_end_datetime', $settings['reservation_end_datetime'] ?? '') }}" required>
                                        </div>

                                        <div class="col-md-12">
                                            <label for="reservation_status" class="form-label">Reservation Status</label>
                                            <select class="form-select border-primary" id="reservation_status" name="reservation_status" required onchange="toggleEligibleField()">
                                                <option value="open" {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="closed" {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12" id="eligible_students_field" style="display: {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'open' ? 'block' : 'none' }};">
                                            <label for="eligible_students" class="form-label">Eligible Students</label>
                                            <select class="form-select border-primary" id="eligible_students" name="eligible_students" required>
                                                <option value="new" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'new' ? 'selected' : '' }}>New Students</option>
                                                <option value="old" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'old' ? 'selected' : '' }}>old Students</option>
                                                <option value="all" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'all' ? 'selected' : '' }}>All Students</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary mt-3">Save Reservation Settings</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Reservation Settings Tab -->

                    <!-- Other Settings Tab Content -->
                    <div class="tab-pane fade" id="other-settings" role="tabpanel" aria-labelledby="other-settings-tab">
                        <!-- Content for Other Settings -->
                        <div class="card shadow-sm border-light rounded">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Other Settings</h5>
                            </div>
                            <div class="card-body">
                                <!-- Add form or content for Other Settings here -->
                            </div>
                        </div>
                    </div>
                    <!-- End Other Settings Tab -->
                </div>
            </div>
        </div>
    </div>
    <!-- End col for settings content -->
</div>
<!-- End row -->

<!-- JavaScript to toggle eligible students field -->
<script>
    function toggleEligibleField() {
        const reservationStatus = document.getElementById('reservation_status').value;
        const eligibleStudentsField = document.getElementById('eligible_students_field');

        if (reservationStatus === 'open') {
            eligibleStudentsField.style.display = 'block';
        } else {
            eligibleStudentsField.style.display = 'none';
        }
    }

    // Call the function on page load to handle pre-filled values
    document.addEventListener('DOMContentLoaded', toggleEligibleField);
</script>
@endsection
