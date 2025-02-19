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
                        <td>
                        @php
    $roles = $user->getRoleNames()->implode(' - ');
@endphp
{{ $roles ?: 'N/A' }}

</td>
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
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">{{ __('Add User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="addUserForm">
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
               <div class="mt-3">
                  <label for="password" class="form-label">{{ __('Password') }}</label>
                  <div class="input-group">
                     <input type="password" class="form-control border border-primary" id="password" name="password" required />
                     <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                        <i class="feather icon-eye"></i>
                     </button>
                  </div>
               </div>
               <!-- Role Category Selection -->
               <div class="mt-3">
                  <label for="role" class="form-label">{{ __('Select Role') }}</label>
                  <select name="role" id="role" class="form-control">
                     <option value="">{{ __('Select Role') }}</option>
                     <option value="admin">{{ __('Admin') }}</option>
                     <option value="housing_manager">{{ __('Housing Manager') }}</option>
                     <option value="technician">{{ __('Technician') }}</option>
                  </select>
               </div>
               <!-- Technician Role Selection (Hidden by Default) -->
               <div class="mt-3" id="technicianRoleDiv" style="display:none">
                  <label for="technician_role" class="form-label">{{ __('Technician Role') }}</label>
                  <select name="technician_role" class="form-control">
                     <option value="">{{ __('Select Technician Role') }}</option>
                     <option value="plumber">{{ __('Plumber') }}</option>
                     <option value="electrician">{{ __('Electrician') }}</option>
                     <option value="carpenter">{{ __('Carpenter') }}</option>
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
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="editRoleModalLabel">{{ __('Edit User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="editUserForm">
               @csrf
               <input type="hidden" id="editUserId" name="user_id" />
               <div class="mb-3">
                  <label for="editRole" class="form-label">{{ __('Role') }}</label>
                  <select name="role" id="editRole" class="form-control">
                     <option value="">{{ __('Select Role') }}</option>
                     <option value="admin">{{ __('Admin') }}</option>
                     <option value="housing_manager">{{ __('Housing Manager') }}</option>
                     <option value="technician">{{ __('Technician') }}</option>
                  </select>
               </div>
               <div class="mt-3" id="editTechnicianRoleDiv" style="display:none">
                  <label for="editTechnicianRole" class="form-label">{{ __('Technician Role') }}</label>
                  <select name="technician_role" id="editTechnicianRole" class="form-control">
                     <option value="">{{ __('Select Technician Role') }}</option>
                     <option value="plumber">{{ __('Plumber') }}</option>
                     <option value="electrician">{{ __('Electrician') }}</option>
                     <option value="carpenter">{{ __('Carpenter') }}</option>
                  </select>
               </div>
               <button type="submit" class="btn btn-primary">{{ __('Update User') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteUserModalLabel">{{ __('Delete User') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>{{ __('Are you sure you want to delete this user?') }}</p>
            <form id="deleteUserForm">
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
<div class="modal fade" id="resetEmailModal" tabindex="-1" aria-labelledby="resetEmailModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="resetEmailModalLabel">{{ __('Reset Email') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="resetEmailForm">
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
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="resetPasswordModalLabel">{{ __('Reset Password') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>{{ __('Are you sure you want to reset the password? A new password will be generated and sent to the user via email.') }}</p>
            <form id="resetPasswordForm">
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

<!-- SweetAlert2 JS -->
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

   // Show/Hide Password
   document.getElementById('togglePassword').addEventListener('click', function () {
      const passwordField = document.getElementById('password');
      const icon = this.querySelector('i');
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

   // Role Change Handler
   $('#role').on('change', function () {
      if ($(this).val() === 'technician') {
         $('#technicianRoleDiv').show();
      } else {
         $('#technicianRoleDiv').hide();
      }
   });

  // Edit Role Change Handler (Corrected ID)
$('#editRole').on('change', function () {
    if ($(this).val() === 'technician') {
        $('#editTechnicianRoleDiv').show(); // Show the div, not the select
    } else {
        $('#editTechnicianRoleDiv').hide(); // Hide the div
    }
});

// Edit Role Modal Handler (Updated to trigger change after setting role)
$('#editRoleModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const userId = button.data('user-id');
    let userRole = button.closest('tr').find('td:eq(2)').text().trim().split('-');
    userRole = userRole.map(role => role.trim());

    const modal = $(this);
    modal.find('#editUserId').val(userId);
    modal.find('#editRole').val(userRole[0]);
    modal.find('#editRole').trigger('change'); // Trigger change to update visibility

    if (userRole.length > 1) {
        modal.find('#editTechnicianRole').val(userRole[1]);
    }
});



   // Delete User Modal Handler
   $('#deleteUserModal').on('show.bs.modal', function (event) {
      const button = $(event.relatedTarget);
      const userId = button.data('user-id');
      const modal = $(this);
      modal.find('#deleteUserId').val(userId);
   });

   // Reset Email Modal Handler
   $('#resetEmailModal').on('show.bs.modal', function (event) {
      const button = $(event.relatedTarget);
      const userId = button.data('user-id');
      const modal = $(this);
      modal.find('#userIdEmail').val(userId);
   });

   // Reset Password Modal Handler
   $('#resetPasswordModal').on('show.bs.modal', function (event) {
      const button = $(event.relatedTarget);
      const userId = button.data('user-id');
      const modal = $(this);
      modal.find('#userIdPassword').val(userId);
   });

   // Add User Form Submission
   $('#addUserForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
         url: "{{ route('admin.account.staff.store') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function (response) {
            if (response.success) {
               $('#addUserModal').modal('hide');
               swal({
                  type: 'success',
                  title: 'Success',
                  text: 'User added successfully',
               }).then(() => location.reload());
            } else {
               swal({
                  type: 'error',
                  title: 'Error',
                  text: response.message,
               });
            }
         },
         error: function (xhr) {
            swal({
               type: 'error',
               title: 'Error',
               text: xhr.responseText,
            });
         }
      });
   });

   // Edit User Form Submission
   $('#editUserForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
         url: "{{ route('admin.account.staff.update') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function (response) {
            if (response.success) {
               $('#editRoleModal').modal('hide');
               swal({
                  type: 'success',
                  title: 'Success',
                  text: 'User updated successfully',
               }).then(() => location.reload());
            } else {
               swal({
                  type: 'error',
                  title: 'Error',
                  text: response.message,
               });
            }
         },
         error: function (xhr) {
            swal({
               type: 'error',
               title: 'Error',
               text: xhr.responseText,
            });
         }
      });
   });

   // Delete User Form Submission
   $('#deleteUserForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
         url: "{{ route('admin.account.staff.destroy') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function (response) {
            if (response.success) {
               $('#deleteUserModal').modal('hide');
               swal({
                  type: 'success',
                  title: 'Success',
                  text: 'User deleted successfully',
               }).then(() => location.reload());
            } else {
               swal({
                  type: 'error',
                  title: 'Error',
                  text: response.message,
               });
            }
         },
         error: function (xhr) {
            swal({
               type: 'error',
               title: 'Error',
               text: xhr.responseText,
            });
         }
      });
   });

   // Reset Email Form Submission
   $('#resetEmailForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
         url: "{{ route('admin.account.staff.editEmail') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function (response) {
            if (response.success) {
               $('#resetEmailModal').modal('hide');
               swal({
                  type: 'success',
                  title: 'Success',
                  text: 'Email reset successfully',
               }).then(() => location.reload());
            } else {
               swal({
                  type: 'error',
                  title: 'Error',
                  text: response.message,
               });
            }
         },
         error: function (xhr) {
            swal({
               type: 'error',
               title: 'Error',
               text: xhr.responseText,
            });
         }
      });
   });

   // Reset Password Form Submission
   $('#resetPasswordForm').on('submit', function (e) {
      e.preventDefault();
      $.ajax({
         url: "{{ route('admin.account.staff.resetPassword') }}",
         type: "POST",
         data: $(this).serialize(),
         success: function (response) {
            if (response.success) {
               $('#resetPasswordModal').modal('hide');
               swal({
                  type: 'success',
                  title: 'Success',
                  text: 'Password reset successfully',
               }).then(() => location.reload());
            } else {
               swal({
                  type: 'error',
                  title: 'Error',
                  text: response.message,
               });
            }
         },
         error: function (xhr) {
            swal({
               type: 'error',
               title: 'Error',
               text: xhr.responseText,
            });
         }
      });
   });
</script>
@endsection