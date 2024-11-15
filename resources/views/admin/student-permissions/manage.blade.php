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
    .form-control-sm {
        width: auto;
        display: inline-block;
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<!-- Start row -->
<div class="row">
   <div class="col-lg-12">
      <div class="card m-b-30">
         <div class="card-body">
            <h2 class="page-title text-primary mb-3">Manage Permissions</h2>

            <!-- Create New Permission Form -->
            <form action="{{ route('admin.student-permissions.store') }}" method="POST">
                @csrf
                <div class="d-flex mb-3">
                    <input type="text" name="name" class="form-control me-2" placeholder="Permission Name" required>
                    <input type="text" name="category" class="form-control me-2" placeholder="Category" required>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                </div>
            </form>

            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
         </div>
      </div>
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
                                <tr>
                                    <form action="{{ route('admin.student-permissions.update', $permission->id) }}" method="POST" class="d-flex">
                                        @csrf
                                        <td>
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $permission->name }}" required>
                                        </td>
                                        <td>
                                            <input type="text" name="category" class="form-control form-control-sm" value="{{ $permission->category }}" required>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-success btn-sm">Update</button>
                                        </td>
                                    </form>
                                    <td>
                                        <form action="{{ route('admin.student-permissions.destroy', $permission->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
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
<!-- End row -->
@endsection

@section('scripts')
<!-- DataTables JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

<!-- SweetAlert JS -->
<script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        // Handle Delete Confirmation
        $('.btn-danger').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // DataTable Initialization
        $('#default-datatable').DataTable({
            responsive: true
        });
    });
</script>
@endsection
