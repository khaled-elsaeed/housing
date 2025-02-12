@extends('layouts.admin')
@section('title', __('pages.admin.rooms.total_rooms'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content')
<!-- Start row for Room View -->
<div class="row">
    <!-- Start col for Total Rooms (This will span the whole row) -->
    <div class="col-lg-12 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Total Rooms')</h5>
                        <h4 class="mb-0">{{ $totalRoomsCount }}</h4> <!-- Total Number of Rooms -->
                    </div>
                    <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col for Total Rooms -->

    <!-- Start col for Single Rooms -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Single Rooms')</h5>
                        <h4 class="mb-0">{{ $singleRoomCount }}</h4> <!-- Total Number of Single Rooms -->
                    </div>
                    <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">@lang('Occupied')</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> @lang('Males'): {{ $singleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> @lang('Females'): {{ $singleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">@lang('Empty')</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> @lang('Males'): {{ $singleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> @lang('Females'): {{ $singleRoomEmptyFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col for Single Rooms -->

    <!-- Start col for Double Rooms -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Double Rooms')</h5>
                        <h4 class="mb-0">{{ $doubleRoomCount }}</h4> <!-- Total Number of Double Rooms -->
                    </div> 
                    <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-users"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">@lang('Occupied')</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> @lang('Males'): {{ $doubleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> @lang('Females'): {{ $doubleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">@lang('Partially Occupied')</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-check text-warning"></i> @lang('Males'): {{ $doubleRoomPartiallyOccupiedMales }}</span>
                        <span class="font-13"><i class="feather icon-user-check text-warning"></i> @lang('Females'): {{ $doubleRoomPartiallyOccupiedFemales }}</span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">@lang('Empty')</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> @lang('Males'): {{ $doubleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> @lang('Females'): {{ $doubleRoomEmptyFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col for Double Rooms -->

    <!-- Start col for Under Maintenance -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Under Maintenance')</h5>
                        <h4 class="mb-0">{{ $underMaintenanceCount }}</h4> <!-- Total Number of Rooms Under Maintenance -->
                    </div>
                    <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-wrench"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="font-13">@lang('Single Rooms')</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-x text-danger"></i> @lang('Males'): {{ $underMaintenanceSingleMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-x text-danger"></i> @lang('Females'): {{ $underMaintenanceSingleFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="font-13">@lang('Double Rooms')</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> @lang('Males'): {{ $underMaintenanceDoubleMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> @lang('Females'): {{ $underMaintenanceDoubleFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col for Under Maintenance -->
</div>
<!-- End row -->

<div class="row">
    <div class="col-lg-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <!-- Title on the Left -->
            <h2 class="page-title text-primary mb-2 mb-md-0">@lang('Total Rooms')</h2>
            <!-- Toggle Button on the Right -->
            <div class="div">
                <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-search-plus"></i>
                </button>
            </div>
        </div>

        <div class="collapse" id="collapseExample">
            <div class="search-filter-container card card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <!-- Search Box with Icon on the Left -->
                    <div class="search-container d-flex align-items-center mb-3 mb-md-0">
                        <div class="search-icon-container">
                            <i class="fa fa-search search-icon"></i>
                        </div>
                        <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search')" />
                    </div>
                    <!-- Filters on the Right -->
                    <div class="d-flex flex-column flex-md-row filters-container">
                        <select id="buildingFilter" class="form-select mb-2 mb-md-0">
                            <option value="">@lang('Select Building')</option>
                            @foreach($buildingNumbers as $buildingNumber)
                                <option value="Building {{ $buildingNumber }}">
                                    @lang('Building') {{ $buildingNumber }}
                                </option>
                            @endforeach
                        </select>

                        <select id="apartmentFilter" class="form-select mb-2 mb-md-0">
                            <option value="">@lang('Select Apartment')</option>
                            @foreach($apartmentNumbers as $apartmentNumber)
                                <option value="Apartment {{ $apartmentNumber }}">
                                    @lang('Apartment') {{ $apartmentNumber }}
                                </option>
                            @endforeach
                        </select>

                        <select id="statusFilter" class="form-select mb-2 mb-md-0">
                            <option value="">@lang('Status')</option>
                            <option value="Active">@lang('Active')</option>
                            <option value="Inactive">@lang('Inactive')</option>
                            <option value="Under maintenance">@lang('Under Maintenance')</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Start row -->
<div class="row">
    <!-- Start col -->
    <div class="col-lg-12">
        <div class="card m-b-30 table-card">
            <div class="card-body table-container">
                <div class="table-responsive">
                    <table id="default-datatable" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('Room Number')</th>
                                <th>@lang('Apartment')</th>
                                <th>@lang('Building Number')</th>
                                <th>@lang('Room Purpose')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Max Occupancy')</th>
                                <th>@lang('Current Occupancy')</th>
                                <th>@lang('Description')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td>@lang('Room') {{ $room->number ?? 'N/A' }}</td>
                                    <td>@lang('Apartment') {{ $room->apartment->number ?? 'N/A' }}</td>
                                    <td>@lang('Building') {{ $room->apartment->building->number ?? 'N/A' }}</td>
                                    <td>{{ $room->purpose }}</td>
                                    <td>{{ $room->type }}</td>
                                    <td>{{ $room->status }}</td>

                                    <td>{{ $room->max_occupancy }} @lang('Students')</td>
                                    <td>{{ $room->current_occupancy }} @lang('Student')</td>
                                    <td>{{ $room->note ?: __('No description available') }}</td>
                                    <td>
                                        <!-- Edit Note Button -->
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-{{ $room->id }}" title="@lang('Edit Note')">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                      
                                        <!-- Edit Status Button -->
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $room->id }}" title="@lang('Edit Status')">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        
                                        <!-- Delete Room Button -->
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $room->id }}" title="@lang('Delete Room')">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->
</div>
<!-- End row -->


<!-- Edit Room Details Modal -->
<div class="modal fade" id="editRoomDetailsModal" tabindex="-1" aria-labelledby="editRoomDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomDetailsModalLabel">@lang('Edit Room Details')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <form id="editRoomDetailsForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Status -->
                    <div class="mb-3">
                        <label for="editRoomStatus" class="form-label">@lang('Room Status')</label>
                        <select class="form-control border border-primary" id="editRoomStatus" name="status" required>
                            <option value="active">@lang('Active')</option>
                            <option value="inactive">@lang('Inactive')</option>
                            <option value="under_maintenance">@lang('Under Maintenance')</option>
                        </select>
                    </div>

                    <!-- Room Purpose -->
                    <div class="mb-3">
                        <label for="editRoomPurpose" class="form-label">@lang('Room Purpose')</label>
                        <select class="form-control border border-primary" id="editRoomPurpose" name="purpose" required>
                            <option value="accommodation">@lang('Accommodation')</option>
                            <option value="office">@lang('Office')</option>
                            <option value="utility">@lang('Utility')</option>
                        </select>
                    </div>

                    <!-- Room Type -->
                    <div class="mb-3">
                        <label for="editRoomType" class="form-label">@lang('Room Type')</label>
                        <select class="form-control border border-primary" id="editRoomType" name="type" required>
                            <option value="single">@lang('Single')</option>
                            <option value="double">@lang('Double')</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary" id="saveDetailsBtn">@lang('Save Changes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit/Add Note Modal -->
<div class="modal fade" id="editRoomNoteModal" tabindex="-1" aria-labelledby="editRoomNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomNoteModalLabel">@lang('Edit/Add Room Note')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <form id="editNoteForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Note -->
                    <div class="mb-3">
                        <label for="editRoomNote" class="form-label">@lang('Room Note')</label>
                        <textarea class="form-control border border-primary" id="editRoomNote" name="note" rows="4" required></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary" id="saveNoteBtn">@lang('Save Note')</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/pages/rooms.js') }}"></script>
<script>
   
window.routes = {
    exportExcel : '{{ route('admin.unit.room.export-excel') }}',
    saveRoom: '{{ route('admin.unit.room.store') }}',
    deleteRoom: '{{ route('admin.unit.room.destroy', ':id') }}',
    updateRoomStatus: '{{ route('admin.unit.room.update-details') }}', 
    updateRoomNote: '{{ route('admin.unit.room.update-note') }}'    
};


</script>
@endsection