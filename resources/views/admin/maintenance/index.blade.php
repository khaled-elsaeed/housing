@extends('layouts.admin')

@section('title', __('Maintenance'))

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
                        <h5 class="card-title font-14">@lang('Total Requests')</h5>
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
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Female')</span>
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
                        <h5 class="card-title font-14">@lang('Pending Requests')</h5>
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
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $malePendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Female')</span>
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
                        <h5 class="card-title font-14">@lang('Completed Requests')</h5>
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
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleCompletedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Female')</span>
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
                        <h5 class="card-title font-14">@lang('Rejected Requests')</h5>
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
                        <span class="font-13">@lang('Male')</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleRejectedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">@lang('Female')</span>
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
    <h2 class="page-title text-primary mb-2">@lang('Maintenance Requests')</h2>
</div>

<div class="collapse" id="collapseExample">
   <div class="search-filter-container card card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search for Requests')" />
         </div>
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="statusFilter" class="form-select mb-2 mb-md-0">
                <option value="">@lang('Filter by Status')</option>
                <option value="pending">@lang('Pending')</option>
                <option value="completed">@lang('Completed')</option>
                <option value="rejected">@lang('Rejected')</option>
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
                        <th>@lang('No.')</th>
                        <th>@lang('Student Name')</th>
                        <th>@lang('Location')</th>
                        <th>@lang('Description')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Actions')</th>
                        <th>@lang('Actions')</th>

                        <th>@lang('Actions')</th>

                        <th>@lang('Actions')</th>

                     </tr>
                  </thead>
                  <tbody>
                    
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
                <h5 class="modal-title" id="viewRequestModalLabel">@lang('Maintenance Issues')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>@lang('Issues')</h5>
                <ul id="issueList" class="list-group">
                    <!-- Issues will be dynamically added here -->
                </ul>
                <hr>
                <h5>@lang('Additional Info')</h5>
                <p id="additionalInfo"></p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
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
      fetchRequests: "{{ route('admin.maintenance.requests.fetch') }}",  
    };
</script>
@endsection
