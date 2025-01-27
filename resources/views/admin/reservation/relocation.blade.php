@extends('layouts.admin')

@section('title', __('Relocation'))

@section('links')

<style>
   .card-img-top {
      width: 100%;
      height: 100%;
      object-fit: contain;
   }

   .option-card {
      position: relative;
      height: 250px;
      transition: all 0.3s ease;
      cursor: pointer;
      overflow: hidden;
      border: 2px solid transparent; /* Default border */
   }

   .card-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(140, 47, 57, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 0.9;
   }

   .card-overlay.show {
      display: flex;
   }

   .check-icon {
      font-size: 40px;
      color: white;
   }

   .active-card {
      border-color: #8C2F39;
   }

   .form-card {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
   }

   .form-card .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e0e0e0;
   }

   .form-card .card-title {
      margin-bottom: 0;
   }

   .btn-primary {
      background-color: #8C2F39;
      border-color: #8C2F39;
   }

   .btn-primary:hover {
      background-color: #7a2832;
      border-color: #7a2832;
   }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
   <div class="col-lg-12">
      <div class="card form-card">
         <div class="card-header">
            <h4 class="card-title">@lang('Relocation')</h4>
            <p class="text-muted mb-0">@lang('Choose the action you would like to perform:')</p>
         </div>
         <div class="card-body">
            <!-- Selection Cards -->
            <div class="row justify-content-center">
               <div class="col-md-5 mb-4 text-center">
                  <div class="card text-center border-primary option-card" id="relocateCard" onclick="showForm('relocate', this)">
                     <div class="card-overlay">
                        <i class="fa fa-check check-icon"></i>
                     </div>
                     <img src="{{ asset('images/reservation/relocate_empty_room.svg') }}" alt="@lang('Relocate')" class="card-img-top">
                  </div>
                  <h5 class="card-title mt-3">@lang('Relocate Empty Room')</h5>
               </div>
               <div class="col-md-5 mb-4 text-center">
                  <div class="card text-center border-primary option-card" id="swapCard" onclick="showForm('swap', this)">
                     <div class="card-overlay">
                        <i class="fa fa-check check-icon"></i>
                     </div>
                     <img src="{{ asset('images/reservation/swap_rooms.svg') }}" alt="@lang('Swap')" class="card-img-top">
                  </div>
                  <h5 class="card-title mt-3">@lang('Swap Rooms')</h5>
               </div>
            </div>

           <!-- Form for Relocation (Empty Room) -->
           <div id="relocationForm" style="display:none;">
               <form action="#" method="POST">
                  @csrf
                  <!-- Hidden input for reservation id (Resident 1) -->
                  <input type="hidden" name="reservation_id" id="reservation_id_1">
                  <div class="row">
                     <!-- Left Column for Resident National ID -->
                     <div class="col-md-6 mb-4">
                        <div class="card border-primary form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Resident National ID')</h5>
                           </div>
                           <div class="card-body">
                              <div class="input-group">
                                 <input type="text" class="form-control" name="resident_nid" id="resident_nid_1" placeholder="@lang('Resident National ID')" required>
                                 <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-id-card"></i></span>
                                 </div>
                              </div>
                              <div id="residentDetails_1" class="mt-3">
                                 <!-- Fetched details will appear here for Resident 1 -->
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- Right Column for Room Selection (Building, Apartment, Room) -->
                     <div class="col-md-6">
                        <div class="card border-primary mb-4 form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Select Building')</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" id="building_select" required>
                                 <option value="">@lang('Select Building')</option>
                              </select>
                           </div>
                        </div>
                        <div class="card border-primary mb-4 form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Select Apartment')</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" id="apartment_select" required>
                                 <option value="">@lang('Select Apartment')</option>
                              </select>
                           </div>
                        </div>
                        <div class="card border-primary mb-4 form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Select New Room')</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" name="new_room" id="room_select" required>
                                 <option value="">@lang('Select New Room')</option>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Submit Button -->
                  <div class="text-center mt-4">
                     <button type="submit" class="btn btn-primary">@lang('Relocate') <i class="fa fa-arrow-right"></i></button>
                  </div>
               </form>
            </div>

            <!-- Swap Residents Form -->
            <div id="swapForm" style="display:none;">
               <form action="#" method="POST">
                  @csrf
                  <input type="hidden" name="reservation_id_1_swap" id="reservation_id_1_swap">
                  <input type="hidden" name="reservation_id_2_swap" id="reservation_id_2_swap">
                  <div class="row">
                     <div class="col-md-6 mb-4">
                        <div class="card border-secondary form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Resident National ID')</h5>
                           </div>
                           <div class="card-body">
                              <div class="input-group">
                                 <input type="text" class="form-control" name="resident_nid" id="resident_nid_1_swap" placeholder="@lang('Resident National ID')" required>
                                 <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-id-card"></i></span>
                                 </div>
                              </div>
                           </div>
                           <div id="residentDetailsSwap1" class="mt-3">
                                 <!-- Fetched details will appear here for Resident 1 -->
                              </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="card border-secondary form-card">
                           <div class="card-header">
                              <h5 class="card-title">@lang('Resident National ID')</h5>
                           </div>
                           <div class="card-body">
                              <div class="input-group">
                                 <input type="text" class="form-control" name="resident2_nid" id="resident_nid_2_swap" placeholder="@lang('Resident National ID')" required>
                                 <div class="input-group-append">
                                    <span class="input-group-text"><i class="fa fa-id-card"></i></span>
                                 </div>
                              </div>
                           </div>
                           <div id="residentDetailsSwap2" class="mt-3">
                                 <!-- Fetched details will appear here for Resident 1 -->
                              </div>
                        </div>
                     </div>
                  </div>
                  <div class="text-center mt-4">
                     <button type="submit" class="btn btn-primary">@lang('Swap Rooms') <i class="fa fa-exchange-alt"></i></button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pages/relocation.js') }}"></script>

<script>
window.routes = {
    fetchEmptyBuildings: '{{ route('admin.unit.building.fetch-empty') }}',
    fetchEmptyApartments: (buildingId) => '{{ route('admin.unit.apartment.fetch-empty', ['buildingId' => 'BUILDING_ID']) }}'.replace('BUILDING_ID', buildingId),
    fetchEmptyRooms: (apartmentId) => '{{ route('admin.unit.room.fetch-empty', ['apartmentId' => 'APARTMENT_ID']) }}'.replace('APARTMENT_ID', apartmentId),
    residentDetails: (userId) => '{{ route('admin.reservation.relocation.show', ':userId') }}'.replace(':userId', userId),
    relocation: '{{route('admin.reservation.relocation.reallocate')}}',
    swapReservation: '{{route('admin.reservation.relocation.swap')}}'
};
</script>
@endsection