@extends('layouts.student')

@section('title', 'Permission Request')

@section('links')
<style>
    /* Style for the card */
    .permission-card {
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        margin-top: 50px;
    }

    /* Hover effect for the card */
    .permission-card:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Overlay and checkmark icon */
    .card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(140, 47, 57, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 0.9;
    }

    .card-overlay.show {
        display: flex;
    }

    .check-icon {
        font-size: 40px;
        color: white;
    }

    /* Active Card Style */
    .active-card {
        border-color: #8C2F39;
    }

    /* Style for permission image */
    .permission-img {
        max-height: 160px;
        object-fit: contain;
    }

    /* Form styles */
    .form-group {
        margin-bottom: 15px;
    }

    .additional-info {
        display: none;
    }

    /* Center the form */
    .center-form {
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="p-4 border rounded shadow-sm bg-white">

                <!-- Late Permission Card (Center-aligned) -->
                <div class="row justify-content-center">
                    <div class="col-12 col-md-6 mb-3">
                        <div class="card text-center border-primary permission-card" id="late_permission_card">
                            <img src="{{ asset('images/permission/late-icon.svg') }}" alt="Late Permission" class="card-img-top p-3 permission-img">
                            <div class="card-body">
                                <h5 class="card-title">Late Permission</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Late Permission Form (Always visible) -->
                <form id="permissionRequestForm" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div id="late_permission">
                        <input type="text" value="late_permission" name="permission_type" hidden >
                        <h5><i class="fa fa-clock"></i> Late Permission</h5>
                        <div class="form-group">
                            <label for="late_date">Date for Late Arrival</label>
                            <input type="date" class="form-control" id="late_date" name="late_date" required>
                        </div>
                        <div class="form-group">
                            <label for="late_time">Expected Arrival Time</label>
                            <input type="time" class="form-control" id="late_time" name="late_time" required>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason for Late Arrival</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Explain why you will arrive late" required></textarea>
                        </div>
                    </div>

                    <!-- Additional Information (Optional) -->
                    <div id="additionalInfoDiv" class="additional-info form-group">
                        <label for="additional_info">Additional Information</label>
                        <textarea class="form-control" id="additional_info" name="additional_info" rows="4" placeholder="Enter any additional details about the request"></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg mt-3">
                            <i class="bi bi-send-fill"></i> Submit Request
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pages/student-permission.js') }}?v={{ config('app.version') }}"></script>
<script>
    Window.routes = {
        permissionStore: "{{ route('student.permission.store') }}",
    }
</script>
@endsection
