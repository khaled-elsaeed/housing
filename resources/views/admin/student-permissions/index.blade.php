@extends('layouts.admin')
@section('title', 'Permissions')
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
         <!-- Start col for Total Permissions -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-file-text"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Total Permissions</h5>
                        <h4 class="mb-0">{{ $totalPermissions }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Approved</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $approvedPermissionsCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Denied</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $deniedPermissionsCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Pending Permissions -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-clock"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Pending Permissions</h5>
                        <h4 class="mb-0">{{ $pendingPermissionsCount }}</h4>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Late Arrival Permissions -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-info-inverse me-0"><i class="feather icon-log-in"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Late Arrivals</h5>
                        <h4 class="mb-0">{{ $lateArrivalPermissionsCount }}</h4>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Extended Stay Permissions -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-log-out"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Extended Stays</h5>
                        <h4 class="mb-0">{{ $extendedStayPermissionsCount }}</h4>
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
            <!-- Title on the Left -->
            <h2 class="page-title text-primary mb-2 mb-md-0">Manage Permissions</h2>
            <!-- Toggle Button on the Right -->
            <div class="div">
                <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-search-plus"></i>
                </button>
                <div class="btn-group ms-2" role="group" aria-label="Download Options">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-download"></i> Download
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" id="exportExcel">
                                <i class="fa fa-file-excel"></i> Permissions (Excel)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="exportPDF">
                                <i class="fa fa-file-pdf"></i> Report (PDF)
                            </a>
                        </li>
                    </ul>
                </div>
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
                <input type="search" class="form-control search-input" id="searchBox" placeholder="Search permissions..." />
            </div>
            <div class="d-flex flex-column flex-md-row filters-container">
                <select id="statusFilter" class="form-select mb-2 mb-md-0">
                    <option value="">Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                    <option value="Denied">Denied</option>
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
                                <th>Student Name</th>
                                <th>Permission Type</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $permission)
                                <tr>
                                    <td>{{ $permission->student->name }}</td>
                                    <td>{{ ucfirst($permission->type) }}</td>
                                    <td>{{ $permission->requested_at->format('Y-m-d') }}</td>
                                    <td>{{ ucfirst($permission->status) }}</td>
                                    <td>{{ $permission->reason }}</td>
                                    <td>
                                        <button type="button" class="btn btn-round btn-warning-rgba" title="Edit" onclick="editPermission({{ $permission->id }})">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-danger-rgba" title="Delete" onclick="deletePermission({{ $permission->id }})">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Student Name</th>
                                <th>Permission Type</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Reason</th>
                                <th>Actions</th>
                            </tr>
                        </tfoot>
                    </table>
                </div> 
            </div>
        </div>
    </div>
</div>
<!-- End row -->

@endsection
