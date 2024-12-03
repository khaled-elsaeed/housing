@extends('layouts.admin')
@section('title', 'Reservation Relocation')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
<meta name="csrf-token" content="{{ csrf_token() }}">

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
</style>
@endsection

@section('content')
<div class="row justify-content-center">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-header">
            <h4 class="card-title">Resident Relocation</h4>
            <p class="text-muted mb-0">Choose whether to relocate to an empty room or swap rooms with another resident.</p>
         </div>
         <div class="card-body">
            <!-- Selection Cards -->
            <div class="row justify-content-center">
               <div class="col-md-5 mb-4 text-center">
                  <div class="card text-center border-primary option-card" id="relocateCard" onclick="showForm('relocate', this)">
                     <div class="card-overlay">
                        <i class="fa fa-check check-icon"></i>
                     </div>
                     <img src="{{ asset('images/reservation/relocate_empty_room.svg') }}" alt="Relocate" class="card-img-top">
                  </div>
                  <h5 class="card-title mt-3">Relocate to an Empty Room</h5>

               </div>
               <div class="col-md-5 mb-4 text-center">
                  <div class="card text-center border-primary option-card" id="swapCard" onclick="showForm('swap', this)">
                     <div class="card-overlay">
                        <i class="fa fa-check check-icon"></i>
                     </div>
                     <img src="{{ asset('images/reservation/swap_rooms.svg') }}" alt="Swap" class="card-img-top">
                  </div>
                  <h5 class="card-title mt-3">Swap Rooms with Another Resident</h5>

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
                     <div class="col-md-6">
                        <div class="card border-primary">
                           <div class="card-header">
                              <h5 class="card-title">Resident National ID</h5>
                           </div>
                           <div class="card-body">
                              <input type="text" class="form-control" name="resident_nid" id="resident_nid_1" placeholder="Enter National ID" required>
                              <div id="residentDetails_1" class="mt-3">
                                 <!-- Fetched details will appear here for Resident 1 -->
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- Right Column for Room Selection (Building, Apartment, Room) -->
                     <div class="col-md-6">
                        <!-- Room Selection Card for Building -->
                        <div class="card border-primary mb-4">
                           <div class="card-header">
                              <h5 class="card-title">Select Building</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" id="building_select" required>
                                 <option value="">Select Building</option>
                                 <!-- Empty room options will be dynamically loaded -->
                              </select>
                           </div>
                        </div>
                        <!-- Room Selection Card for Apartment -->
                        <div class="card border-primary mb-4">
                           <div class="card-header">
                              <h5 class="card-title">Select Apartment</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" id="apartment_select" required>
                                 <option value="">Select Apartment</option>
                                 <!-- Empty room options will be dynamically loaded -->
                              </select>
                           </div>
                        </div>
                        <!-- Room Selection Card for New Room -->
                        <div class="card border-primary mb-4">
                           <div class="card-header">
                              <h5 class="card-title">Select New Room</h5>
                           </div>
                           <div class="card-body">
                              <select class="form-control" name="new_room" id="room_select" required>
                                 <option value="">Select an Empty Room</option>
                                 <!-- Empty room options will be dynamically loaded -->
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Submit Button -->
                  <div class="text-center mt-4">
                     <button type="submit" class="btn btn-primary">Relocate Resident</button>
                  </div>
               </form>
            </div>
            <!-- Form for Swapping Rooms -->
            <div id="swapForm" style="display:none;">
               <form action="#" method="POST">
                  @csrf
                  <!-- Hidden input for reservation id (Resident 1) -->
                  <input type="hidden" name="reservation_id_1_swap" id="reservation_id_1_swap">
                  <!-- Hidden input for reservation id (Resident 2) -->
                  <input type="hidden" name="reservation_id_2_swap" id="reservation_id_2_swap">
                  <div class="row">
                     <!-- Resident 1 National ID Card -->
                     <div class="col-md-6">
                        <div class="card border-secondary">
                           <div class="card-header">
                              <h5 class="card-title">First Resident National ID</h5>
                           </div>
                           <div class="card-body">
                              <input type="text" class="form-control" name="resident_nid" id="resident_nid_1_swap" placeholder="Enter National ID" required>
                              <div id="resident1Details" class="mt-3">
                                 <!-- Resident 1 details will be fetched dynamically -->
                              </div>
                           </div>
                        </div>
                     </div>
                     <!-- Resident 2 National ID Card -->
                     <div class="col-md-6">
                        <div class="card border-secondary">
                           <div class="card-header">
                              <h5 class="card-title">Second Resident National ID</h5>
                           </div>
                           <div class="card-body">
                              <input type="text" class="form-control" name="resident2_nid" id="resident_nid_2_swap" placeholder="Enter National ID" required>
                              <div id="resident2Details" class="mt-3">
                                 <!-- Resident 2 details will be fetched dynamically -->
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <!-- Submit Button -->
                  <div class="text-center mt-4">
                     <button type="submit" class="btn btn-primary">Swap Residents</button>
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