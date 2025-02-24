@extends('layouts.admin')
@section('title', __('Accounts Management'))

@section('links')
    <link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <!-- Start row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
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
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                <h2 class="page-title text-primary mb-2 mb-md-0">{{ __('User Accounts') }}</h2>
                <div>
                    <button class="btn btn-outline-primary btn-sm toggle-btn" id="toggleButton" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa fa-search-plus"></i>
                    </button>
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
        <div class="col-lg-12">
            <div class="card m-b-30 table-card">
                <div class="card-body table-container">
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
                                        {{ __($roles) ?: 'N/A' }}
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @elseif($user->status === 'inactive')
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-round btn-warning-rgba ms-2" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-user-id="{{ $user->id }}" title="{{ __('Edit User') }}">
                                            <i class="feather icon-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-info-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetEmailModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Email') }}">
                                            <i class="feather icon-mail"></i>
                                        </button>
                                        <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Password') }}">
                                            <i class="feather icon-lock"></i>
                                        </button>
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
    </div>

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
                        <div class="mt-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control border border-primary" id="email" name="email" required />
                        </div>
                        <div class="mt-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <div class="input-group">
                                <input type="password" class="form-control border border-primary" id="password" name="password" required />
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="feather icon-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="role" class="form-label">{{ __('Select Role') }}</label>
                            <select name="role" id="role" class="form-control">
                                <option value="">{{ __('Select Role') }}</option>
                                <option value="admin">{{ __('Admin') }}</option>
                                <option value="housing_manager">{{ __('Housing Manager') }}</option>
                                <option value="technician">{{ __('Technician') }}</option>
                            </select>
                        </div>
                        <div class="mt-3" id="technicianRoleDiv" style="display:none">
                            <label for="technician_role" class="form-label">{{ __('Technician Role') }}</label>
                            <select name="technician_role" class="form-control">
                                <option value="">{{ __('Select Technician Role') }}</option>
                                <option value="plumber">{{ __('Plumber') }}</option>
                                <option value="electrician">{{ __('Electrician') }}</option>
                                <option value="carpenter">{{ __('Carpenter') }}</option>
                            </select>
                        </div>
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
            order: [[0, "asc"]],
            responsive: true,
            language: isArabic ? { url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json" } : {}
        });

        // Search functionality on keyup event
        $('#searchBox').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Show/Hide Password
        $('#togglePassword').on('click', function () {
            const $passwordField = $('#password');
            const $icon = $(this).find('i');
            if ($passwordField.attr('type') === 'password') {
                $passwordField.attr('type', 'text');
                $icon.removeClass('icon-eye').addClass('icon-eye-off');
            } else {
                $passwordField.attr('type', 'password');
                $icon.removeClass('icon-eye-off').addClass('icon-eye');
            }
        });

        // Role Change Handler for Add User
        $('#role').on('change', function () {
            const isTechnician = $(this).val() === 'technician';
            $('#technicianRoleDiv').toggle(isTechnician);
            if (!isTechnician) {
                $('#technician_role').val('');
            }
        });

        // Edit Role Change Handler
        $('#editRole').on('change', function () {
            const isTechnician = $(this).val() === 'technician';
            $('#editTechnicianRoleDiv').toggle(isTechnician);
            if (!isTechnician) {
                $('#editTechnicianRole').val('');
            }
        });

        // Edit Role Modal Handler
        $('#editRoleModal').on('show.bs.modal', function (event) {
            console.log("staff:862 Edit Role Modal Opened");
            const $button = $(event.relatedTarget);
            const userId = $button.data('user-id');
            console.log("staff:868 User ID:", userId);

            const $modal = $(this);
            $modal.find('#editUserId').val(userId);

            // Role mapping for primary roles (translated display to slugs)
            const roleMapping = {
                'Admin': 'admin',
                'مدير': 'admin',
                'Housing Manager': 'housing_manager',
                'مشرف/مشرفة السكن': 'housing_manager',
                'Technician': 'technician',
                'فني': 'technician'
            };

            // Technician sub-role mapping (translated display to slugs)
            const technicianRoleMapping = {
                'Plumber': 'plumber',
                'سباك': 'plumber',
                'Electrician': 'electrician',
                'كهربائي': 'electrician',
                'Carpenter': 'carpenter',
                'نجار': 'carpenter'
            };

            // Fetch user role from table display text
            let userRole = $button.closest('tr').find('td:eq(2)').text().trim().split(' - ').map(role => role.trim());
            console.log("staff:869 Extracted User Role:", userRole);

            // Map primary role
            const primaryRole = roleMapping[userRole[0]] || 'housing_manager'; // Fallback to 'housing_manager' if unmapped
            console.log("staff:884 Mapped Primary Role:", primaryRole);

            // Set primary role and trigger change
            $modal.find('#editRole').val(primaryRole).trigger('change');

            // Handle technician sub-role if present
            if (userRole.length > 1 && primaryRole === 'technician') {
                const technicianRole = technicianRoleMapping[userRole[1]] || userRole[1].toLowerCase();
                console.log("staff:890 Technician Role:", technicianRole);
                $modal.find('#editTechnicianRole').val(technicianRole);
            } else {
                $modal.find('#editTechnicianRole').val('');
            }

            // Focus workaround for aria-hidden warning
            $modal.on('shown.bs.modal', function () {
                $modal.find('#editRole').focus();
            });
        });

        // Delete User Modal Handler
        $('#deleteUserModal').on('show.bs.modal', function (event) {
            const $button = $(event.relatedTarget);
            const userId = $button.data('user-id');
            $(this).find('#deleteUserId').val(userId);
        });

        // Reset Email Modal Handler
        $('#resetEmailModal').on('show.bs.modal', function (event) {
            const $button = $(event.relatedTarget);
            const userId = $button.data('user-id');
            $(this).find('#userIdEmail').val(userId);
        });

        // Reset Password Modal Handler
        $('#resetPasswordModal').on('show.bs.modal', function (event) {
            const $button = $(event.relatedTarget);
            const userId = $button.data('user-id');
            $(this).find('#userIdPassword').val(userId);
        });

        // AJAX Form Submission Handler
        function handleFormSubmission($form, url, successMessage, errorTitle) {
            $form.on('submit', function (e) {
                e.preventDefault();
                const $btn = $form.find('button[type="submit"]');
                const originalText = $btn.html();
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ __("Processing...") }}');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: $form.serialize(),
                    success: function (response) {
                        if (response.success) {
                            $form.closest('.modal').modal('hide');
                            swal({
                                type: 'success',
                                title: '{{ __("Success") }}',
                                text: successMessage,
                            }).then(() => location.reload());
                        } else {
                            swal({
                                type: 'error',
                                title: errorTitle,
                                text: response.message || '{{ __("An error occurred.") }}'
                            });
                        }
                    },
                    error: function (xhr) {
                        const errorMessage = xhr.responseJSON?.message || '{{ __("An unexpected error occurred.") }}';
                        swal({
                            type: 'error',
                            title: errorTitle,
                            text: errorMessage
                        });
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        }

        // Form Submissions
        handleFormSubmission(
            $('#addUserForm'),
            "{{ route('admin.account.staff.store') }}",
            '{{ __("User added successfully") }}',
            '{{ __("Error") }}'
        );

        handleFormSubmission(
            $('#editUserForm'),
            "{{ route('admin.account.staff.update') }}",
            '{{ __("User updated successfully") }}',
            '{{ __("Error") }}'
        );

        handleFormSubmission(
            $('#deleteUserForm'),
            "{{ route('admin.account.staff.destroy') }}",
            '{{ __("User deleted successfully") }}',
            '{{ __("Error") }}'
        );

        handleFormSubmission(
            $('#resetEmailForm'),
            "{{ route('admin.account.staff.editEmail') }}",
            '{{ __("Email reset successfully") }}',
            '{{ __("Error") }}'
        );

        handleFormSubmission(
            $('#resetPasswordForm'),
            "{{ route('admin.account.staff.resetPassword') }}",
            '{{ __("Password reset successfully") }}',
            '{{ __("Error") }}'
        );
    </script>
@endsection