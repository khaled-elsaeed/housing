@extends('layouts.admin')
@section('title', 'Applicants')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
   .loading {
   pointer-events: none; 
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
         <!-- Start col - Total Invoices -->
         <div class="col-lg-3 col-md-5 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Total Invoices</h5>
                        <h4 class="mb-0" id="totalInvoice">
                           <!-- Loading Spinner -->
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalMaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalFemaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col - Paid Invoices -->
         <div class="col-lg-3 col-md-5 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Paid Invoices</h5>
                        <h4 class="mb-0" id="totalPaidInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalPaidMaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalPaidFemaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col - Unpaid Invoices -->
         <div class="col-lg-3 col-md-5 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-danger-inverse me-0"><i class="feather icon-x-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Unpaid Invoices</h5>
                        <h4 class="mb-0" id="totalUnpaidInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalUnpaidMaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalUnpaidFemaleInvoice">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col - Accepted Payments -->
         <div class="col-lg-3 col-md-5 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-check-circle"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Accepted Payments</h5>
                        <h4 class="mb-0" id="totalAcceptedPayments">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalAcceptedMalePayments">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13" id="totalAcceptedFemalePayments">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </span>
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


<!-- End row -->
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <!-- Title on the Left -->
         <h2 class="page-title text-primary mb-2 mb-md-0">Applicant Payments</h2>
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
                     <i class="fa fa-file-excel"></i> Applicants (Excel)
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
         <!-- Search Box with Icon on the Left -->
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control search-input" id="searchBox" placeholder="Search..." />
         </div>

         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="genderFilter" class="form-select mb-2 mb-md-0">
            <option value="">Select Gender</option>
               <option value="male">Male</option>
               <option value="female">Female</option>
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
                        <th>Name</th>
                        <th>National ID</th>
                        <th>Faculty</th>
                        <th>Mobile</th>
                        <th>Invoice Status</th> <!-- New column for Invoice Status -->
                        <th>Payment Status</th> <!-- New column for Invoice Status -->

                        <th>Actions</th> <!-- Actions column -->
                     </tr>
                  </thead>
                  <tbody>
                     <!-- This will be populated dynamically via DataTables -->
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <!-- End col -->
</div>
<!-- End row -->


<!-- Applicant Details Modal -->
<div class="modal fade" id="applicantDetailsModal" tabindex="-1" role="dialog" aria-labelledby="applicantDetailsModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl modal-dialog-centered" role="payment">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="applicantDetailsModalLabel">Applicant Payments</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div id="payments-list">
               <!-- Details and payments will be dynamically loaded here -->
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
<script src="{{ asset('js/pages/invoices.js') }}"></script>
<script>

   
window.routes = {
    exportExcel: "{{ route('admin.invoices.export-excel') }}",  // Excel export route for invoices
    fetchInvoicePayment: "{{ route('admin.invoices.show', ':id') }}",  // Fetch details of a specific applicant's payment
    fetchInvoices: "{{ route('admin.invoices.fetch') }}",  // Fetch applicants' payment data (for DataTables)
    fetchStats: "{{ route('admin.invoices.stats') }}",  // Fetch statistics related to invoices
    updateInvoiceStatus : "{{ route('admin.invoices.payment.update', ':paymentId')}}"
};

</script>
@endsection