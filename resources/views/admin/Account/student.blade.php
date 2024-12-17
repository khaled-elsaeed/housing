@extends('layouts.admin')
@section('title', __('pages.admin.account.accounts_management'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}" rel="stylesheet" type="text/css" />
<style>
    .loading {
        pointer-events: none; /* Disable button interactions */
    }
    .search-box {
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-start;
    }
    .search-box input {
        width: 300px;
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
         <!-- Start col for Total Maintenance Requests -->
         <div class="col-lg-3 col-md-6 mb-2">
            <div class="card m-b-30">
               <div class="card-body">
                  <div class="row align-items-center">
                     <div class="col-5">
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-wrench"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('pages.admin.account.total_students') }}</h5>
                        <h4 class="mb-0">{{ $totalStudentsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.account.male') }}</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">{{ __('pages.admin.account.female') }}</span>
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

<!-- Student Account Table -->
<div class="d-flex flex-column mb-3">
    <h2 class="page-title text-primary mb-2">{{ __('pages.admin.account.student_accounts') }}</h2>
</div>

<!-- Start row -->
<div class="row">
   <!-- Start col -->
   <div class="col-lg-12">
      <div class="card m-b-30 table-card">
         <div class="card-body table-container">
            <!-- Search Box -->
            <!-- Search Bar -->
<div class="mb-3">
    <label for="searchBox" class="form-label">Search</label>
    <input type="text" id="searchBox" class="form-control" >
</div>

            <div class="table-responsive">
               <table id="default-datatable" class="display table table-bordered">
                  <thead>
                     <tr>
                        <th>{{ __('pages.admin.account.no') }}</th>
                        <th>{{ __('pages.admin.account.student_name') }}</th>
                        <th>{{ __('pages.admin.account.national_id') }}</th>
                        <th>{{ __('pages.admin.account.email') }}</th>
                        <th>{{ __('pages.admin.account.status') }}</th>
                        <th>{{ __('pages.admin.account.actions') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                  @foreach($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->getUsernameEnAttribute() }}</td>
                        <td>{{ $student->universityArchive->national_id }}</td>
                        <td>{{ $student->email ?? __('pages.admin.account.no_email') }}</td>
                        <td>
                            @if($student->status === 'active')
                                <span class="badge bg-success">{{ __('pages.admin.account.active') }}</span>
                            @elseif($student->status === 'inactive')
                                <span class="badge bg-danger">{{ __('pages.admin.account.inactive') }}</span>
                            @endif
                        </td>
                        <td>
                            <!-- Reset Email Button -->
                            <button type="button" class="btn btn-round btn-info-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetEmailModal" data-student-id="{{ $student->id }}" title="{{ __('pages.admin.account.reset_email') }}">
                                <i class="feather icon-mail"></i> 
                            </button>

                            <!-- Reset Password Button -->
                            <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-student-id="{{ $student->id }}" title="{{ __('pages.admin.account.reset_password') }}">
                                <i class="feather icon-lock"></i> 
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

<!-- Modal for Reset Email -->
<div class="modal fade" id="resetEmailModal" tabindex="-1" aria-labelledby="resetEmailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetEmailModalLabel">{{ __('pages.admin.account.reset_email_modal_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.account.student.editEmail') }}" method="POST">
          @csrf
          <input type="hidden" id="studentIdEmail" name="student_id" />
          <div class="mb-3">
            <label for="newEmail" class="form-label">{{ __('pages.admin.account.new_email_address') }}</label>
            <input type="email" class="form-control" id="newEmail" name="new_email" required />
          </div>
          <button type="submit" class="btn btn-primary">{{ __('pages.admin.account.reset_email') }}</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetPasswordModalLabel">{{ __('pages.admin.account.reset_password_modal_title') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.account.student.resetPassword') }}" method="POST">
          @csrf
          <input type="hidden" id="studentIdPassword" name="student_id" />
          <div class="mb-3">
            <label for="newPassword" class="form-label">{{ __('pages.admin.account.new_password') }}</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required />
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">{{ __('pages.admin.account.confirm_password') }}</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required />
          </div>
          <button type="submit" class="btn btn-primary">{{ __('pages.admin.account.reset_password') }}</button>
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
   // Initialize DataTable
   const table = $('#default-datatable').DataTable({
        "order": [[0, "asc"]],
        responsive: true,
    });

    // Search functionality on keyup event
    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Modal to reset email
    $('#resetEmailModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var studentId = button.data('student-id');
        var modal = $(this);
        modal.find('#studentIdEmail').val(studentId);
    });

    // Modal to reset password
    $('#resetPasswordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var studentId = button.data('student-id');
        var modal = $(this);
        modal.find('#studentIdPassword').val(studentId);
    });
</script>
@endsection
