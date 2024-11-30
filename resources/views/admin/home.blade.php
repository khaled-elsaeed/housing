@extends('layouts.admin')

@section('title', 'Home')

@section('links')
<!-- Page-specific CSS -->
<!-- Apex css -->
<link href="{{ asset('plugins/apexcharts/apexcharts.css') }}" rel="stylesheet">
<!-- Slick css -->
<link href="{{ asset('plugins/slick/slick.css') }}" rel="stylesheet">
<link href="{{ asset('plugins/slick/slick-theme.css') }}" rel="stylesheet">
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
                        <h5 class="card-title font-14">Students</h5>
                        <h4 class="mb-0" id="totalStudentsCount">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <div class="font-13" id="lastUpdateStudents">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-4 text-end">
                        <span class="badge badge-primary-inverse">
                           <i class="feather icon-info me-1"></i>
                        </span>
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
                        <h4 class="mb-0" id="totalMaleStudentsCount">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <div class="font-13" id="lastUpdateMaleStudents">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-4 text-end">
                        <span class="badge badge-success-inverse">
                           <i class="feather icon-info me-1"></i>
                        </span>
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
                        <h4 class="mb-0" id="totalFemaleStudentsCount">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <div class="font-13" id="lastUpdateFemaleStudents">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-4 text-end">
                        <span class="badge badge-warning-inverse">
                           <i class="feather icon-info me-1"></i>
                        </span>
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
                        <h5 class="card-title font-14">Occupancy Rate</h5>
                        <h4 class="mb-0" id="occupancyRate">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <div class="font-13" id="lastUpdateOccupancyRate">
                           <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                              <span class="visually-hidden">Loading...</span>
                           </div>
                        </div>
                     </div>
                     <div class="col-4 text-end">
                        <span class="badge badge-secondary-inverse">
                           <i class="feather icon-info me-1"></i>
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

<!-- Buildings Section -->
<div class="row">
   <div class="col-lg-12 col-xl-9">
      <div class="card m-b-30">
         <div class="card-header">
            <h5 class="card-title mb-0">Buildings</h5>
         </div>
         <div class="card-body text-center z-0">
            <div class="building-slider" id="buildingSlider">
               <!-- Dynamic content will be populated here by JavaScript -->
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('scripts')
<!-- Page-specific JS -->
<!-- Apex js -->
<script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('plugins/apexcharts/irregular-data-series.js') }}"></script>
<!-- Slick js -->
<script src="{{ asset('plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('js/pages/admin-home.js') }}"></script>

<script>
   window.routes = {
      fetchStats: "{{ route('admin.home.stats') }}",  // Fixed the syntax error here
   }
</script>
@endsection
