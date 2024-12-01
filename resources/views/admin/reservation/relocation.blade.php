@extends('layouts.admin')
@section('title', 'Reservation Relocation')
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
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
<script>
function showForm(formType, selectedCard) {
      // Reset all cards
      document.querySelectorAll('.option-card').forEach(card => {
         card.classList.remove('active-card');
         card.querySelector('.card-overlay').classList.remove('show');
      });

      // Highlight selected card
      selectedCard.classList.add('active-card');
      selectedCard.querySelector('.card-overlay').classList.add('show');

      // Show corresponding form
      document.getElementById('relocationForm').style.display = formType === 'relocate' ? 'block' : 'none';
      document.getElementById('swapForm').style.display = formType === 'swap' ? 'block' : 'none';

      // Fetch data if needed
      if (formType === 'relocate') {
         fetchEmptyBuildings();
      }
   }
   
   
   async function fetchEmptyBuildings() {
   const url = '{{ route('admin.unit.building.fetch-empty') }}'; 
   try {
       const response = await $.ajax({
           url: url,
           method: 'GET',
           dataType: 'json',
       });
   
       if (response.success) {
           populateBuildingSelect(response.buildings); 
       } else {
           alert('No buildings available!');
       }
   } catch (error) {
       console.error('Error fetching buildings:', error);
       alert('Error fetching buildings!');
   }
   }
   
   function populateBuildingSelect(buildings) {
   const buildingSelect = $('#building_select');  // Select the dropdown element
   buildingSelect.empty();  // Clear previous options
   buildingSelect.append('<option value="">Select Building</option>'); // Default option
   
   buildings.forEach(building => {
       buildingSelect.append(`<option value="${building.id}"> Building ${building.number}</option>`);
   });
   
   // Bind the change event for the building selection
   buildingSelect.on('change', function () {
       fetchEmptyApartments($(this).val());  // Fetch apartments based on the selected building
   });
   }
   async function fetchEmptyApartments(buildingId) {
   // Construct the URL by replacing only the buildingId in the route string
   const url = '{{ route('admin.unit.apartment.fetch-empty', ['buildingId' => 'BUILDING_ID']) }}'.replace('BUILDING_ID', buildingId);
   
   try {
       const response = await $.ajax({
           url: url,  // Send the request to the URL with the buildingId
           method: 'GET',
           dataType: 'json'  // Assuming the response is JSON
       });
   
       if (response.success) {
           populateApartmentSelect(response.apartments); // Populate apartment options
       } else {
           alert('No apartments available!');
       }
   } catch (error) {
       console.error('Error fetching apartments:', error);
       alert('Error fetching apartments!');
   }
   }
   
   function populateApartmentSelect(apartments) {
   const apartmentSelect = $('#apartment_select');
   apartmentSelect.empty();
   apartmentSelect.append('<option value="">Select Apartment</option>');  // Default option
   
   apartments.forEach(apartment => {
       apartmentSelect.append(`<option value="${apartment.id}">Apartment ${apartment.number}</option>`);
   });
   
   // Bind the change event for apartment selection
   apartmentSelect.on('change', function () {
       const apartmentId = $(this).val();
       if (apartmentId) {
           fetchEmptyRooms(apartmentId);  // Fetch rooms based on selected apartment
       }
   });
   }
   
   async function fetchEmptyRooms(apartmentId) {
   // Construct the URL by replacing only the apartmentId in the route string
   const url = '{{ route('admin.unit.room.fetch-empty', ['apartmentId' => 'APARTMENT_ID']) }}'.replace('APARTMENT_ID', apartmentId);
   
   try {
       const response = await $.ajax({
           url: url,  // Send the request to the URL with the apartmentId
           method: 'GET',
           dataType: 'json'  // Assuming the response is JSON
       });
   
       if (response.success) {
           populateRoomSelect(response.rooms); // Populate room options
       } else {
           alert('No rooms available!');
       }
   } catch (error) {
       console.error('Error fetching rooms:', error);
       alert('Error fetching rooms!');
   }
   }
   
   function populateRoomSelect(rooms) {
   const roomSelect = $('#room_select');
   roomSelect.empty();
   roomSelect.append('<option value="">Select Room</option>');  // Default option
   
   rooms.forEach(room => {
       roomSelect.append(`<option value="${room.id}">Room ${room.number}</option>`);
   });
   }
   
   
   
   // Fetch Resident Details for Relocate when National ID is entered (Resident 1)
   $('#resident_nid_1').on('keyup', function() {
   let nid = $(this).val();
   let url = '{{ route('admin.reservation.relocation.show', ':userId') }}'.replace(':userId', nid);
   
   if (nid.length == 14) {  
       $.get(url, function(data) {
           if (data.success) {
               // Populate resident details
               $('#residentDetails_1').html(`
                   <div class="card border-info">
                       <div class="card-body">
                           <p><strong>Name:</strong> ${data.student.name_en}</p>
                           <p><strong>Faculty:</strong> ${data.student.faculty}</p>
                           <p><strong>Building:</strong> B-${data.reservation.building_number}</p>
                           <p><strong>Apartment:</strong> A-${data.reservation.apartment_number}</p>
                           <p><strong>Room:</strong> R-${data.reservation.room_number}</p>
                       </div>
                   </div>
               `);
   
               // Set the reservation ID to the hidden input
               $('#reservation_id_1').val(data.reservation.id);
           } else {
               $('#residentDetails_1').html('<p class="text-danger">Resident not found.</p>');
           }
       });
   } else {
       $('#residentDetails_1').html('');
       $('#new_room').html('<option value="">Select a room</option>');
   }
   });
   
   
   // Fetch Resident Details for Swap when National ID is entered (Resident 1)
   $('#resident_nid_1_swap').on('keyup', function() {
   let nid = $(this).val();
   let url = '{{ route('admin.reservation.relocation.show', ':userId') }}'.replace(':userId', nid);
   
   if (nid.length == 14) {  
       $.get(url, function(data) {
           if (data.success) {
               // Populate resident details for Resident 1
               $('#resident1Details').html(`
                   <div class="card border-info">
                       <div class="card-body">
                           <p><strong>Name:</strong> ${data.student.name_en}</p>
                           <p><strong>Faculty:</strong> ${data.student.faculty}</p>
                           <p><strong>Building:</strong> B-${data.reservation.building_number}</p>
                           <p><strong>Apartment:</strong> A-${data.reservation.apartment_number}</p>
                           <p><strong>Room:</strong> R-${data.reservation.room_number}</p>
                       </div>
                   </div>
               `);
   
               // Set the reservation ID for Resident 1 in the hidden input
               $('#reservation_id_1_swap').val(data.reservation.id);
           } else {
               $('#resident1Details').html('<p class="text-danger">Resident not found.</p>');
           }
       });
   } else {
       $('#resident1Details').html('');
   }
   });
   
   // Fetch Resident Details for Swap when National ID is entered (Resident 2)
   $('#resident_nid_2_swap').on('keyup', function() {
   let nid = $(this).val();
   let url = '{{ route('admin.reservation.relocation.show', ':userId') }}'.replace(':userId', nid);
   
   if (nid.length == 14) {  
       $.get(url, function(data) {
           if (data.success) {
               // Populate resident details for Resident 2
               $('#resident2Details').html(`
                   <div class="card border-info">
                       <div class="card-body">
                           <p><strong>Name:</strong> ${data.student.name_en}</p>
                           <p><strong>Faculty:</strong> ${data.student.faculty}</p>
                           <p><strong>Building:</strong> B-${data.reservation.building_number}</p>
                           <p><strong>Apartment:</strong> A-${data.reservation.apartment_number}</p>
                           <p><strong>Room:</strong> R-${data.reservation.room_number}</p>
                       </div>
                   </div>
               `);
   
               // Set the reservation ID for Resident 2 in the hidden input
               $('#reservation_id_2_swap').val(data.reservation.id);
           } else {
               $('#resident2Details').html('<p class="text-danger">Resident not found.</p>');
           }
       });
   } else {
       $('#resident2Details').html('');
   }
   });
   
   
   
   
   // Handle Relocation Form Submission
   $('#relocationForm form').on('submit', function(event) {
   event.preventDefault();
   let newRoom = $('#room_select').val();
   let reservationId = $('#reservation_id_1').val();  // Get the reservation ID
   
   // Send AJAX request for relocation
   $.ajax({
    url: '{{route('admin.reservation.relocation.reallocate')}}',
    method: 'POST',
       data: {
           _token: '{{ csrf_token() }}',
           room_id: newRoom,
           reservation_id: reservationId  // Send reservation ID with the request
       },
       success: function(response) {
           // Handle success (e.g., show a success message, reset form, etc.)
           alert('Relocation successful!');
       },
       error: function(response) {
           // Handle error (e.g., show an error message)
           alert('Error during relocation!');
       }
   });
   });
   
   
   // Handle Swap Form Submission
   $('#swapForm form').on('submit', function(event) {
   event.preventDefault();
   let reservation1Id = $('#reservation_id_1_swap').val();  // Get Resident 1's reservation ID
   let reservation2Id = $('#reservation_id_2_swap').val();  // Get Resident 2's reservation ID
   
   // Send AJAX request for room swap
   $.ajax({
       url: '{{route('admin.reservation.relocation.swap')}}',
       method: 'POST',
       data: {
           _token: '{{ csrf_token() }}',
           reservation_id_1: reservation1Id,  // Send Resident 1's reservation ID
           reservation_id_2: reservation2Id   // Send Resident 2's reservation ID
       },
       success: function(response) {
           // Handle success (e.g., show a success message, reset form, etc.)
           alert('Swap successful!');
       },
       error: function(response) {
           // Handle error (e.g., show an error message)
           alert('Error during swap!');
       }
   });
   });
   
   
</script>
@endsection