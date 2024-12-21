@extends('layouts.student')

@section('title', __('pages.student.maintenance.title'))
@section('links')
<style>
    /* Style for the card */
    .issue-card {
        position: relative;
        transition: all 0.3s ease;
    }

    .issue-card:hover {
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

    /* Style for issue image */
    .issue-img {
        max-height: 160px;
        object-fit: contain;
    }

    /* Additional info input style */
    .additional-info {
        display: none;
    }

    /* Upload Zone */
.upload-zone {
    border: 2px dashed #007bff;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    color: #6c757d;
    background-color: #f8f9fa;
}

.upload-zone:hover {
    background-color: #e9ecef;
    border-color: #0056b3;
}

.upload-text {
    font-size: 16px;
    margin: 0;
}

.upload-link {
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
}

.upload-zone.dragging {
    background-color: #e3f2fd;
    border-color: #007bff;
}

/* Thumbnail Preview */
.thumbnail-wrapper {
    display: inline-block;
    max-width: 100%;
    position: relative;
    margin-top: 10px;
    text-align: center;
}

.thumbnail {
    width: 150px;
    height: 150px;
    object-fit: cover;
    display: block;
    margin: 0 auto;
}


</style>
@endsection
@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="p-4 border rounded shadow-sm bg-white">
                <form id="maintenanceRequestForm" method="POST" enctype="multipart/form-data">
                @csrf

                    <!-- Issue Type Selection with Cards -->
                    <div class="row mb-4">
                        <!-- Water and Sanitary Issues Card -->
                        <div class="col-12 col-md-4 mb-3">
                            <div class="card text-center border-primary issue-card" id="water_sanitary_card" style="cursor: pointer;">
                                <div class="card-overlay">
                                    <i class="fa fa-check check-icon"></i>
                                </div>
                                <img src="{{ asset('images/maintenance/water-issues-icon.svg') }}" alt="@lang('pages.student.maintenance.water_sanitary')" class="card-img-top p-3 issue-img">
                                <div class="card-body">
                                    <h5 class="card-title">@lang('pages.student.maintenance.water_sanitary')</h5>
                                </div>
                            </div>
                        </div>
                        <!-- Electrical Issues Card -->
                        <div class="col-12 col-md-4 mb-3">
                            <div class="card text-center border-primary issue-card" id="electrical_card" style="cursor: pointer;">
                                <div class="card-overlay">
                                    <i class="fa fa-check check-icon"></i>
                                </div>
                                <img src="{{ asset('images/maintenance/electrican-issues-icon.svg') }}" alt="@lang('pages.student.maintenance.electrical')" class="card-img-top p-3 issue-img">
                                <div class="card-body">
                                    <h5 class="card-title">@lang('pages.student.maintenance.electrical')</h5>
                                </div>
                            </div>
                        </div>
                        <!-- General Housing Issues Card -->
                        <div class="col-12 col-md-4 mb-3">
                            <div class="card text-center border-primary issue-card" id="housing_card" style="cursor: pointer;">
                                <div class="card-overlay">
                                    <i class="fa fa-check check-icon"></i>
                                </div>
                                <img src="{{ asset('images/maintenance/general-issues-icon.svg') }}" alt="@lang('pages.student.maintenance.general_housing')" class="card-img-top p-3 issue-img">
                                <div class="card-body">
                                    <h5 class="card-title">@lang('pages.student.maintenance.general_housing')</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Issue Details (Conditionally displayed) -->
                    <div id="issue_details" class="mb-3">
                        <!-- Water & Sanitary Issues -->
                        <div id="water_sanitary_issues" class="d-none">
                            <h5><i class="fa fa-tint"></i> @lang('pages.student.maintenance.water_sanitary_issues')</h5>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="water_issues[]" value="leakage" id="leakage">
                                <label class="form-check-label" for="leakage">@lang('pages.student.maintenance.water_leakage')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="water_issues[]" value="sewage_problem" id="sewage_problem">
                                <label class="form-check-label" for="sewage_problem">@lang('pages.student.maintenance.sewage_problem')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="water_issues[]" value="plumbing_problem" id="plumbing_problem">
                                <label class="form-check-label" for="plumbing_problem">@lang('pages.student.maintenance.plumbing_problem')</label>
                            </div>
                        </div>

                        <!-- Electrical Issues -->
                        <div id="electrical_issues" class="d-none">
                            <h5><i class="fa fa-plug"></i> @lang('pages.student.maintenance.electrical_issues')</h5>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="electrical_issues[]" value="bulb_replacement" id="bulb_replacement">
                                <label class="form-check-label" for="bulb_replacement">@lang('pages.student.maintenance.bulb_replacement')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="electrical_issues[]" value="fan_issue" id="fan_issue">
                                <label class="form-check-label" for="fan_issue">@lang('pages.student.maintenance.fan_issue')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="electrical_issues[]" value="water_heater_issue" id="water_heater_issue">
                                <label class="form-check-label" for="water_heater_issue">@lang('pages.student.maintenance.water_heater_issue')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="electrical_issues[]" value="electricity_problem" id="electricity_problem">
                                <label class="form-check-label" for="electricity_problem">@lang('pages.student.maintenance.electricity_problem')</label>
                            </div>
                        </div>

                        <!-- General Housing Issues -->
                        <div id="housing_issues" class="d-none">
                            <h5><i class="fa fa-couch"></i> @lang('pages.student.maintenance.general_housing_issues')</h5>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="housing_issues[]" value="furniture_damage" id="furniture_damage">
                                <label class="form-check-label" for="furniture_damage">@lang('pages.student.maintenance.furniture_damage')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="housing_issues[]" value="appliance_issue" id="appliance_issue">
                                <label class="form-check-label" for="appliance_issue">@lang('pages.student.maintenance.appliance_issue')</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="housing_issues[]" value="door_window_issue" id="door_window_issue">
                                <label class="form-check-label" for="door_window_issue">@lang('pages.student.maintenance.door_window_issue')</label>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Text Input -->
                    <div id="additionalInfoDiv" class="additional-info form-group">
                        <label for="additional_info">@lang('pages.student.maintenance.additional_info')</label>
                        <textarea class="form-control" id="additional_info" name="additional_info" rows="4" placeholder="@lang('pages.student.maintenance.enter_additional_details')"></textarea>
                    </div>

                    <div id="uploadZone" class="upload-zone">
    <p class="upload-text">@lang('pages.student.maintenance.upload_text')</p>
    <input type="file" id="image" name="image" accept="image/*" class="d-none">
</div>
<div id="imagePreview" class="mt-3 d-none">
    <div class="thumbnail-wrapper position-relative">
        <img id="previewImage" alt="@lang('pages.student.maintenance.uploaded_image')" class="thumbnail border rounded">
        <button id="removeImageBtn" type="button" class="btn btn-round btn-danger"><i class="feather icon-trash-2"></i></button>
        </div>
</div>

                    <!-- Submit Button -->
                    <div class="text-center">
    <button type="submit" class="btn btn-primary btn-lg mt-3">
        <i class="bi bi-send-fill"></i> @lang('pages.student.maintenance.submit_request')
    </button>
</div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pages/student-maintenance.js') }}"></script>
<script>
    window.routes = {
        maintenanceStore: "{{ route('student.maintenance.store') }}", // Corrected with ":" instead of "="
    };
</script>
@endsection
