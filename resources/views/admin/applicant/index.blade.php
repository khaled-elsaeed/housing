@extends('layouts.admin')

@section('title', __('pages.admin.applicants.Applicants'))

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
         <!-- Start col for Applicants -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.applicants.Applicants') }}</h5>
                        <h4 class="mb-0" id="totalApplicantsCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-address-card-o" aria-hidden="true" style="color: white;"></i>
                  </span>                     
               </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="maleCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Female') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="femaleCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col for Applicants -->

         <!-- Start col for Pending -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.applicants.Pending') }}</h5>
                        <h4 class="mb-0" id="totalPendingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-clock-o" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="malePendingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Female') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="femalePendingCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col for Pending -->

         <!-- Start col for Preliminary Accepted -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.applicants.Preliminary Accepted') }}</h5>
                        <h4 class="mb-0" id="totalPreliminaryAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-check-circle" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="malePreliminaryAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Female') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="femalePreliminaryAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col for Preliminary Accepted -->

         <!-- Start col for Final Accepted -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.applicants.Final Accepted') }}</h5>
                        <h4 class="mb-0" id="totalFinalAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </h4>
                     </div>
                     <div class="col-5 text-end">
                     <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-check-square" aria-hidden="true" style="color: white;"></i>
                  </span>                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="maleFinalAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.applicants.Female') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="femaleFinalAcceptedCount">
                            <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                                <span class="visually-hidden">{{ __('pages.admin.applicants.Loading...') }}</span>
                            </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col for Final Accepted -->
      </div>
      <!-- End row -->
   </div>
   <!-- End col -->
</div>
<!-- End row -->

<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <!-- Title on the Left -->
         <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('pages.admin.applicants.Applicants') }}</h2>
         <!-- Toggle Button on the Right -->
         <!-- <div>
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
               <i class="fa fa-search-plus"></i>
            </button>
            <div class="btn-group ms-2" role="group" aria-label="Download Options">
               <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadBtn" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fa fa-download"></i> {{ __('pages.admin.applicants.Download') }}
               </button>
               <ul class="dropdown-menu">
                  <li>
                     <a class="dropdown-item" href="#" id="exportExcel">
                     <i class="fa fa-file-excel"></i> {{ __('pages.admin.applicants.Applicants (Excel)') }}
                     </a>
                  </li>
               </ul>
            </div>
         </div> -->
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
           <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('pages.admin.applicants.Search...') }}" />
        </div>
      </div>
   </div>
</div>

<div class="row">
    <!-- Start col -->
    <div class="col-lg-12">
        <div class="card m-b-30 table-card">
            <div class="card-body table-container">
                <div class="table-responsive">
                    <table id="default-datatable" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('pages.admin.applicants.Name') }}</th>
                                <th>{{ __('pages.admin.applicants.National ID') }}</th>
                                <th>{{ __('pages.admin.applicants.Faculty') }}</th>
                                <th>{{ __('pages.admin.applicants.Email') }}</th> <!-- Add Email column here -->
                                <th>{{ __('pages.admin.applicants.Mobile') }}</th>
                                <th>{{ __('pages.admin.applicants.Registration Date') }}</th>
                                <th>{{ __('pages.admin.applicants.Actions') }}</th>
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
    <!-- End col -->
</div>

<!-- Applicant Details Modal -->
<div class="modal fade" id="applicantDetailsModal" tabindex="-1" role="dialog" aria-labelledby="applicantDetailsModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="applicantDetailsModalLabel">{{ __('pages.admin.applicants.Applicant Details') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="form-group">
               <label for="faculty">{{ __('pages.admin.applicants.Faculty') }}</label>
               <input type="text" class="form-control" id="faculty" readonly>
            </div>
            <div class="form-group">
               <label for="program">{{ __('pages.admin.applicants.Program') }}</label>
               <input type="text" class="form-control" id="program" readonly>
            </div>
            <div class="form-group">
               <label for="score">{{ __('pages.admin.applicants.Score') }}</label>
               <input type="text" class="form-control" id="score" readonly>
            </div>
            <div class="form-group">
               <label for="percent">{{ __('pages.admin.applicants.Percent') }}</label>
               <input type="text" class="form-control" id="percent" readonly>
            </div>
            <div class="form-group">
               <label for="governorate">{{ __('pages.admin.applicants.Governorate') }}</label>
               <input type="text" class="form-control" id="governorate" readonly>
            </div>
            <div class="form-group">
               <label for="city">{{ __('pages.admin.applicants.City') }}</label>
               <input type="text" class="form-control" id="city" readonly>
            </div>
            <div class="form-group">
               <label for="street">{{ __('pages.admin.applicants.Street') }}</label>
               <input type="text" class="form-control" id="street" readonly>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('pages.admin.applicants.Close') }}</button>
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
<script src="{{ asset('js/pages/applicants.js') }}"></script>
<script>
   window.routes = {
      exportExcel: "{{ route('admin.applicants.export-excel') }}",
      getApplicantMoreDetails: "{{ route('admin.applicants.details', ':id') }}",
      fetchApplicants: "{{ route('admin.applicants.fetch') }}",
      fetchStats: "{{ route('admin.applicants.stats') }}"
   };
</script>
@endsection
