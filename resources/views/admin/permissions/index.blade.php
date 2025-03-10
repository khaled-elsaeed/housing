@extends('layouts.admin')
@section('title', __('Permission Management'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />

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
         <!-- Start col for Total Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-home"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Total Requests')</h5>
                        <h4 class="mb-0">{{ $totalPermissionRequests }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $totalMalePermissionRequests }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">@lang('Female')</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $totalFemalePermissionRequests }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Pending Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-clock"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Pending Requests')</h5>
                        <h4 class="mb-0">{{ $pendingPermissionRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $malePendingPermissionRequestsCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">@lang('Female')</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $femalePendingPermissionRequestsCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Approved Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Approved Requests')</h5>
                        <h4 class="mb-0">{{ $approvedPermissionRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $maleApprovedPermissionRequestsCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">@lang('Female')</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $femaleApprovedPermissionRequestsCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Rejected Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-danger-inverse me-0"><i class="feather icon-x-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">@lang('Rejected Requests')</h5>
                        <h4 class="mb-0">{{ $rejectedPermissionRequestsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $maleRejectedPermissionRequestsCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">@lang('Female')</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $femaleRejectedPermissionRequestsCount }}</span>
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


<!-- Permission Requests Table -->
<div class="d-flex flex-column mb-3">
    <h2 class="page-title text-primary mb-2">@lang('Permission Requests')</h2>
</div>

<div class="d-flex justify-content-end align-items-center mb-3">
   
      <button class="btn btn-outline-primary btn-sm ms-2 toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse" 
         data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
      <i class="fa fa-search-plus"></i>
      </button>
      <div class="btn-group ms-2" role="group" aria-label="Download Options">
         <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn"  data-bs-toggle="dropdown" aria-expanded="false">
         <i class="fa fa-download"></i> @lang('Download')
         </button>
         <ul class="dropdown-menu">
            <li>
               <a class="dropdown-item" href="#" id="exportExcel">
               <i class="fa fa-file-excel"></i> @lang('Export to Excel')
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
            <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search by student name or reason')" />
         </div>
         <!-- Filters on the Right -->
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
            <option value="">@lang('Filter by Status')</option>
            <option value="pending">@lang('Pending')</option>
            <option value="approved">@lang('Approved')</option>
            <option value="rejected">@lang('Rejected')</option>
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
                        <th>@lang('No')</th>
                        <th>@lang('Student Name')</th>
                        <th>@lang('Permission Type')</th>
                        <th>@lang('Reason')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Actions')</th>
                     </tr>
                  </thead>
                  <tbody>
                  @foreach($permissionRequests as $request)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $request->user->name }}</td>
                        <td>{{ $request->studentPermission->name }}</td>
                        <td>{{ $request->reason ?? __('No reason provided') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <span class="badge bg-warning text-dark">@lang('Pending')</span>
                            @elseif($request->status === 'approved')
                                <span class="badge bg-success">@lang('Approved')</span>
                            @elseif($request->status === 'rejected')
                                <span class="badge bg-danger">@lang('Rejected')</span>
                            @endif
                        </td>
                        <td>
                            @if($request->status === 'pending')
                                <!-- Accept Button -->
                                <button type="button" class="btn btn-rounded btn-success-rgba" id="accept-status-btn-{{ $request->id }}" title="@lang('Approve')">
                                    <i class="feather icon-check-circle"></i> @lang('Approve')
                                </button>

                                <!-- Reject Button -->
                                <button type="button" class="btn btn-rounded btn-danger-rgba" id="reject-status-btn-{{ $request->id }}" title="@lang('Reject')">
                                    <i class="feather icon-x-square"></i> @lang('Reject')
                                </button>
                            @else
                                <!-- View Button -->
                                <button type="button" class="btn btn-info btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewRequestModal" 
                                        data-request-id="{{ $request->id }}">
                                    <i class="feather icon-eye"></i> @lang('View')
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
        <h5 class="modal-title" id="viewRequestModalLabel">@lang('Request Details')</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Dynamic content for request details -->
        <div id="request-details"></div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- DataTables and Custom JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/pages/datatables.init.js') }}"></script>
<script>
$(document).ready(function () {
    $('#default-datatable').DataTable();
});
</script>
@endsection
