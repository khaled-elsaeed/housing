@extends('layouts.admin')
@section('title', 'Buildings')
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
         <!-- Start col for Total Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.buildings.total_buildings') }}</h5>
                        <h4 class="mb-0" id="totalBuildings">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-home" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.active') }}</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="activeBuildingsCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.inactive') }}</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="inactiveBuildingsCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
         <!-- Start col for Male Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.buildings.male_buildings') }}</h5>
                        <h4 class="mb-0" id="maleBuildingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-male" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.active') }}</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="maleActiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.inactive') }}</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="maleInactiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
         <!-- Start col for Female Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.buildings.female_buildings') }}</h5>
                        <h4 class="mb-0" id="femaleBuildingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-female" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.active') }}</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="femaleActiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.inactive') }}</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="femaleInactiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
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
                        <h5 class="card-title font-14">{{ __('pages.admin.buildings.under_maintenance') }}</h5>
                        <h4 class="mb-0" id="maintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-wrench" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.males') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="maleUnderMaintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">{{ __('pages.admin.buildings.females') }}</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="femaleUnderMaintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.buildings.loading') }}</span>
                            </div>
                        </span>
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
<div class="d-flex flex-column mb-3">
   <!-- Title in its own row -->
   <h2 class="page-title text-primary mb-2">{{ __('pages.admin.buildings.title') }}</h2>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
   <!-- Left Section with Add Building Button -->
   <div class="d-flex">
      <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#addBuilidngModal">
         <i class="fa fa-plus-circle"></i> {{ __('pages.admin.buildings.add_building') }}
      </button>
   </div>
   <!-- Right Section with Toggle and Download Dropdown -->
   <div class="d-flex align-items-center">
      <!-- Toggle Button -->
      <button class="btn btn-outline-primary btn-sm ms-2 toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse" 
         data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
         <i class="fa fa-search-plus"></i>
      </button>
      <!-- <div class="btn-group ms-2" role="group" aria-label="Download Options">
         <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa fa-download"></i> {{ __('pages.admin.buildings.download') }}
         </button>
         <ul class="dropdown-menu">
            <li>
               <a class="dropdown-item" href="#" id="exportExcel">
                  <i class="fa fa-file-excel"></i> {{ __('pages.admin.buildings.building_excel') }}
               </a>
            </li>
         </ul>
      </div> -->
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
            <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.buildings.search') }}" />
         </div>
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="genderFilter" class="form-select mb-2 mb-md-0">
               <option value="">{{ __('pages.admin.buildings.gender') }}</option>
               <option value="male">{{ __('pages.admin.buildings.male') }}</option>
               <option value="female">{{ __('pages.admin.buildings.female') }}</option>
            </select>
         </div>
         <!-- Filters on the Right -->
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
               <option value="">{{ __('pages.admin.buildings.status') }}</option>
               <option value="active">{{ __('pages.admin.buildings.active') }}</option>
               <option value="inactive">{{ __('pages.admin.buildings.inactive') }}</option>
               <option value="under_maintenance">{{ __('pages.admin.buildings.under_maintenance') }}</option>
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
                        <th>{{ __('pages.admin.buildings.building_number') }}</th>
                        <th>{{ __('pages.admin.buildings.gender') }}</th>
                        <th>{{ __('pages.admin.buildings.max_apartments') }}</th>
                        <th>{{ __('pages.admin.buildings.status') }}</th>
                        <th>{{ __('pages.admin.buildings.description') }}</th>
                        <th>{{ __('pages.admin.buildings.actions') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <!-- End col -->
</div>
<!-- End row -->
<!-- Modal -->
<div class="modal fade" id="addBuilidngModal" tabindex="-1" aria-labelledby="addBuilidngModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addBuilidngModalLabel">{{ __('pages.admin.buildings.create_building') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="buildingForm">
            <div class="modal-body">
               <!-- Building Number -->
               <div class="mb-3">
                  <label for="newBuildingNumber" class="form-label">{{ __('pages.admin.buildings.building_number') }}</label>
                  <input type="text" class="form-control border border-primary" id="newBuildingNumber" name="building_number" required>
               </div>
               <!-- Gender -->
               <div class="mb-3">
                  <label for="newBuildingGender" class="form-label">{{ __('pages.admin.buildings.gender') }}</label>
                  <select class="form-control border border-primary" id="newBuildingGender" name="gender" required>
                     <option value="male">{{ __('pages.admin.buildings.male') }}</option>
                     <option value="female">{{ __('pages.admin.buildings.female') }}</option>
                  </select>
               </div>
               <!-- Max Apartments per Building -->
               <div class="mb-3">
                  <label for="newBuildingMaxApartments" class="form-label">{{ __('pages.admin.buildings.max_apartments') }}</label>
                  <input type="number" class="form-control border border-primary" id="newBuildingMaxApartments" name="max_apartments" required>
               </div>
               <!-- Max Rooms per Apartment -->
               <div class="mb-3">
                  <label for="newBuildingMaxRooms" class="form-label">{{ __('pages.admin.buildings.max_rooms') }}</label>
                  <input type="number" class="form-control border border-primary" id="newBuildingMaxRooms" name="max_rooms" required>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.buildings.close') }}</button>
               <button type="submit" class="btn btn-primary" id="saveBuildingBtn">{{ __('pages.admin.buildings.save_building') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Edit Status Modal -->
<div class="modal fade" id="editBuildingStatusModal" tabindex="-1" aria-labelledby="editBuildingStatusModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="editBuildingStatusModalLabel">{{ __('pages.admin.buildings.edit_status') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editStatusForm">
            <div class="modal-body">
               <!-- Building Status -->
               <div class="mb-3">
                  <label for="editBuildingStatus" class="form-label">{{ __('pages.admin.buildings.status') }}</label>
                  <select class="form-control border border-primary" id="editBuildingStatus" name="status" required>
                     <option value="active">{{ __('pages.admin.buildings.active') }}</option>
                     <option value="inactive">{{ __('pages.admin.buildings.inactive') }}</option>
                     <option value="under_maintenance">{{ __('pages.admin.buildings.under_maintenance') }}</option>
                  </select>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.buildings.close') }}</button>
               <button type="submit" class="btn btn-primary" id="saveStatusBtn">{{ __('pages.admin.buildings.save_status') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Edit/Add Note Modal -->
<div class="modal fade" id="editBuildingNoteModal" tabindex="-1" aria-labelledby="editBuildingNoteModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="editBuildingNoteModalLabel">{{ __('pages.admin.buildings.edit_note') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editNoteForm">
            <div class="modal-body">
               <!-- Building Note -->
               <div class="mb-3">
                  <label for="editBuildingNote" class="form-label">{{ __('pages.admin.buildings.note') }}</label>
                  <textarea class="form-control border border-primary" id="editBuildingNote" name="note" rows="4" required></textarea>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.buildings.close') }}</button>
               <button type="submit" class="btn btn-primary" id="saveNoteBtn">{{ __('pages.admin.buildings.save_note') }}</button>
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
<script src="{{ asset('js/pages/buildings.js') }}"></script>
<script>
   window.routes = {
      fetchBuildings: '{{ route('admin.unit.building.fetch') }}',
      fetchStats: '{{ route('admin.unit.building.stats') }}',
       exportExcel : '{{ route('admin.unit.building.export-excel') }}',
       saveBuilding: '{{ route('admin.unit.building.store') }}',
       deleteBuilding: '{{ route('admin.unit.building.destroy', ':id') }}',
       updateBuildingStatus: '{{ route('admin.unit.building.update-status') }}', 
       updateBuildingNote: '{{ route('admin.unit.building.update-note') }}'    
   };
</script>
@endsection