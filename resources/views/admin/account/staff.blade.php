@extends('layouts.admin')
@section('title', __('Accounts Management'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<!-- Start row -->
<div class="row">
   <!-- Start col -->
   <div class="col-lg-12">
      <!-- Start row -->
      <div class="row">
         <!-- Start col for Total Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     
                     <div class="col-7 text-start mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('Total Users') }}</h5>
                        <h4 class="mb-0">{{ $totalUsersCount }}</h4>
                     </div>
                     <div class="col-5 text-end">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('Male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('Female') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $femaleTotalCount }}</span>
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
         <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('User Accounts') }}</h2>
         <div>
            <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse"
               data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            <i class="fa fa-search-plus"></i>
            </button>
           <!-- Add User Button -->
           <button class="btn btn-outline-success btn-sm toggle-btn" type="button" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="feather icon-plus me-2"></i> {{ __('Add User') }}
            </button>
         </div>
      </div>
   </div>
</div>
<!-- Search Filter -->
<div class="collapse" id="collapseExample">
   <div class="search-filter-container card card-body">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
         <!-- Search Box with Icon on the Left -->
         <div class="search-container d-flex align-items-center mb-3 mb-md-0">
            <div class="search-icon-container">
               <i class="fa fa-search search-icon"></i>
            </div>
            <input type="search" class="form-control border border-primary search-input" id="searchBox" placeholder="{{ __('Search...') }}" />
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
            <!-- Search Box -->
            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>{{ __('User Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>

                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @foreach($users as $user)
                     <tr>
                        <td>{{ $user->name ?? 'N/A' }}</td>
                        <td>{{ $user->email ?? 'N/A' }}</td>
                        <td>{{ $user->getRoleNames()->first() ?? 'N/A' }}</td>

                        <td>
                           @if($user->status === 'active') 
                           <span class="badge bg-success"> {{ __('active') }} </span>
                           @elseif($user->status === 'inactive') 
                           <span class="badge bg-danger">{{ __('Inactive') }} </span>
                           @else
                           <span class="badge bg-secondary">{{  __('Unknown')  }}</span>
                           @endif
                        </td>
                        <td>
                           <!-- Edit Button -->
                           <button type="button" class="btn btn-round btn-warning-rgba ms-2" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-user-id="{{ $user->id }}" title="{{ __('Edit User') }}">
                           <i class="feather icon-edit"></i> 
                           </button>
                           <!-- Reset Email Button -->
                           <button type="button" class="btn btn-round btn-info-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetEmailModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Email') }}">
                           <i class="feather icon-mail"></i> 
                           </button>
                           <!-- Reset Password Button -->
                           <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Password') }}">
                           <i class="feather icon-lock"></i> 
                           </button>
                           <!-- Delete Button -->
                           <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="{{ $user->id }}" title="{{ __('Delete User') }}">
                           <i class="feather icon-trash"></i> 
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
<!-- Modals -->

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">{{ __('Add User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form action="{{ route('admin.account.staff.store') }}" method="POST">
               @csrf

               <!-- First & Last Name (English) -->
               <div class="row">
                  <div class="col-6">
                     <label for="firstNameEn" class="form-label">{{ __('First Name (EN)') }}</label>
                     <input type="text" class="form-control border border-primary" id="firstNameEn" name="first_name_en" required />
                  </div>
                  <div class="col-6">
                     <label for="lastNameEn" class="form-label">{{ __('Last Name (EN)') }}</label>
                     <input type="text" class="form-control border border-primary" id="lastNameEn" name="last_name_en" required />
                  </div>
               </div>

               <!-- First & Last Name (Arabic) -->
               <div class="row mt-3">
                  <div class="col-6">
                     <label for="firstNameAr" class="form-label">{{ __('First Name (AR)') }}</label>
                     <input type="text" class="form-control border border-primary" id="firstNameAr" name="first_name_ar" required />
                  </div>
                  <div class="col-6">
                     <label for="lastNameAr" class="form-label">{{ __('Last Name (AR)') }}</label>
                     <input type="text" class="form-control border border-primary" id="lastNameAr" name="last_name_ar" required />
                  </div>
               </div>

               <!-- Email -->
               <div class="mt-3">
                  <label for="email" class="form-label">{{ __('Email') }}</label>
                  <input type="email" class="form-control border border-primary" id="email" name="email" required />
               </div>

               <!-- Password with Show/Hide Toggle -->
               <div class="mt-3 position-relative">
                  <label for="password" class="form-label">{{ __('Password') }}</label>
                  <div class="input-group">
                     <input type="password" class="form-control border border-primary" id="password" name="password" required />
                     <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="feather icon-eye"></i>
                     </button>
                  </div>
               </div>

               <!-- Role Selection -->
               <div class="mt-3">
                  <label for="role" class="form-label">{{ __('Role') }}</label>
                  <select class="form-control border border-primary" id="role" name="role" required>
                     <option value="admin">{{ __('Admin') }}</option>
                     <option value="housing_manager">{{ __('Housing Manager') }}</option>
                     <option value="building_manager">{{ __('Building Manager') }}</option>
                     <option value="technician">{{ __('Technician') }}</option>
                  </select>
               </div>

               <!-- Submit Button -->
               <button type="submit" class="btn btn-primary mt-3">{{ __('Add User') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>


<!-- Edit User Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="editRoleModalLabel">{{ __('Edit User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form action="{{ route('admin.account.staff.update') }}" method="POST">
               @csrf
               <input type="hidden" id="editUserId" name="user_id" />
              
               <div class="mb-3">
                  <label for="editRole" class="form-label">{{ __('Role') }}</label>
                  <select class="form-control border border-primary" id="editRole" name="role" required>
                     <option value="admin">{{ __('Admin') }}</option>
                     <option value="housing_manager">{{ __('Housing Manager') }}</option>
                     <option value="building_manager">{{ __('Building Manager') }}</option>
                     <option value="technician">{{ __('Technician') }}</option>
                  </select>
               </div>
               <button type="submit" class="btn btn-primary">{{ __('Update User') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteUserModalLabel">{{ __('Delete User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>{{ __('Are you sure you want to delete this user?') }}</p>
            <form action="{{ route('admin.account.staff.destroy') }}" method="POST">
               @csrf
               <input type="hidden" id="deleteUserId" name="user_id" />
               <div class="text-end">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                  <button type="submit" class="btn btn-danger">{{ __('Delete User') }}</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Reset Email Modal -->
<div class="modal fade" id="resetEmailModal" tabindex="-1" aria-labelledby="resetEmailModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="resetEmailModalLabel">{{ __('Reset Email') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form action="{{ route('admin.account.staff.editEmail') }}" method="POST">
               @csrf
               <input type="hidden" id="userIdEmail" name="user_id" />
               <div class="mb-3">
                  <label for="newEmail" class="form-label">{{ __('New Email Address') }}</label>
                  <input type="email" class="form-control border border-primary" id="newEmail" name="new_email" required />
               </div>
               <button type="submit" class="btn btn-primary">{{ __('Reset Email') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" >
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="resetPasswordModalLabel">{{ __('Reset Password') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>{{ __('Are you sure you want to reset the password? A new password will be generated and sent to the user via email.') }}</p>
            <form action="{{ route('admin.account.staff.resetPassword') }}" method="POST">
               @csrf
               <input type="hidden" id="userIdPassword" name="user_id" />
               <div class="text-end">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                  <button type="submit" class="btn btn-primary">{{ __('Confirm Reset') }}</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<!-- Datatable JS -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
<script>
   
   const isArabic = $('html').attr('dir') === 'rtl';

   // Initialize DataTable
   const table = $('#default-datatable').DataTable({
    "order": [[0, "asc"]],
    "responsive": true,
    language: isArabic ? {
                url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
            } : {},
});

   
    // Search functionality on keyup event
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });
   

    $('#editRoleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); 
    var userId = button.data('user-id'); 
    var userRole = button.closest('tr').find('td:eq(2)').text().trim(); 
    
    var modal = $(this);
    modal.find('#editUserId').val(userId);
    modal.find('#editUserRole').val(userRole); 
});


   $('#deleteUserModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var userId = button.data('user-id');
      var modal = $(this);
      modal.find('#deleteUserId').val(userId);
   });

   $('#resetEmailModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var userId = button.data('user-id');
      var modal = $(this);
      modal.find('#userIdEmail').val(userId);
   });

   $('#resetPasswordModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var userId = button.data('user-id');
      var modal = $(this);
      modal.find('#userIdPassword').val(userId);
   });


   document.getElementById('togglePassword').addEventListener('click', function () {
      var passwordField = document.getElementById('password');
      var icon = this.querySelector('i');

      if (passwordField.type === 'password') {
         passwordField.type = 'text';
         icon.classList.remove('icon-eye');
         icon.classList.add('icon-eye-off');
      } else {
         passwordField.type = 'password';
         icon.classList.remove('icon-eye-off');
         icon.classList.add('icon-eye');
      }
   });

</script>
@endsection