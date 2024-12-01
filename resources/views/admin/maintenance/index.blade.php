@extends('layouts.admin')
@section('title', 'Maintenance Requests')
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
         <!-- Start col for Total Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Total Maintenance Requests</h5>
                        <h4 class="mb-0">{{ $totalMaintenanceRequests }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Male</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Female</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $femaleTotalCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Pending Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-alert-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Pending Maintenance</h5>
                        <h4 class="mb-0">{{ $pendingMaintenanceRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Male</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $malePendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Female</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $femalePendingCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Completed Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Completed Maintenance</h5>
                        <h4 class="mb-0">{{ $completedMaintenanceRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Male</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleCompletedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Female</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $femaleCompletedCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Rejected Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-danger-inverse me-0"><i class="feather icon-x-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Rejected Maintenance</h5>
                        <h4 class="mb-0">{{ $rejectedMaintenanceRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Male</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleRejectedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Female</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $femaleRejectedCount }}</span>
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


<!-- Maintenance Request Table -->
<div class="d-flex flex-column mb-3">
    <h2 class="page-title text-primary mb-2">Maintenance Requests</h2>
</div>

<div class="d-flex justify-content-end align-items-center mb-3">
   
   <!-- Right Section with Toggle and Download Dropdown -->
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
               <i class="fa fa-file-excel"></i> Maintenance Requests (Excel)
               </a>
            </li>
         </ul>
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
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
            <option value="">Filter by Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="rejected">Rejected</option>
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
                        <th>No.</th>
                        <th>Student Name</th>
                        <th>Location</th>
                        <th>Issue Type</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                  @foreach($maintenanceRequests as $request)
                    @php
                        $location = $request->room ? $request->room->getLocation() : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $request->user->getUsernameEnAttribute() }}</td>
                        <td>
                            Building {{ $location['building'] }} - Apartment {{ $location['apartment'] }} - Room {{ $location['room'] }}
                        </td>
                        <td>{{ $request->issue_type ?? 'Unknown' }}</td>
                        <td>{{ $request->description ?? 'No Description' }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($request->status === 'accepted')
                                <span class="badge bg-success">Accepted</span><br>
                                <small>Accepted At: {{ $request->updated_at->format('d M Y, h:i A') }}</small>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                <small>Rejected At: {{ $request->updated_at->format('d M Y, h:i A') }}</small>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <!-- Accept Button -->
                                <button type="button" class="btn btn-rounded btn-success-rgba" id="accept-status-btn-{{ $request->id }}" title="Accept request">
                                    <i class="feather icon-check-circle"></i> Accept
                                </button>

                                <!-- Reject Button -->
                                <button type="button" class="btn btn-rounded btn-danger-rgba" id="reject-status-btn-{{ $request->id }}" title="Reject request">
                                    <i class="feather icon-x-square"></i> Reject
                                </button>
                            @else
                                <!-- View Button for Accepted/Rejected Requests -->
                                <button type="button" class="btn btn-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewRequestModal" 
                                        data-request-id="{{ $request->id }}">
                                    <i class="feather icon-eye"></i> View
                                </button>
                            @endif
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

<!-- Modal for View Request -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewRequestModalLabel">Request Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Here you would dynamically load the details based on request ID -->
        <div id="request-details">
          <!-- Request details will be dynamically loaded here -->
        </div>
      </div>
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
<script src="{{ asset('js/pages/maintenance.js') }}"></script>
   <script>
    window.routes = {
        exportExcel: "{{ route('admin.maintenance.excel') }}",
        updateStatus: "{{ route('admin.maintenance.updateStatus', ':id') }}",  // Add updateStatus route
    };
</script>

   
@endsection

