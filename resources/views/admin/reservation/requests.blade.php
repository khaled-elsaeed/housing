@extends('layouts.admin')

@section('title', __('Reservation Requests'))

@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Start row -->
<div class="row">
   <!-- Start col for Total Requests -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Total Requests')</h5>
                  <h4 class="mb-0" id="totalRequestsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </h4>
               </div>
               <div class="col-5 text-end">
                  <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-book"></i></span>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Start col for Pending Requests -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Pending Requests')</h5>
                  <h4 class="mb-0" id="pendingRequestsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </h4>
               </div>
               <div class="col-5 text-end">
                  <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-clock"></i></span>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Start col for Accepted Requests -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Accepted Requests')</h5>
                  <h4 class="mb-0" id="acceptedRequestsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </h4>
               </div>
               <div class="col-5 text-end">
                  <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Start col for Rejected Requests -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Rejected Requests')</h5>
                  <h4 class="mb-0" id="rejectedRequestsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </h4>
               </div>
               <div class="col-5 text-end">
                  <span class="action-icon badge badge-danger-inverse me-0"><i class="feather icon-x-circle"></i></span>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Search and Auto-Reserve Button -->
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <h2 class="page-title text-primary mb-2 mb-md-0">@lang('Reservation Requests')</h2>
         <div>
            <button class="btn btn-primary me-3" id="autoReserveBtn">
               <i class="feather icon-zap"></i> @lang('Auto-Reserve All')
            </button>
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
               <i class="fa fa-search-plus"></i>
            </button>
         </div>
      </div>
   </div>
</div>

<!-- Search Filter -->
<div class="collapse" id="collapseExample">
   <div class="search-filter-container card card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search by request details')" />
         </div>
      </div>
   </div>
</div>

<!-- Table for Requests -->
<div class="row">
   <div class="col-lg-12">
      <div class="card m-b-30 table-card">
         <div class="card-body table-container">
            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>@lang('Student Name')</th>
                        <th>@lang('Period Type')</th>
                        <th>@lang('Duration')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Actions')</th>
                     </tr>
                  </thead>
                  <tbody>
                     <!-- Data will be populated by DataTables -->
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Accept Request Modal -->
<div class="modal fade" id="acceptRequestModal" tabindex="-1" aria-labelledby="acceptRequestModalLabel">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
      
         <!-- Modal Header -->
         <div class="modal-header">
            <h5 class="modal-title" id="acceptRequestModalLabel">
               <i class="fa fa-check-circle"></i> @lang('Accept Reservation Request')
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         
         <!-- Modal Body -->
         <div class="modal-body">
            <form id="acceptRequestForm">
               @CSRF
               <input type="hidden" id="requestId" name="request_id">
               
               <div class="row g-3">
               
                  <!-- Select Building -->
                  <div class="col-md-4">
                     <label for="building" class="form-label">
                        <i class="fa fa-building"></i> @lang('Select Building')
                     </label>
                     <select class="form-select" id="building" name="building_id" required>
                        <option value="">@lang('Select Building')</option>
                     </select>
                  </div>

                  <!-- Select Apartment -->
                  <div class="col-md-4">
                     <label for="apartment" class="form-label">
                        <i class="fa fa-door-open"></i> @lang('Select Apartment')
                     </label>
                     <select class="form-select" id="apartment" name="apartment_id" required disabled>
                        <option value="">@lang('Select Apartment')</option>
                     </select>
                  </div>

                  <!-- Select Room -->
                  <div class="col-md-4">
                     <label for="room" class="form-label">
                        <i class="fa fa-bed"></i> @lang('Select Room')
                     </label>
                     <select class="form-select" id="room" name="room_id" required disabled>
                        <option value="">@lang('Select Room')</option>
                     </select>
                  </div>

               </div>
            </form>
         </div>
         
         <!-- Modal Footer -->
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
               <i class="fa fa-times"></i> @lang('Close')
            </button>
            <button type="button" class="btn btn-primary" id="confirmAccept">
               <i class="fa fa-check"></i> @lang('Accept Request')
            </button>
         </div>

      </div>
   </div>
</div>



<!-- Reject Request Modal -->
<div class="modal fade" id="rejectRequestModal" tabindex="-1" aria-labelledby="rejectRequestModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="rejectRequestModalLabel">@lang('Reject Reservation Request')</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="rejectRequestForm">
               @CSRF
               <input type="hidden" id="rejectRequestId" name="request_id">
               <div class="mb-3">
                  <label for="rejectReason" class="form-label">@lang('Rejection Reason')</label>
                  <textarea class="form-control" id="rejectReason" name="reason" rows="3" required></textarea>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
            <button type="button" class="btn btn-danger" id="confirmReject">@lang('Reject Request')</button>
         </div>
      </div>
   </div>
</div>

@endsection

@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>

<script>
    window.routes = {
        fetchRequests: "{{ route('admin.reservation-requests.fetch') }}",
        getSummary: "{{ route('admin.reservation-requests.get-summary') }}",
        acceptRequest: "{{ route('admin.reservation-requests.accept', ':id') }}",
        rejectRequest: "{{ route('admin.reservation-requests.reject', ':id') }}",
        autoReserve: "{{ route('admin.reservation-requests.auto-reserve') }}",
        fetchEmptyBuildings: "{{ route('admin.unit.building.fetch-empty') }}",
         fetchEmptyApartments: "{{ route('admin.unit.apartment.fetch-empty',':buildingId')}}",
         fetchEmptyRooms: "{{ route('admin.unit.room.fetch-empty',':apartmentId' ) }}",
    };
</script>

<script src="{{ asset('js/pages/reservation-requests.js') }}"></script>
@endsection