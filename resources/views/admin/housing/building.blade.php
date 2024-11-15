@extends('layouts.admin')
@section('title', 'Buildings')
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

    /* Basic Styling */
.room-btn {
    display: inline-block;
    width: 50px;
    height: 50px;
    margin: 5px;
    text-align: center;
    line-height: 50px;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.3s;
}

.room-btn.selected {
    background-color: #4CAF50; /* Green for selected */
    color: white;
}

.room-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin-top: 10px;
}

.apartment-container {
    margin-bottom: 20px;
}

#apartmentSelection {
    margin-top: 20px;
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
         <!-- Start col for Total Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-home"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Total Buildings</h5>
                        <h4 class="mb-0">{{ $totalBuildings }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $activeBuildingsCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $inactiveBuildingsCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Male Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-success-inverse me-0"><i class="feather icon-user-check"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Male Buildings</h5>
                        <h4 class="mb-0">{{ $maleBuildingCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $maleActiveCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $maleInactiveCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Female Buildings -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-user-x"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Female Buildings</h5>
                        <h4 class="mb-0">{{ $femaleBuildingCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Active</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-check-circle text-success"></i> {{ $femaleActiveCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Inactive</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-x-circle text-danger"></i> {{ $femaleInactiveCount }}</span>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- End col -->

         <!-- Start col for Under Maintenance -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">Under Maintenance</h5>
                        <h4 class="mb-0">{{ $maintenanceCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Males</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $maleUnderMaintenanceCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-9 text-start">
                        <span class="font-13">Females</span>
                     </div>
                     <div class="col-3 text-end">
                        <span class="font-13"><i class="feather icon-clock text-warning"></i> {{ $femaleUnderMaintenanceCount }}</span>
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


<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
    <!-- Title on the Left -->
    <h2 class="page-title text-primary mb-2 mb-md-0">Buildings</h2>
    <!-- Toggle Button on the Right -->
    <div class="div">
        <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-search-plus"></i>
        </button>
        <div class="btn-group ms-2" role="group" aria-label="Download Options">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" id="downloadButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-download"></i> Download
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item" href="#" id="exportExcel">
                        <i class="fa fa-file-excel"></i> Buildings (Excel)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#" id="exportPDF">
                        <i class="fa fa-file-pdf"></i> Report (PDF)
                    </a>
                </li>
            </ul>
        </div>
        
       <!-- Button to Open Modal -->
<button type="button" class="btn btn-outline-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#buildingModal">
    <i class="fa fa-plus-circle"></i> Add Building
</button>

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
                <select id="statusFilter" class="form-select mb-2 mb-md-0">
                    <option value="">Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Under maintenance">Under maintenance</option>
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
                                <th>Building Number</th>
                                <th>Gender</th>
                                
                                <th>Max Apartments</th> <!-- New column for max apartments -->
                                <th>Status</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buildings as $building)
                                <tr>
                                    <td>Building {{ $building->number }}</td>
                                    <td>{{ ucfirst($building->gender) }}</td>
                                    <td>{{ $building->max_apartments }}</td> <!-- Display max apartments -->
                                    <td>{{ ucfirst(str_replace('_', ' ', $building->status)) }}</td>
                                    <td>{{ $building->description }}</td>
                                    <td>
                                        <!-- Edit Notes Button -->
                                        <button type="button" class="btn btn-round btn-warning-rgba" id="edit-notes-btn-{{ $building->id }}" title="Edit Notes">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        
                                        <!-- Edit Status Button -->
                                        <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-{{ $building->id }}" title="Edit Status">
                                            <i class="feather icon-settings"></i>
                                        </button>
                                        
                                        <!-- Delete Building Button -->
                                        <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-{{ $building->id }}" title="Delete Building">
                                            <i class="feather icon-trash-2"></i>
                                        </button>
                                    </td>
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

<!-- Modal -->
<div class="modal fade" id="buildingModal" tabindex="-1" aria-labelledby="buildingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buildingModalLabel">Create Building</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="buildingForm">
                <!-- Modal Form -->
<!-- Modal Content -->
<div class="modal-body">
    <form id="buildingForm">
        <!-- Building Info Section -->
        <div class="mb-3">
            <label for="buildingNumber" class="form-label">Building Number</label>
            <input type="text" class="form-control" id="buildingNumber" name="building_number" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <!-- Apartment and Room Selection -->
        <div class="mb-3">
            <label for="createApartments" class="form-label">Create Apartments</label>
            <input type="checkbox" id="createApartments">
        </div>

        <!-- Display Apartment Options When Checked -->
        <div id="maxRoomsContainer" style="display:none;">
            <label for="maxApartments">Max Apartments</label>
            <input type="number" id="maxApartments" name="max_apartments" min="1" required>
        </div>

        <!-- Double Room Section -->
        <div id="doubleRoomsSection" style="display:none;">
            <label for="doubleApartments">Number of Apartments with Double Rooms</label>
            <input type="number" id="doubleApartments" name="double_apartments" min="0">
            
            <!-- Dynamic Apartment Room Selector -->
            <div id="apartmentSelection"></div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Building</button>
                </div>
            </form>
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
<script src="{{ asset('js/pages/buildings.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const createApartmentsCheckbox = document.getElementById('createApartments');
    const maxRoomsContainer = document.getElementById('maxRoomsContainer');
    const doubleRoomsSection = document.getElementById('doubleRoomsSection');
    const doubleApartmentsInput = document.getElementById('doubleApartments');
    const apartmentSelectionContainer = document.getElementById('apartmentSelection');
    let apartments = [];

    // Toggle visibility of room-related fields based on checkbox
    createApartmentsCheckbox.addEventListener('change', function () {
        if (createApartmentsCheckbox.checked) {
            maxRoomsContainer.style.display = 'block'; // Show max rooms field
            doubleRoomsSection.style.display = 'block'; // Show double room section
        } else {
            maxRoomsContainer.style.display = 'none'; // Hide max rooms field
            doubleRoomsSection.style.display = 'none'; // Hide double room section
            apartmentSelectionContainer.innerHTML = ''; // Clear apartment selection
        }
    });

    // Show apartment selection inputs when double apartments are specified
    doubleApartmentsInput.addEventListener('input', function () {
        const numApartments = parseInt(doubleApartmentsInput.value);
        apartments = [];
        apartmentSelectionContainer.innerHTML = ''; // Clear previous selections
        
        if (numApartments > 0) {
            for (let i = 1; i <= numApartments; i++) {
                // Create apartment selection button (or grid)
                const apartmentDiv = document.createElement('div');
                apartmentDiv.classList.add('apartment-container');
                apartmentDiv.innerHTML = `<h5>Apartment ${i}</h5>`;

                // Add room selection buttons dynamically
                const roomGrid = document.createElement('div');
                roomGrid.classList.add('room-grid');
                
                for (let j = 1; j <= 5; j++) {  // Assume 5 rooms per apartment for now
                    const roomButton = document.createElement('button');
                    roomButton.classList.add('room-btn');
                    roomButton.textContent = `Room ${j}`;
                    roomButton.dataset.apartment = i;
                    roomButton.dataset.room = j;

                    // Add click event to mark room as "double"
                    roomButton.addEventListener('click', function() {
                        toggleRoomSelection(roomButton, i, j);
                    });
                    roomGrid.appendChild(roomButton);
                }

                apartmentDiv.appendChild(roomGrid);
                apartmentSelectionContainer.appendChild(apartmentDiv);
            }
        }
    });

    // Toggle Room Selection (double)
    function toggleRoomSelection(button, apartment, room) {
        button.classList.toggle('selected');
        
        // Update apartments array with double room info
        const apartmentIndex = apartments.findIndex(a => a.apartment === apartment);
        if (apartmentIndex === -1) {
            apartments.push({ apartment: apartment, rooms: [room] });
        } else {
            const roomIndex = apartments[apartmentIndex].rooms.indexOf(room);
            if (roomIndex === -1) {
                apartments[apartmentIndex].rooms.push(room);
            } else {
                apartments[apartmentIndex].rooms.splice(roomIndex, 1);
            }
        }
    }

    // Handle form submission
    const buildingForm = document.getElementById('buildingForm');
    buildingForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Prepare the form data
        const formData = {
            building_number: document.getElementById('buildingNumber').value,
            gender: document.getElementById('gender').value,
            max_apartments: document.getElementById('maxApartments').value,
            double_apartments: apartments, // The selected apartments and rooms
            _token: '{{ csrf_token() }}'  // CSRF token
        };

        // Send data to the backend using AJAX
        $.ajax({
            url: '{{ route('admin.buildings.store') }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Building Created',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Server Error',
                    text: 'There was an error saving the building. Please try again.'
                });
            }
        });
    });
});



</script>

@endsection
