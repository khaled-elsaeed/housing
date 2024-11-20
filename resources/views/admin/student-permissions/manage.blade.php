@extends('layouts.admin')

@section('title', 'Permissions')

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
            <!-- Start col for Total Permissions -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-file-text"></i></span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Total Permissions</h5>
                                <h4 class="mb-0" id="totalPermissions">{{ $totalPermissions }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-6 text-start">
                                <span class="font-13">Approved</span>
                            </div>
                            <div class="col-6 text-end">
                                <span class="font-13"><i class="feather icon-check-circle text-success"></i> <span id="approvedPermissionsCount">{{ $approvedPermissionsCount }}</span></span>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-9 text-start">
                                <span class="font-13">Denied</span>
                            </div>
                            <div class="col-3 text-end">
                                <span class="font-13"><i class="feather icon-x-circle text-danger"></i> <span id="deniedPermissionsCount">{{ $deniedPermissionsCount }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->

            <!-- Start col for Pending Permissions -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-warning-inverse me-0"><i class="feather icon-clock"></i></span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Pending Permissions</h5>
                                <h4 class="mb-0" id="pendingPermissionsCount">{{ $pendingPermissionsCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->

            <!-- Start col for Late Arrival Permissions -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-info-inverse me-0"><i class="feather icon-log-in"></i></span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Late Arrivals</h5>
                                <h4 class="mb-0" id="lateArrivalPermissionsCount">{{ $lateArrivalPermissionsCount }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End col -->

            <!-- Start col for Extended Stay Permissions -->
            <div class="col-lg-3 col-md-6 mb-2">
                <div class="card m-b-30">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-5">
                                <span class="action-icon badge badge-secondary-inverse me-0"><i class="feather icon-log-out"></i></span>
                            </div>
                            <div class="col-7 text-end mt-2 mb-2">
                                <h5 class="card-title font-14">Extended Stays</h5>
                                <h4 class="mb-0" id="extendedStayPermissionsCount">{{ $extendedStayPermissionsCount }}</h4>
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

<!-- Create New Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.student-permissions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPermissionModalLabel">Create New Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="permissionName" class="form-label">Permission Name</label>
                        <input type="text" id="permissionName" name="name" class="form-control" placeholder="Enter Permission Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="permissionCategory" class="form-label">Category</label>
                        <input type="text" id="permissionCategory" name="category" class="form-control" placeholder="Enter Category" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Permission Modal -->
<div class="modal fade" id="updatePermissionModal" tabindex="-1" aria-labelledby="updatePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.student-permissions.store') }}" method="POST" id="updatePermissionForm">
                @csrf
                <input type="hidden" name="_method" value="PUT"> <!-- Spoofing PUT method for update -->
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePermissionModalLabel">Update Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="updatePermissionName" class="form-label">Permission Name</label>
                        <input type="text" id="updatePermissionName" name="name" class="form-control" placeholder="Enter Permission Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="updatePermissionCategory" class="form-label">Category</label>
                        <input type="text" id="updatePermissionCategory" name="category" class="form-control" placeholder="Enter Category" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
    <!-- Title on the Left -->
    <h2 class="page-title text-primary mb-2 mb-md-0">Manage Permissions</h2>
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
<button type="button" class="btn btn-outline-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
    <i class="fa fa-plus-circle"></i> Add Permission
</button>

    </div>
</div>

<!-- Permissions Table -->
<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30 table-card">
            <div class="card-body table-container">
                <div class="table-responsive">
                    <table id="default-datatable" class="display table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    @foreach($permissions as $permission)
        <tr data-id="{{ $permission->id }}">
            <td>{{ $permission->name }}</td>
            <td>{{ $permission->category }}</td>
            <td>
                <button type="button" class="btn btn-success btn-sm edit-btn" data-id="{{ $permission->id }}">Update</button>
                <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="{{ $permission->id }}">Delete</button>
            </td>
        </tr>
    @endforeach
</tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {

// Initialize DataTable
const table = $('#default-datatable').DataTable();

// Handle form submission for creating a new permission
const addPermissionForm = document.querySelector('#addPermissionModal form');
addPermissionForm.addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission (which reloads the page)

    const formData = $(this).serialize(); // Serialize the form data for the POST request

    $.ajax({
        url: '{{ route("admin.student-permissions.store") }}',
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                // Hide the modal on success
                $('#addPermissionModal').modal('hide');  

                // Show success message using SweetAlert
                swal('Success!', 'Permission created successfully.', 'success').then(() => {
                    location.reload();  // Refresh the page to reflect the new permission
                });
            } else {
                // Handle error response if any
                swal('Error!', response.message || 'Failed to create permission.', 'error');
            }
        },
        error: function(xhr) {
            // Handle AJAX error
            let errorMessage = 'Failed to create permission.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message; // Custom error message from backend
            }
            swal('Error!', errorMessage, 'error');
        }
    });
});

// Handle Update Permission via AJAX
$(document).on('click', '.edit-btn', function() {
    const permissionId = $(this).data('id');  // Get the permission ID

    // Fetch the current data using the route name for 'edit' action
    $.ajax({
        url: '{{ route("admin.student-permissions.edit", ":id") }}'.replace(':id', permissionId),
        type: 'GET',
        success: function(response) {
            // Pre-fill the form with the current permission data
            $('#updatePermissionName').val(response.name);
            $('#updatePermissionCategory').val(response.category);

            // Set the form action for updating (use the 'update' route)
            $('#updatePermissionForm').attr('action', '{{ route("admin.student-permissions.update", ":id") }}'.replace(':id', permissionId));

            // Change modal title to "Update Permission"
            $('#updatePermissionModalLabel').text('Update Permission');
            
            // Show the modal
            $('#updatePermissionModal').modal('show');
        },
        error: function(xhr) {
            swal('Error!', 'Failed to fetch permission data.', 'error');
        }
    });
});

// Handle form submission for updating a permission
const updatePermissionForm = document.querySelector('#updatePermissionForm');
updatePermissionForm.addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent the default form submission (which reloads the page)

    const formData = $(this).serialize(); // Serialize the form data for the PUT request

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST', // Since we are spoofing PUT, we need POST for Laravel to handle it
        data: formData,
        success: function(response) {
            if (response.success) {
                // Hide the modal on success
                $('#updatePermissionModal').modal('hide');  

                // Show success message using SweetAlert
                swal('Success!', 'Permission updated successfully.', 'success').then(() => {
                    location.reload();  // Refresh the page to reflect the updated permission
                });
            } else {
                // Handle error response if any
                swal('Error!', response.message || 'Failed to update permission.', 'error');
            }
        },
        error: function(xhr) {
            // Handle AJAX error
            let errorMessage = 'Failed to update permission.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message; // Custom error message from backend
            }
            swal('Error!', errorMessage, 'error');
        }
    });
});

// Handle Delete Permission via AJAX
$(document).on('click', '.delete-btn', function() {
    const permissionId = $(this).data('id');  // Get the permission ID
    
    // Show confirmation dialog
    swal({
        title: 'Are you sure?',
        text: 'This will delete the permission permanently.',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
            // Make AJAX request to delete the permission using the route name for 'destroy'
            $.ajax({
                url: '{{ route("admin.student-permissions.destroy", ":id") }}'.replace(':id', permissionId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    // Remove the row from the table
                    $('tr[data-id="' + permissionId + '"]').remove();

                    // Show success message
                    swal('Success!', 'Permission deleted successfully.', 'success');
                },
                error: function(xhr) {
                    swal('Error!', 'Failed to delete permission.', 'error');
                }
            });
        }
    });
});

});


</script>
@endsection
