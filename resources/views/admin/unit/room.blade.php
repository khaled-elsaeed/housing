@extends('layouts.admin')
@section('title', 'Rooms')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
<style>
    .loading {
        pointer-events: none; /* Disable button interactions */
    }
</style>
@endsection

@section('content')
<!-- Start row for Room View -->
<div class="row">
    <!-- Start col for Single Rooms -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                    </div>
                    <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Single Rooms</h5>
                        <h4 class="mb-0">{{ $singleRoomCount }}</h4> <!-- Total Number of Single Rooms -->
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-5 text-start">
                        <span class="font-13">Occupied</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> Males: {{ $singleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> Females: {{ $singleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-5 text-start">
                        <span class="font-13">Empty</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> Males: {{ $singleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> Females: {{ $singleRoomEmptyFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->

    <!-- Start col for Double Rooms -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-users"></i></span>
                    </div>
                    <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Double Rooms</h5>
                        <h4 class="mb-0">{{ $doubleRoomCount }}</h4> <!-- Total Number of Double Rooms -->
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-5 text-start">
                        <span class="font-13">Occupied</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> Males: {{ $doubleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> Females: {{ $doubleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-start">
                        <span class="font-13">Partially Occupied</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-check text-warning"></i> Males: {{ $doubleRoomPartiallyOccupiedMales }}</span>
                        <span class="font-13"><i class="feather icon-user-check text-warning"></i> Females: {{ $doubleRoomPartiallyOccupiedFemales }}</span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-start">
                        <span class="font-13">Empty</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> Males: {{ $doubleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> Females: {{ $doubleRoomEmptyFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->

    <!-- Start col for Under Maintenance -->
    <div class="col-lg-4 col-md-6 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-wrench"></i></span>
                    </div>
                    <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Under Maintenance</h5>
                        <h4 class="mb-0">{{ $underMaintenanceCount }}</h4> <!-- Total Number of Rooms Under Maintenance -->
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="font-13">Single</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-x text-danger"></i> Males: {{ $underMaintenanceSingleMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-x text-danger"></i> Females: {{ $underMaintenanceSingleFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="font-13">Double</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> Males: {{ $underMaintenanceDoubleMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> Females: {{ $underMaintenanceDoubleFemales }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End col -->
</div>




<!-- End row for Room View -->

<div class="row">
    <div class="col-lg-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <!-- Title on the Left -->
            <h2 class="page-title text-primary mb-2 mb-md-0">Rooms</h2>
            <!-- Toggle Button on the Right -->
            <div class="div">
                <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-search-plus"></i>
                </button>
                <div class="btn-group ms-2" role="group" aria-label="Download Options">
               <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn"  data-bs-toggle="dropdown" aria-expanded="false">
               <i class="fa fa-download"></i> Download
               </button>
               <ul class="dropdown-menu">
                  <li>
                     <a class="dropdown-item" href="#" id="exportExcel">
                     <i class="fa fa-file-excel"></i> Rooms (Excel)
                     </a>
                  </li>
               </ul>
            </div>
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
                <input type="search" class="form-control search-input" id="searchBox" placeholder="Search..." />
            </div>
            <!-- Filters on the Right -->
            <div class="d-flex flex-column flex-md-row filters-container">
            <select id="buildingFilter" class="form-select mb-2 mb-md-0">
    <option value="">Select Building</option>
    @foreach($buildingNumbers as $buildingNumber)
        <option value="Building {{ $buildingNumber }}">Building {{ $buildingNumber }}</option>
    @endforeach
</select>

<select id="apartmentFilter" class="form-select mb-2 mb-md-0">
    <option value="">Select Apartment</option>
    @foreach($apartmentNumbers as $apartmentNumber)
        <option value="Apartment {{ $apartmentNumber }}">Apartment {{ $apartmentNumber }}</option>
    @endforeach
</select>

                <select id="statusFilter" class="form-select mb-2 mb-md-0">
                    <option value="">Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Under maintenance">Under maintenance</option>
                </select>
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
                                <th>Room Number</th>
                                <th>Apartment</th>
                                <th>Building Number</th>
                                <th>Room Purpose</th>
                                <th>Type</th>
                                <th>Status</th>

                                <th> Max Occupancy </th>
                                <th> Current Occupancy </th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td>Room {{ $room->number }}</td>
                                    <td>Apartment {{ $room->apartment->number }}</td>
                                    <td>Building {{ $room->apartment->building->number }}</td>
                                    <td>{{ $room->purpose }}</td>
                                    <td>{{ $room->type }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $room->status)) }}</td>

                                    <td>{{ $room->max_occupancy }} students</td>
                                    <td>{{ $room->current_occupancy }} student</td>

                                    <td>{{ $room->note ?: 'No description available' }}</td>
                                    <td>
                                        <!-- Edit Note Button -->
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-{{ $room->id }}" title="Edit Note">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        
                                        <!-- Edit Status Button -->
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $room->id }}" title="Edit Status">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        
                                        <!-- Delete Apartment Button -->
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $room->id }}" title="Delete room">
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
<!-- Edit Status Modal -->
<div class="modal fade" id="editRoomStatusModal" tabindex="-1" aria-labelledby="editRoomStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRoomStatusModalLabel">Edit Room Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStatusForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Status -->
                    <div class="mb-3">
                        <label for="editRoomStatus" class="form-label">Status</label>
                        <select class="form-control border border-primary" id="editRoomStatus" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="under_maintenance">Under Maintenance</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveStatusBtn">Save Status</button>
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
                <h5 class="modal-title" id="editRoomNoteModalLabel">Edit/Add Room Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoteForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Note -->
                    <div class="mb-3">
                        <label for="editRoomNote" class="form-label">Note</label>
                        <textarea class="form-control border border-primary" id="editRoomNote" name="note" rows="4" required></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveNoteBtn">Save Note</button>
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
<script src="{{ asset('js/custom/custom-table-datatable.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/pages/rooms.js') }}"></script>
<script>
   
window.routes = {
    exportExcel : '{{ route('admin.unit.room.export-excel') }}',
    saveRoom: '{{ route('admin.unit.room.store') }}',
    deleteRoom: '{{ route('admin.unit.room.destroy', ':id') }}',
    updateRoomStatus: '{{ route('admin.unit.room.update-status') }}', 
    updateRoomNote: '{{ route('admin.unit.room.update-note') }}'    
};


</script>
@endsection
