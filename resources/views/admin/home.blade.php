@extends('layouts.admin') 

@section('title', __('Welcome Message'))

@section('links')
<!-- Page-specific CSS -->
<link href="{{ asset('plugins/apexcharts/apexcharts.css') }}" rel="stylesheet">
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
      <!-- Students Card -->
      <div class="col-lg-3 col-md-6 mb-2">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row align-items-center">
                  <div class="col-7 text-start  mt-2 mb-2">
                     <h5 class="card-title font-14">@lang('Total Students')</h5>
                     <h4 class="mb-0" id="totalStudentsCount">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </h4>
                  </div>
                  <div class="col-5 text-end">
                  <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-users" aria-hidden="true" style="color: white;"></i>
                  </span>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <div class="row align-items-center">
                  <div class="col-8">
                     <div class="font-13" id="lastUpdateStudents">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4 text-end ">
                     <span class="badge badge-secondary">
                        <i class="feather icon-info"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Males Card -->
      <div class="col-lg-3 col-md-6 mb-2">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row align-items-center">
                  <div class="col-7 text-start  mt-2 mb-2">
                     <h5 class="card-title font-14">@lang('Males')</h5>
                     <h4 class="mb-0" id="totalMaleStudentsCount">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </h4>
                  </div>
                  <div class="col-5 text-end">
                  <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-male" aria-hidden="true" style="color: white;"></i>
                  </span>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <div class="row align-items-center">
                  <div class="col-8">
                     <div class="font-13" id="lastUpdateMaleStudents">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4 text-end">
                     <span class="badge badge-secondary">
                        <i class="feather icon-info"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Females Card -->
      <div class="col-lg-3 col-md-6 mb-2">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row align-items-center">
                  <div class="col-7 text-start  mt-2 mb-2">
                     <h5 class="card-title font-14">@lang('Females')</h5>
                     <h4 class="mb-0" id="totalFemaleStudentsCount">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </h4>
                  </div>
                  <div class="col-5 text-end">
                  <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-female" aria-hidden="true" style="color: white;"></i>
                  </span>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <div class="row align-items-center">
                  <div class="col-8">
                     <div class="font-13" id="lastUpdateFemaleStudents">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4 text-end ">
                     <span class="badge badge-secondary">
                        <i class="feather icon-info"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- Occupancy Rate Card -->
      <div class="col-lg-3 col-md-6 mb-2">
         <div class="card m-b-30">
            <div class="card-body">
               <div class="row align-items-center">
                  <div class="col-7 text-start  mt-2 mb-2">
                     <h5 class="card-title font-14">@lang('Occupancy Rate')</h5>
                     <h4 class="mb-0" id="occupancyRate">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </h4>
                  </div>
                  <div class="col-5 text-end">
                  <span class="action-icon badge badge-primary rounded-circle p-1 d-inline-flex align-items-center justify-content-center">
                     <i class="fa fa-pie-chart" aria-hidden="true" style="color: white;"></i>
                  </span>
                  </div>
               </div>
            </div>
            <div class="card-footer">
               <div class="row align-items-center">
                  <div class="col-8">
                     <div class="font-13" id="lastUpdateOccupancyRate">
                        <div class="spinner-border spinner-border-sm text-primary d-inline-block" role="status">
                           <span class="visually-hidden">@lang('Loading...')</span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4 text-end ">
                     <span class="badge badge-secondary">
                        <i class="feather icon-info"></i>
                     </span>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- End row -->

      <!-- Buildings Section -->
      <div class="row {{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
   <div class="col-lg-12 col-xl-9">
      <div class="card m-b-30">
         <div class="card-header">
            <h5 class="card-title mb-0">@lang('Buildings')</h5>
         </div>
         <div class="card-body text-center z-0">
            <div class="building-slider" id="buildingSlider">
               <!-- Dynamic content will be populated here by JavaScript -->
            </div>
         </div>
      </div>
   </div>
</div>

   </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('js/pages/admin-home.js') }}"></script>
<script>

  window.routes = {
    fetchStats: @json(route('admin.home.stats')),
  };
</script>

@endsection
