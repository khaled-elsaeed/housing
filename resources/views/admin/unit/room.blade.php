@extends('layouts.admin')
@section('title', __('pages.admin.rooms.total_rooms'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

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
    <!-- Start col for Total Rooms (This will span the whole row) -->
    <div class="col-lg-12 mb-2">
        <div class="card m-b-30">
            <div class="card-body">
                <div class="row align-items-center">
                   
                    <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.rooms.total_rooms') }}</h5>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.rooms.single_rooms') }}</h5>
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
                        <span class="font-13">{{ __('pages.admin.rooms.occupied') }}</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> {{ __('pages.admin.rooms.males') }}: {{ $singleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> {{ __('pages.admin.rooms.females') }}: {{ $singleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">{{ __('pages.admin.rooms.empty') }}</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.males') }}: {{ $singleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.females') }}: {{ $singleRoomEmptyFemales }}</span>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.rooms.double_rooms') }}</h5>
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
                        <span class="font-13">{{ __('pages.admin.rooms.occupied') }}</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-check text-success"></i> {{ __('pages.admin.rooms.males') }}: {{ $doubleRoomOccupiedMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-check text-success"></i> {{ __('pages.admin.rooms.females') }}: {{ $doubleRoomOccupiedFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">{{ __('pages.admin.rooms.partially_occupied') }}</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-check text-warning"></i> {{ __('pages.admin.rooms.males') }}: {{ $doubleRoomPartiallyOccupiedMales }}</span>
                        <span class="font-13"><i class="feather icon-user-check text-warning"></i> {{ __('pages.admin.rooms.females') }}: {{ $doubleRoomPartiallyOccupiedFemales }}</span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-5 text-end">
                        <span class="font-13">{{ __('pages.admin.rooms.empty') }}</span>
                    </div>
                    <div class="col-7 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.males') }}: {{ $doubleRoomEmptyMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.females') }}: {{ $doubleRoomEmptyFemales }}</span>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.rooms.under_maintenance') }}</h5>
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
                        <span class="font-13">{{ __('pages.admin.rooms.room_type.single') }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2">
                            <i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.males') }}: {{ $underMaintenanceSingleMales }}
                        </span>
                        <span class="font-13">
                            <i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.females') }}: {{ $underMaintenanceSingleFemales }}
                        </span>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.rooms.room_type.double') }}</span>
                    </div>
                    <div class="col-6 text-end">
                        <span class="font-13 me-2"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.males') }}: {{ $underMaintenanceDoubleMales }}</span>
                        <span class="font-13"><i class="feather icon-user-x text-danger"></i> {{ __('pages.admin.rooms.females') }}: {{ $underMaintenanceDoubleFemales }}</span>
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
            <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('pages.admin.rooms.total_rooms') }}</h2>
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
                        <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.rooms.search') }}" />
                    </div>
                    <!-- Filters on the Right -->
                    <div class="d-flex flex-column flex-md-row filters-container">
                    <select id="buildingFilter" class="form-select mb-2 mb-md-0">
                        <option value="">{{ __('pages.admin.rooms.select_building') }}</option>
                        @foreach($buildingNumbers as $buildingNumber)
                            <option value="{{ __('pages.admin.apartment.building') }} {{ $buildingNumber }}">
                                {{ __('pages.admin.apartment.building') }} {{ $buildingNumber }}
                            </option>
                        @endforeach
                    </select>

                    <select id="apartmentFilter" class="form-select mb-2 mb-md-0">
                        <option value="">{{ __('pages.admin.rooms.select_apartment') }}</option>
                        @foreach($apartmentNumbers as $apartmentNumber)
                            <option value="{{ __('pages.admin.rooms.apartment') }} {{ $apartmentNumber }}">
                                {{ __('pages.admin.rooms.apartment') }} {{ $apartmentNumber }}
                            </option>
                        @endforeach
                    </select>

                    <select id="statusFilter" class="form-select mb-2 mb-md-0">
                        <option value="">{{ __('pages.admin.rooms.status') }}</option>
                        <option value="Active">{{ __('pages.admin.rooms.active') }}</option>
                        <option value="Inactive">{{ __('pages.admin.rooms.inactive') }}</option>
                        <option value="Under maintenance">{{ __('pages.admin.rooms.under_maintenance_status') }}</option>
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
                                <th>{{ __('pages.admin.rooms.room_number') }}</th>
                                <th>{{ __('pages.admin.rooms.apartment') }}</th>
                                <th>{{ __('pages.admin.rooms.building_number') }}</th>
                                <th>{{ __('pages.admin.rooms.room_purpose') }}</th>
                                <th>{{ __('pages.admin.rooms.type') }}</th>
                                <th>{{ __('pages.admin.rooms.status') }}</th>
                                <th>{{ __('pages.admin.rooms.max_occupancy') }}</th>
                                <th>{{ __('pages.admin.rooms.current_occupancy') }}</th>
                                <th>{{ __('pages.admin.rooms.description') }}</th>
                                <th>{{ __('pages.admin.rooms.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td>{{ __('pages.admin.rooms.room') }} {{ $room->number ?? __('pages.admin.rooms.n/a') }}</td>
                                    <td>{{ __('pages.admin.rooms.apartment') }} {{ $room->apartment->number ?? __('pages.admin.rooms.n/a') }}</td>
                                    <td>{{ __('pages.admin.apartment.building') }} {{ $room->apartment->building->number ?? __('pages.admin.rooms.n/a') }}</td>
                                    <td>{{ __('pages.admin.rooms.room_purposes.' . $room->purpose) }}</td>
                                    <td>{{ __('pages.admin.rooms.room_type.' . $room->type) }}</td>
                                    <td>{{ __('pages.admin.rooms.room_status.' . strtolower($room->status)) }}</td>

                                    <td>{{ $room->max_occupancy }} {{ __('pages.admin.rooms.students') }}</td>
                                    <td>{{ $room->current_occupancy }} {{ __('pages.admin.rooms.student') }}</td>
                                    <td>{{ $room->note ?: __('pages.admin.rooms.no_description') }}</td>
                                    <td>
                                        <!-- Edit Note Button -->
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-{{ $room->id }}" title="{{ __('pages.admin.rooms.edit_note') }}">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        
                                        <!-- Edit Status Button -->
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $room->id }}" title="{{ __('pages.admin.rooms.edit_status') }}">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        
                                        <!-- Delete Apartment Button -->
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $room->id }}" title="{{ __('pages.admin.rooms.delete_room') }}">
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
                <h5 class="modal-title" id="editRoomDetailsModalLabel">{{ __('pages.admin.rooms.edit_room_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editRoomDetailsForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Status -->
                    <div class="mb-3">
                        <label for="editRoomStatus" class="form-label">{{ __('pages.admin.rooms.status') }}</label>
                        <select class="form-control border border-primary" id="editRoomStatus" name="status" required>
                            <option value="active">{{ __('pages.admin.rooms.room_status.active') }}</option>
                            <option value="inactive">{{ __('pages.admin.rooms.room_status.inactive') }}</option>
                            <option value="under_maintenance">{{ __('pages.admin.rooms.room_status.under_maintenance') }}</option>
                        </select>
                    </div>

                    <!-- Room Purpose -->
                    <div class="mb-3">
                        <label for="editRoomPurpose" class="form-label">{{ __('pages.admin.rooms.room_purpose') }}</label>
                        <select class="form-control border border-primary" id="editRoomPurpose" name="purpose" required>
                            <option value="accommodation">{{ __('pages.admin.rooms.room_purposes.accommodation') }}</option>
                            <option value="office">{{ __('pages.admin.rooms.room_purposes.office') }}</option>
                            <option value="utility">{{ __('pages.admin.rooms.room_purposes.utility') }}</option>
                        </select>
                    </div>

                    <!-- Room Type -->
                    <div class="mb-3">
                        <label for="editRoomType" class="form-label">{{ __('pages.admin.rooms.type') }}</label>
                        <select class="form-control border border-primary" id="editRoomType" name="type" required>
                            <option value="single">{{ __('pages.admin.rooms.room_type.single') }}</option>
                            <option value="double">{{ __('pages.admin.rooms.room_type.double') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.rooms.close') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveDetailsBtn">{{ __('pages.admin.rooms.save_changes') }}</button>
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
                <h5 class="modal-title" id="editRoomNoteModalLabel">{{ __('pages.admin.rooms.edit_add_room_note') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoteForm">
                <!-- Modal Content -->
                <div class="modal-body">
                    <!-- Room Note -->
                    <div class="mb-3">
                        <label for="editRoomNote" class="form-label">{{ __('pages.admin.rooms.note') }}</label>
                        <textarea class="form-control border border-primary" id="editRoomNote" name="note" rows="4" required></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.rooms.close') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveNoteBtn">{{ __('pages.admin.rooms.save_note') }}</button>
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