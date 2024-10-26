@extends('layouts.app')
@section('title', 'Home Page')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
         <!-- Start col -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Applicants</h5>
                        <h4 class="mb-0">{{ $totalApplicants }}</h4>
                     </div>
                  </div>
               </div>
               
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Pending</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning "></i> {{ $totalPendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Preliminary Accepted</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather feather icon-check-circle text-success "></i> {{ $totalPreliminaryAcceptedCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
         <!-- Start col -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-award"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Males</h5>
                        <h4 class="mb-0">{{ $maleCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Pending</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning "></i> {{ $malePendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Preliminary Accepted</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather feather icon-check-circle text-success "></i> {{ $malePreliminaryAcceptedCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
         <!-- Start col -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-briefcase"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Females</h5>
                        <h4 class="mb-0">{{ $femaleCount }}</h4>
                     </div>
                  </div>
               </div>
               
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Pending</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning "></i> {{ $femalePendingCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Preliminary Accepted</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather feather icon-check-circle text-success "></i> {{ $femalePreliminaryAcceptedCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->
         <!-- Start col -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-book-open"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Final Accepted</h5>
                        <h4 class="mb-0">{{$totalFinalAcceptedCount}}</h4>
                     </div>
                  </div>
               </div>
               
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning "></i> {{ $maleFinalAcceptedCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather feather icon-check-circle text-success "></i> {{ $femaleFinalAcceptedCount }}</span>
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
<div class="row">
   <div class="col-lg-12">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
         <!-- Title on the Left -->
         <h2 class="page-title text-primary mb-2 mb-md-0">Applicants</h2>
         <!-- Toggle Button on the Right -->
         <div class="div">
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-search-plus"></i>
            </button>
            <!-- New Applicant Button -->
            <!-- Updated Export Button -->
<button class="btn btn-outline-primary ms-2" id="exportButton">
    <i class="fa fa-download"></i> Export Applicants
</button>
<button class="btn btn-outline-primary ms-2" id="exportReportn">
    <i class="fa fa-download"></i> Export Report
</button>
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
         <!-- Filters on the Right -->
         <div class="d-flex flex-column flex-md-row filters-container">
            <select id="categoryFilter" class="form-select mb-2 mb-md-0" >
               <option value="">Category</option>
               <option value="Category 1">Category 1</option>
               <option value="Category 2">Category 2</option>
               <option value="Category 3">Category 3</option>
            </select>
            <select id="statusFilter" class="form-select">
               <option value="">Status</option>
               <option value="Active">Active</option>
               <option value="Inactive">Inactive</option>
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
               <table id="default-datatable" class="display table table-bordered table-hover">
                  <thead>
                     <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>National ID</th>
                        <th>Email</th>
                        <th>Profile Completed</th>
                        <th>Registration Date</th>
                        <!-- Fixed closing tag -->
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($applicants as $applicant)
                     <tr>
                        <td>{{ $applicant->universityArchive->name_en ?? 'N/A' }}</td>
                        <!-- Correct relationship name -->
                        <td>{{ $applicant->universityArchive->gender ?? 'N/A' }}</td>
                        <!-- Correct relationship name -->
                        <td>{{ $applicant->universityArchive->national_id ?? 'N/A' }}</td>
                        <td>{{ $applicant->email }}</td>
                        <td>
                           @if ($applicant->has_student_profile)
                           <span class="badge badge-success badge-lg">Yes</span>
                           @else
                           <span class="badge badge-danger badge-lg">No</span>
                           @endif
                        </td>
                        <td>{{ $applicant->created_at->format('F j, Y, g:i A') }}</td>
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
<!-- End row -->
@endsection
@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/jszip.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/pdfmake.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/vfs_fonts.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.html5.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.print.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/custom/custom-table-datatable.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
            $('#exportButton').on('click', function() {
                const originalText = $(this).html(); // Store original button text
                $(this).html('<i class="fa fa-spinner fa-spin"></i> Downloading...'); // Change button text and add spinner
                $(this).addClass('loading'); // Disable button interactions

                // Get the CSRF token from the meta tag
                const csrfToken = '{{ csrf_token() }}'; // Correctly embed CSRF token

                // Make the fetch request to export applicants
                fetch('{{ route('export.applicants') }}', {
                    method: 'GET', // Use GET request
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Indicate an AJAX request
                        'X-CSRF-Token': csrfToken // Include CSRF token for security
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.blob(); // Get the response as a Blob
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob); // Create a URL for the Blob
                    const a = document.createElement('a'); // Create an anchor element
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'applicants.xlsx'; // Set the desired filename
                    document.body.appendChild(a); // Append the anchor to the body
                    a.click(); // Trigger the download
                    window.URL.revokeObjectURL(url); // Clean up the URL object
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                })
                .finally(() => {
                    $(this).html(originalText); // Reset the button text to original
                    $(this).removeClass('loading'); // Enable button interactions
                });
            });


            $('#exportReportn').on('click', function() {
                const originalText = $(this).html(); // Store original button text
                $(this).html('<i class="fa fa-spinner fa-spin"></i> Downloading...'); // Change button text and add spinner
                $(this).addClass('loading'); // Disable button interactions

                // Get the CSRF token from the meta tag
                const csrfToken = '{{ csrf_token() }}'; // Correctly embed CSRF token

                // Make the fetch request to export applicants
                fetch('{{ route('export.applicants.pdf') }}', {
                    method: 'GET', // Use GET request
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Indicate an AJAX request
                        'X-CSRF-Token': csrfToken // Include CSRF token for security
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.blob(); // Get the response as a Blob
                    }
                    throw new Error('Network response was not ok.');
                })
                .then(blob => {
                    const url = window.URL.createObjectURL(blob); // Create a URL for the Blob
                    const a = document.createElement('a'); // Create an anchor element
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'applicants.pdf'; // Set the desired filename
                    document.body.appendChild(a); // Append the anchor to the body
                    a.click(); // Trigger the download
                    window.URL.revokeObjectURL(url); // Clean up the URL object
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                })
                .finally(() => {
                    $(this).html(originalText); // Reset the button text to original
                    $(this).removeClass('loading'); // Enable button interactions
                });
            });

           


           
        });
</script>

@endsection