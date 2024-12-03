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
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-home"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Total Buildings</h5>
                        <h4 class="mb-0" id="totalBuildings">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="activeBuildingsCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="inactiveBuildingsCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
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
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-user-check"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Male Buildings</h5>
                        <h4 class="mb-0" id="maleBuildingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="maleActiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="maleInactiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
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
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-user-x"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Female Buildings</h5>
                        <h4 class="mb-0" id="femaleBuildingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                     <span class="font-13" id="femaleActiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="femaleInactiveCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
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
                     <div class="col-5">
                        <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Under Maintenance</h5>
                        <h4 class="mb-0" id="maintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="maleUnderMaintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-3 text-end">
                     <span class="font-13" id="femaleUnderMaintenanceCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">Loading...</span>
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
   <h2 class="page-title text-primary mb-2">Buildings</h2>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">
   <!-- Left Section with Add Building Button -->
   <div class="d-flex">
      <button type="button" class="btn btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#addBuilidngModal">
      <i class="fa fa-plus-circle"></i> Add Building
      </button>
   </div>
   <!-- Right Section with Toggle and Download Dropdown -->
   <div class="d-flex align-items-center">
      <!-- Toggle Button -->
      <button class="btn btn-outline-primary btn-sm ms-2 toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse" 
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
               <i class="fa fa-file-excel"></i> Building (Excel)
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
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="genderFilter" class="form-select mb-2 mb-md-0">
               <option value="">Gender</option>
               <option value="male">Male</option>
               <option value="female">Female</option>
            </select>
         </div>
         <!-- Filters on the Right -->
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
               <option value="">Status</option>
               <option value="active">Active</option>
               <option value="inactive">Inactive</option>
               <option value="under_maintenance">Under maintenance</option>
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
                        <th>Building Number</th>
                        <th>Gender</th>
                        <th>Max Apartments</th>
                        <!-- New column for max apartments -->
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
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
            <h5 class="modal-title" id="addBuilidngModalLabel">Create Building</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="buildingForm">
            <!-- Modal Content -->
            <div class="modal-body">
               <!-- Building Number -->
               <div class="mb-3">
                  <label for="newBuildingNumber" class="form-label">Building Number</label>
                  <input type="text" class="form-control border border-primary" id="newBuildingNumber" name="building_number" required>
               </div>
               <!-- Gender -->
               <div class="mb-3">
                  <label for="newBuildingGender" class="form-label">Gender</label>
                  <select class="form-control border border-primary" id="newBuildingGender" name="gender" required>
                     <option value="male">Male</option>
                     <option value="female">Female</option>
                  </select>
               </div>
               <!-- Max Apartments per Building -->
               <div class="mb-3">
                  <label for="newBuildingMaxApartments" class="form-label">Maximum Number of Apartments Per Building</label>
                  <input type="number" class="form-control border border-primary" id="newBuildingMaxApartments" name="max_apartments" required>
               </div>
               <!-- Max Rooms per Apartment -->
               <div class="mb-3">
                  <label for="newBuildingMaxRooms" class="form-label">Maximum Number of Rooms per Apartment</label>
                  <input type="number" class="form-control border border-primary" id="newBuildingMaxRooms" name="max_rooms" required>
               </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               <button type="submit" class="btn btn-primary" id="saveBuildingBtn">Save Building</button>
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
            <h5 class="modal-title" id="editBuildingStatusModalLabel">Edit Building Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editStatusForm">
            <!-- Modal Content -->
            <div class="modal-body">
               <!-- Building Status -->
               <div class="mb-3">
                  <label for="editBuildingStatus" class="form-label">Status</label>
                  <select class="form-control border border-primary" id="editBuildingStatus" name="status" required>
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
<div class="modal fade" id="editBuildingNoteModal" tabindex="-1" aria-labelledby="editBuildingNoteModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="editBuildingNoteModalLabel">Edit/Add Building Note</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editNoteForm">
            <!-- Modal Content -->
            <div class="modal-body">
               <!-- Building Note -->
               <div class="mb-3">
                  <label for="editBuildingNote" class="form-label">Note</label>
                  <textarea class="form-control border border-primary" id="editBuildingNote" name="note" rows="4" required></textarea>
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