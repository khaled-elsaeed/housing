@extends('layouts.admin')
@section('title', 'Residents')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Start row -->
<div class="row">
    <!-- Start col -->
    <div class="col-lg-12">
        <!-- Start row -->
        <div class="row">
            <!-- Start col -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-primary-inverse me-0">
                                    <i class="feather icon-user"></i>
                                </span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Residents</h5>
                                <h4 id="totalResidents">
                                    <!-- Small loading spinner for Residents (inline) -->
                                    <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->

            <!-- Start col -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-success-inverse me-0">
                                    <i class="feather icon-award"></i>
                                </span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Male</h5>
                                <h4 id="totalMaleCount">
                                    <!-- Small loading spinner for Male Count (inline) -->
                                    <div class="spinner-border spinner-border-sm text-success d-inline-block" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->

            <!-- Start col -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-warning-inverse me-0">
                                    <i class="feather icon-briefcase"></i>
                                </span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Female</h5>
                                <h4 id="totalFemaleCount">
                                    <!-- Small loading spinner for Female Count (inline) -->
                                    <div class="spinner-border spinner-border-sm text-warning d-inline-block" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </h4>
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
<!-- End row -->

<!-- Header for Residents Table and Controls -->
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <!-- Title on the Left -->
         <h2 class="page-title text-primary mb-2 mb-md-0">Residents</h2>
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
                     <i class="fa fa-file-excel"></i> Residents (Excel)
                     </a>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Search Filter -->
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
      </div>
   </div>
</div>

<!-- Residents Table -->
<div class="row">
   <div class="col-lg-12">
      <div class="card m-b-30 table-card">
         <div class="card-body table-container">
            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>National ID</th>
                        <th>Faculty</th>
                        <th>Mobile</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                     </tr>
                  </thead>
                  <tbody></tbody> <!-- Data will load via Ajax -->
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Resident Details Modal -->
<div class="modal fade" id="residentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="residentDetailsModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="residentDetailsModalLabel">Resident Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label for="faculty">Faculty</label>
               <input type="text" class="form-control" id="faculty" readonly>
            </div>
            <div class="form-group">
               <label for="program">Program</label>
               <input type="text" class="form-control" id="program" readonly>
            </div>
            <div class="form-group">
               <label for="score">Score</label>
               <input type="text" class="form-control" id="score" readonly>
            </div>
            <div class="form-group">
               <label for="percent">Percent</label>
               <input type="text" class="form-control" id="percent" readonly>
            </div>
            <div class="form-group">
               <label for="governorate">Governorate</label>
               <input type="text" class="form-control" id="governorate" readonly>
            </div>
            <div class="form-group">
               <label for="city">City</label>
               <input type="text" class="form-control" id="city" readonly>
            </div>
            <div class="form-group">
               <label for="street">Street</label>
               <input type="text" class="form-control" id="street" readonly>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('js/pages/residents.js') }}"></script>
<script>
   window.routes = {
      exportExcel: "{{ route('admin.residents.export-excel') }}",
      getResidentMoreDetails: "{{ route('admin.residents.more-details', ':id') }}",
      fetchResidents: "{{ route('admin.residents.fetch') }}",
      getSummary: "{{ route('admin.residents.get-summary') }}"
   };
</script>
@endsection
