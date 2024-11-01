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
                        <h4 class="mb-0">{{$total_students}}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <span class="font-13">Updated {{$last_create_student}}</span>
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
                        <h4 class="mb-0">{{$male_students}}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <p class="font-13">Updated {{$last_created_male_student}}</p>
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
                        <h4 class="mb-0">{{$female_students}}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <p class="font-13">Updated {{$last_created_female_student}}</p>
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
                        <h4 class="mb-0">{{$occupancy_rate}} %</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-8">
                        <p class="font-13">Updated {{$last_updated_room}}</p>
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
<!-- Start row -->
<div class="row">
   <!-- Start col -->
<div class="col-lg-12 col-xl-9">
   <!-- Start row -->
   <div class="row">
      <!-- Start col -->
<div class="col-lg-12 col-xl-9">
    <div class="card m-b-30">
        <div class="card-header">
            <h5 class="card-title mb-0">Buildings</h5>
        </div>
        <div class="card-body text-center z-0">
            <div class="building-slider">
            @foreach ($buildings as $building)
    <div class="building-slider-item">
        <h4 class="my-0">{{ $building['name'] }}</h4>
        <div class="row align-items-center my-4 py-3">
            <div class="col-4 p-0">
                <h4>{{ $building['occupied'] }}</h4>
                <p class="mb-0">Occupied</p>
            </div>
            <div class="col-4 py-3 px-0 bg-primary-rgba rounded">
                <h4 class="text-primary">{{ $building['total'] }}</h4>
                <p class="text-primary mb-0">Total Bedrooms</p>
            </div>
            <div class="col-4 p-0">
                <h4>{{ $building['empty'] }}</h4>
                <p class="mb-0">Empty</p>
            </div>
        </div>
        <div class="progress mb-2 mt-2" style="height: 5px;">
            <div class="progress-bar" role="progressbar" style="width: {{ $building['total'] > 0 ? round(($building['occupied'] / $building['total']) * 100) : 0 }}%;" aria-valuenow="{{ $building['total'] > 0 ? round(($building['occupied'] / $building['total']) * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="row align-items-center">
            <div class="col-6 text-start">
                <p class="font-13">{{ $building['total'] > 0 ? round(($building['occupied'] / $building['total']) * 100) : 0 }}% Occupied</p>
            </div>
            <div class="col-6 text-end">
                <p class="font-13">{{ $building['occupied'] }}/{{ $building['total'] }} Bedrooms Occupied</p>
            </div>
        </div>
    </div>
@endforeach

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
@endsection
@section('scripts')
<!-- Page-specific JS -->
<!-- Apex js -->
<script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('plugins/apexcharts/irregular-data-series.js') }}"></script>
<!-- Slick js -->
<script src="{{ asset('plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('js/custom/custom-dashboard-school.js') }}"></script>
@endsection