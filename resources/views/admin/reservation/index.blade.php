@extends('layouts.admin')

@section('title', __('Reservations'))

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
   <!-- Start col for Total Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
              
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Total Reservations')</h5>
                  <h4 class="mb-0" id="totalReservationsCount">
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
         <div class="card-footer">
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Male')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Female')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Total Reservations -->

   <!-- Start col for Pending Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
              
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Pending Reservations')</h5>
                  <h4 class="mb-0" id="totalPendingReservationsCount">
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
         <div class="card-footer">
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Male')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="malePendingReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Female')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femalePendingReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Pending Reservations -->

   <!-- Start col for Active Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Active Reservations')</h5>
                  <h4 class="mb-0" id="totalActiveReservationsCount">
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
         <div class="card-footer">
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Male')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleActiveReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Female')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleActiveReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Active Reservations -->

   <!-- Start col for Cancelled Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">@lang('Cancelled Reservations')</h5>
                  <h4 class="mb-0" id="totalCancelledReservationsCount">
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
         <div class="card-footer">
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Male')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleCancelledReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">@lang('Female')</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleCancelledReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">@lang('Loading...')</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Cancelled Reservations -->
</div>
<!-- End row -->

<!-- Search and Download Buttons -->
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <h2 class="page-title text-primary mb-2 mb-md-0">@lang('Reservations')</h2>
         <div>
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
            <input type="search" class="form-control search-input" id="searchBox" placeholder="@lang('Search by reservation details')" />
         </div>
      </div>
   </div>
</div>

<!-- Table for Reservations -->
<div class="row">
   <div class="col-lg-12">
      <div class="card m-b-30 table-card">
         <div class="card-body table-container">
            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>@lang('Reservation ID')</th>
                        <th>@lang('Name')</th>
                        <th>@lang('Location')</th>
                        <th>@lang('Period')</th>
                        <th>@lang('Duration')</th>
                        <th>@lang('Status')</th>
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

@endsection

@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}?v={{ config('app.version') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}?v={{ config('app.version') }}"></script>

<script src="{{ asset('js/pages/reservation.js') }}"></script>
<script>
    window.routes = {
        exportExcel: "{{ route('admin.reservation.export-excel') }}",
        getReservationMoreDetails: "{{ route('admin.reservation.show', ':id') }}",
        fetchReservations: "{{ route('admin.reservation.fetch') }}",
        getSummary: "{{ route('admin.reservation.get-summary') }}",
    };
</script>

@endsection
