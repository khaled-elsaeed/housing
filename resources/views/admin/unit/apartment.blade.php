@extends('layouts.admin')
@section('title', __('pages.admin.apartment.title'))
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
                        <h5 class="card-title font-14">{{ __('pages.admin.apartment.total_apartments') }}</h5>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.apartment.male_apartments') }}</h5>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.apartment.female_apartments') }}</h5>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.apartment.under_maintenance') }}</h5>
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
                        <span class="font-13">{{ __('pages.admin.apartment.males') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $maleUnderMaintenanceCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">{{ __('pages.admin.apartment.females') }}</span>
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
            <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('pages.admin.apartment.apartments') }}</h2> <!-- Updated title -->
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
                <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.apartment.search_placeholder') }}" />
            </div>
            <div class="d-flex flex-column flex-md-row filters-container">
            <select id="buildingFilter" class="form-select mb-2 mb-md-0">
                        <option value="">{{ __('pages.admin.rooms.select_building') }}</option>
                        @foreach($buildingNumbers as $buildingNumber)
                            <option value="{{ __('pages.admin.apartment.building') }} {{ $buildingNumber }}">
                                {{ __('pages.admin.apartment.building') }} {{ $buildingNumber }}
                            </option>
                        @endforeach
                    </select>
                <select id="statusFilter" class="form-select mb-2 mb-md-0">
                    <option value="">{{ __('pages.admin.apartment.status') }}</option>
                    <option value="Active">{{ __('pages.admin.apartment.active') }}</option>
                    <option value="Inactive">{{ __('pages.admin.apartment.inactive') }}</option>
                    <option value="Under maintenance">{{ __('pages.admin.apartment.under_maintenance') }}</option>
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
                                <th>{{ __('pages.admin.apartment.apartment_number') }}</th>
                                <th>{{ __('pages.admin.apartment.building_number') }}</th>
                                <th>{{ __('pages.admin.apartment.gender') }}</th>
                                <th>{{ __('pages.admin.apartment.max_rooms') }}</th>
                                <th>{{ __('pages.admin.apartment.status') }}</th>
                                <th>{{ __('pages.admin.apartment.notes') }}</th>
                                <th>{{ __('pages.admin.apartment.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartments as $apartment)
                                <tr>
                                    <td>{{ __('pages.admin.rooms.apartment') }} {{ $apartment->number }}</td>
                                    <td>{{ __('pages.admin.apartment.building') }} {{ $apartment->Building->number }}</td>
                                    <td>{{ ucfirst($apartment->Building->gender) }}</td>
                                    <td>{{ $apartment->max_rooms }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $apartment->status)) }}</td>
                                    <td>{{ $apartment->note ?: __('pages.admin.apartment.no_description') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-{{ $apartment->id }}" title="{{ __('pages.admin.apartment.edit_note') }}">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $apartment->id }}" title="{{ __('pages.admin.apartment.edit_status') }}">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $apartment->id }}" title="{{ __('pages.admin.apartment.delete_apartment') }}">
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
                <h5 class="modal-title" id="editApartmentStatusModalLabel">{{ __('pages.admin.apartment.edit_apartment_status') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editStatusForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editApartmentStatus" class="form-label">{{ __('pages.admin.apartment.status') }}</label>
                        <select class="form-control border border-primary" id="editApartmentStatus" name="status" required>
                            <option value="active">{{ __('pages.admin.apartment.active') }}</option>
                            <option value="inactive">{{ __('pages.admin.apartment.inactive') }}</option>
                            <option value="under_maintenance">{{ __('pages.admin.apartment.under_maintenance') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.apartment.close') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveStatusBtn">{{ __('pages.admin.apartment.save_status') }}</button>
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
                <h5 class="modal-title" id="editApartmentNoteModalLabel">{{ __('pages.admin.apartment.edit_add_apartment_note') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNoteForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editApartmentNote" class="form-label">{{ __('pages.admin.apartment.note') }}</label>
                        <textarea class="form-control border border-primary" id="editApartmentNote" name="note" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.apartment.close') }}</button>
                    <button type="submit" class="btn btn-primary" id="saveNoteBtn">{{ __('pages.admin.apartment.save_note') }}</button>
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
