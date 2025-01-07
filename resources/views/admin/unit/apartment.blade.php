@extends('layouts.admin')

@section('title', __('Apartments'))

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
<!-- Start row -->
<div class="row">
   <!-- Start col -->
   <div class="col-lg-12">
      <!-- Start row -->
      <div class="row">
         <!-- Start col for Total Apartments -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                    
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Total Apartments')</h5>
                        <h4 class="mb-0">{{ $totalApartments }}</h4>
                     </div>
                      <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-home"></i></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Male Apartments -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                      <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Male Apartments')</h5>
                        <h4 class="mb-0">{{ $maleApartmentCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-user-check"></i></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Female Apartments -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                      <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Female Apartments')</h5>
                        <h4 class="mb-0">{{ $femaleApartmentCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-user-x"></i></span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Under Maintenance -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Under Maintenance')</h5>
                        <h4 class="mb-0">{{ $maintenanceCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Males')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $maleUnderMaintenanceCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">@lang('Females')</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $femaleUnderMaintenanceCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
      </div>
      <!-- End row -->
   </div>
   <!-- End col -->
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
            <h2 class="page-title text-primary mb-2 mb-md-0">@lang('Apartments')</h2> <!-- Updated title -->
            <div>
                <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-search-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>


<div class="collapse" id="collapseExample">
    <div class="search-filter-container card card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="search-container d-flex align-items-center mb-3 mb-md-0">
                <div class="search-icon-container">
                    <i class="fa fa-search search-icon"></i>
                </div>
                <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search apartments')" />
            </div>
            <div class="d-flex flex-column flex-md-row filters-container">
            <select id="buildingFilter" class="form-select mb-2 mb-md-0">
                        <option value="">@lang('Select Building')</option>
                        @foreach($buildingNumbers as $buildingNumber)
                            <option value="Building {{ $buildingNumber }}">
                                @lang('Building') {{ $buildingNumber }}
                            </option>
                        @endforeach
                    </select>
                <select id="statusFilter" class="form-select mb-2 mb-md-0">
                    <option value="">@lang('Status')</option>
                    <option value="Active">@lang('Active')</option>
                    <option value="Inactive">@lang('Inactive')</option>
                    <option value="Under maintenance">@lang('Under maintenance')</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Start row -->
<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30 table-card">
            <div class="card-body table-container">
                <div class="table-responsive">
                    <table id="default-datatable" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('Apartment Number')</th>
                                <th>@lang('Building Number')</th>
                                <th>@lang('Gender')</th>
                                <th>@lang('Max Rooms')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Notes')</th>
                                <th>@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartments as $apartment)
                                <tr>
                                    <td>@lang('Apartment') {{ $apartment->number }}</td>
                                    <td>@lang('Building') {{ $apartment->Building->number }}</td>
                                    <td>{{ ucfirst($apartment->Building->gender) }}</td>
                                    <td>{{ $apartment->max_rooms }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $apartment->status)) }}</td>
                                    <td>{{ $apartment->note ?: __('No description') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-{{ $apartment->id }}" title="@lang('Edit Note')">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $apartment->id }}" title="@lang('Edit Status')">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $apartment->id }}" title="@lang('Delete Apartment')">
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
</div>
<!-- End row -->

<!-- Edit Status Modal -->
<div class="modal fade" id="editApartmentStatusModal" tabindex="-1" aria-labelledby="editApartmentStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editApartmentStatusModalLabel">@lang('Edit Apartment Status')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStatusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editApartmentStatus" class="form-label">@lang('Status')</label>
                        <select class="form-control border border-primary" id="editApartmentStatus" name="status" required>
                            <option value="active">@lang('Active')</option>
                            <option value="inactive">@lang('Inactive')</option>
                            <option value="under_maintenance">@lang('Under Maintenance')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn-primary" id="saveStatusBtn">@lang('Save Status')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit/Add Note Modal -->
<div class="modal fade" id="editApartmentNoteModal" tabindex="-1" aria-labelledby="editApartmentNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editApartmentNoteModalLabel">@lang('Edit/Add Apartment Note')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoteForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editApartmentNote" class="form-label">@lang('Note')</label>
                        <textarea class="form-control border border-primary" id="editApartmentNote" name="note" rows="4" required></textarea>
                    </div>
                </div>
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
<script src="{{ asset('js/custom/custom-table-datatable.js') }}"></script>
<script src="{{ asset('js/pages/apartments.js') }}"></script> <!-- Updated script reference -->
<script>
   
window.routes = {
    exportExcel : '{{ route('admin.unit.apartment.export-excel') }}',
    saveApartment: '{{ route('admin.unit.apartment.store') }}',
    deleteApartment: '{{ route('admin.unit.apartment.destroy', ':id') }}',
    updateApartmentStatus: '{{ route('admin.unit.apartment.update-status') }}', 
    updateApartmentNote: '{{ route('admin.unit.apartment.update-note') }}'    
};
</script>
@endsection
