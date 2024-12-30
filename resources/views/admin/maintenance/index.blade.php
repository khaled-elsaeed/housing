@extends('layouts.admin')

@section('title', __('pages.admin.maintenance.title'))

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
<!-- Statistics Cards -->
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
                    
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.maintenance.total_requests') }}</h5>
                        <h4 class="mb-0">{{ $totalMaintenanceRequests }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.female') }}</span>
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
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.maintenance.pending_requests') }}</h5>
                        <h4 class="mb-0">{{ $pendingMaintenanceRequestsCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-alert-circle"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $malePendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.female') }}</span>
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
                    
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.maintenance.completed_requests') }}</h5>
                        <h4 class="mb-0">{{ $completedMaintenanceRequestsCount }}</h4>
                     </div> 
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleCompletedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.female') }}</span>
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
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.maintenance.rejected_requests') }}</h5>
                        <h4 class="mb-0">{{ $rejectedMaintenanceRequestsCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-danger-inverse me-0"><i class="feather icon-x-circle"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleRejectedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.maintenance.female') }}</span>
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


<!-- Table Section -->
<div class="d-flex flex-column mb-3">
    <h2 class="page-title text-primary mb-2">{{ __('pages.admin.maintenance.page_title') }}</h2>
</div>

<!-- <div class="d-flex justify-content-end align-items-center mb-3">
    <button class="btn btn-outline-primary btn-sm ms-2 toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
      <i class="fa fa-search-plus"></i> {{ __('pages.admin.maintenance.toggle_filter') }}
    </button>
    <div class="btn-group ms-2" role="group" aria-label="Download Options">
         <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
         <i class="fa fa-download"></i> {{ __('pages.admin.maintenance.download') }}
         </button>
         <ul class="dropdown-menu">
            <li>
               <a class="dropdown-item" href="#" id="exportExcel">
               <i class="fa fa-file-excel"></i> {{ __('pages.admin.maintenance.export_excel') }}
               </a>
            </li>
         </ul>
    </div>
</div> -->

<div class="collapse" id="collapseExample">
   <div class="search-filter-container card card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.maintenance.search_placeholder') }}" />
         </div>
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
                <option value="">{{ __('pages.admin.maintenance.filter_by_status') }}</option>
                <option value="pending">{{ __('pages.admin.maintenance.pending') }}</option>
                <option value="completed">{{ __('pages.admin.maintenance.completed') }}</option>
                <option value="rejected">{{ __('pages.admin.maintenance.rejected') }}</option>
            </select>
         </div>
      </div>
   </div>
</div>
<div class="row">
   <div class="col-lg-12">
      <div class="card m-b-30 table-card">
         <div class="card-body table-container">
            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>{{ __('pages.admin.maintenance.no') }}</th>
                        <th>{{ __('pages.admin.maintenance.student_name') }}</th>
                        <th>{{ __('pages.admin.maintenance.location') }}</th>
                        <th>{{ __('pages.admin.maintenance.description') }}</th>
                        <th>{{ __('pages.admin.maintenance.status') }}</th>
                        <th>{{ __('pages.admin.maintenance.actions') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($maintenanceRequests as $request)
                        <tr>
                           <td>{{ $loop->iteration }}</td>
                           <td>{{ $request->user->getUsernameEnAttribute() }}</td>
                           <?php
                              $location = method_exists($request->user, 'getLocationDetails') 
                              ? $request->user->getLocationDetails() 
                              : ['building' => 'N/A', 'apartment' => 'N/A', 'room' => 'N/A'];

                              $locationString = __(
                              'pages.admin.maintenance.building', ['building' => $location['building']]
                              ) . ' - ' . __(
                              'pages.admin.maintenance.apartment', ['apartment' => $location['apartment']]
                              ) . ' - ' . __(
                              'pages.admin.maintenance.room', ['room' => $location['room']]
                              );
                           ?>
                           <td>{{$locationString}}</td>
                           <td>{{ $request->description ?? __('pages.admin.maintenance.no_description') }}</td>
                           <td>
    @if($request->status === 'pending')
        <span class="badge bg-warning text-dark">{{ __('pages.admin.maintenance.status_pending') }}</span>
    @elseif($request->status === 'in_progress')
        <span class="badge bg-success">{{ __('pages.admin.maintenance.status_accepted') }}</span><br>
        <small>{{ __('pages.admin.maintenance.accepted_at', ['date' => $request->updated_at->format('d M Y, h:i A')]) }}</small>
    @elseif($request->status === 'rejected')
        <span class="badge bg-danger">{{ __('pages.admin.maintenance.status_rejected') }}</span><br>
        <small>{{ __('pages.admin.maintenance.rejected_at', ['date' => $request->updated_at->format('d M Y, h:i A')]) }}</small>
    @elseif($request->status === 'completed')
        <span class="badge bg-primary">{{ __('pages.admin.maintenance.status_completed') }}</span><br>
        <small>{{ __('pages.admin.maintenance.completed_at', ['date' => $request->updated_at->format('d M Y, h:i A')]) }}</small>
    @endif
</td>

<td>
    @if($request->status === 'pending')
        <!-- Accept and Reject Buttons -->
        <button type="button" class="btn btn-rounded btn-success-rgba" id="in-progress-status-btn-{{ $request->id }}" title="{{ __('pages.admin.maintenance.accept_request') }}">
            <i class="feather icon-check-circle"></i> {{ __('pages.admin.maintenance.accept') }}
        </button>
        <button type="button" class="btn btn-rounded btn-danger-rgba" id="reject-status-btn-{{ $request->id }}" title="{{ __('pages.admin.maintenance.reject_request') }}">
            <i class="feather icon-x-square"></i> {{ __('pages.admin.maintenance.reject') }}
        </button>
    @elseif($request->status === 'in_progress')
        <!-- Complete Button -->
        <button type="button" class="btn btn-rounded btn-info-rgba" id="complete-status-btn-{{ $request->id }}" title="{{ __('pages.admin.maintenance.complete_request') }}" onclick="completeRequest({{ $request->id }})">
            <i class="feather icon-check-circle"></i> {{ __('pages.admin.maintenance.complete') }}
        </button>
    @endif

    <!-- View Button -->
    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewRequestModal" data-request-id="{{ $request->id }}">
        <i class="feather icon-eye"></i> {{ __('pages.admin.maintenance.view') }}
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

<!-- Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-labelledby="viewRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRequestModalLabel">{{ __('pages.admin.maintenance.issues') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>{{ __('pages.admin.maintenance.issues') }}</h5>
                <ul id="issueList" class="list-group">
                    <!-- Issues will be dynamically added here -->
                </ul>
                <hr>
                <h5>{{ __('pages.admin.maintenance.additional_info') }}</h5>
                <p id="additionalInfo"></p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/pages/maintenance.js') }}"></script>
<script>
    window.routes = {
        exportExcel: "{{ route('admin.maintenance.excel') }}",
        updateStatus: "{{ route('admin.maintenance.updateStatus', ':id') }}",
        getIssues: "{{ route('admin.maintenance.getIssues', ':id') }}",
    };
    window.translation = {
        issues: @json(__('pages.admin.maintenance.issues'))
    };
</script>
@endsection

