@extends('layouts.admin')

@section('title', __('pages.admin.reservation.title'))

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
   <!-- Start col for Total Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
              
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">{{ __('pages.admin.reservation.total_reservations') }}</h5>
                  <h4 class="mb-0" id="totalReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
                  <span class="font-13">{{ __('pages.admin.reservation.male') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">{{ __('pages.admin.reservation.female') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
                  <h5 class="card-title font-14">{{ __('pages.admin.reservation.pending_reservations') }}</h5>
                  <h4 class="mb-0" id="totalPendingReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
                  <span class="font-13">{{ __('pages.admin.reservation.male') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="malePendingReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">{{ __('pages.admin.reservation.female') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femalePendingReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Pending Reservations -->

   <!-- Start col for Confirmed Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">{{ __('pages.admin.reservation.confirmed_reservations') }}</h5>
                  <h4 class="mb-0" id="totalConfirmedReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
                  <span class="font-13">{{ __('pages.admin.reservation.male') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleConfirmedReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">{{ __('pages.admin.reservation.female') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleConfirmedReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End col for Confirmed Reservations -->

   <!-- Start col for Cancelled Reservations -->
   <div class="col-lg-3 col-md-6 mb-2">
      <div class="card m-b-30">
         <div class="card-body">
            <div class="row align-items-center">
               
               <div class="col-7 text-start mt-2 mb-2">
                  <h5 class="card-title font-14">{{ __('pages.admin.reservation.cancelled_reservations') }}</h5>
                  <h4 class="mb-0" id="totalCancelledReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
                  <span class="font-13">{{ __('pages.admin.reservation.male') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="maleCancelledReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
                     </div>
                  </span>
               </div>
            </div>
            <div class="row align-items-center">
               <div class="col-6 text-start">
                  <span class="font-13">{{ __('pages.admin.reservation.female') }}</span>
               </div>
               <div class="col-6 text-end">
                  <span class="font-13" id="femaleCancelledReservationsCount">
                     <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                        <span class="visually-hidden">{{ __('pages.admin.reservation.loading') }}</span>
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
         <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('pages.admin.reservation.title') }}</h2>
         <div>
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-search-plus"></i>
            </button>
            <!-- <div class="btn-group ms-2" role="group" aria-label="Download Options">
               <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn"  data-bs-toggle="dropdown" aria-expanded="false">
               <i class="fa fa-download"></i> {{ __('pages.admin.reservation.download') }}
               </button>
               <ul class="dropdown-menu">
                  <li>
                     <a class="dropdown-item" href="#" id="exportExcel">
                     <i class="fa fa-file-excel"></i> {{ __('pages.admin.reservation.excel') }}
                     </a>
                  </li>
               </ul>
            </div> -->
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
            <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.reservation.search_placeholder') }}" />
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
                        <th>{{ __('pages.admin.reservation.reservation_id') }}</th>
                        <th>{{ __('pages.admin.reservation.name') }}</th>
                        <th>{{ __('pages.admin.reservation.location') }}</th>
                        <th>{{ __('pages.admin.reservation.start_date') }}</th>
                        <th>{{ __('pages.admin.reservation.end_date') }}</th>
                        <th>{{ __('pages.admin.reservation.status') }}</th>
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
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/pages/reservation.js') }}"></script>
<script>
    window.routes = {
        exportExcel: "{{ route('admin.reservation.export-excel') }}",
        getReservationMoreDetails: "{{ route('admin.reservation.show', ':id') }}",
        fetchReservations: "{{ route('admin.reservation.fetch') }}",
        getSummary: "{{ route('admin.reservation.get-summary') }}",
    };

    // Ensure all status keys are included for translations
    window.translations = {
    status: {
        pending: @json(__('pages.admin.reservation.statuses.pending')),
        confirmed: @json(__('pages.admin.reservation.statuses.confirmed')),
        completed: @json(__('pages.admin.reservation.statuses.completed')),
        cancelled: @json(__('pages.admin.reservation.statuses.cancelled')),
        expired: @json(__('pages.admin.reservation.statuses.expired')),
    }
};

</script>

@endsection
