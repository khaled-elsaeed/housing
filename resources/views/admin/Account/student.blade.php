@extends('layouts.admin')
@section('title', 'Student Account Management')
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
                        <h5 class="card-title font-14">Total Students</h5>
                        <h4 class="mb-0">{{ $totalStudentsCount }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-footer">
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Male</span>
                     </div>
                     <div class="col-6 text-end">
                        <span class="font-13">{{ $maleTotalCount }}</span>
                     </div>
                  </div>
                  <div class="row align-items-center">
                     <div class="col-6 text-start">
                        <span class="font-13">Female</span>
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
    <h2 class="page-title text-primary mb-2">Student Accounts</h2>
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
                        <th>No.</th>
                        <th>Student Name</th>
                        <th>National Id</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                  @foreach($students as $student)
                   
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->getUsernameEnAttribute() }}</td>
                        <td>
                           {{ $student->universityArchive->national_id }}
                        </td>
                        <td>{{ $student->email ?? 'No Email' }}</td>
                        <td>
                            @if($student->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($student->status === 'inactive')
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <!-- Edit Button -->
                            <a href="#" class="btn btn-round btn-warning-rgba" title="Edit Student">
                                <i class="feather icon-edit"></i> 
                            </a>

                            <!-- Reset Email Button -->
                            <button type="button" class="btn btn-round btn-info-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetEmailModal" data-student-id="{{ $student->id }}" title="Reset Email">
                                <i class="feather icon-mail"></i> 
                            </button>

                            <!-- Reset Password Button -->
                            <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-student-id="{{ $student->id }}" title="Reset Password">
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
        <h5 class="modal-title" id="resetEmailModalLabel">Reset Student's Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="#" method="POST">
          @csrf
          <input type="hidden" id="studentIdEmail" name="student_id" />
          <div class="mb-3">
            <label for="newEmail" class="form-label">New Email Address</label>
            <input type="email" class="form-control" id="newEmail" name="new_email" required />
          </div>
          <button type="submit" class="btn btn-primary">Reset Email</button>
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
        <h5 class="modal-title" id="resetPasswordModalLabel">Reset Student's Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="#" method="POST">
          @csrf
          <input type="hidden" id="studentIdPassword" name="student_id" />
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required />
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required />
          </div>
          <button type="submit" class="btn btn-primary">Reset Password</button>
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
<script src="{{ asset('js/custom/custom-table-datatable.js') }}"></script>

<script>
    // Update the student ID for the reset actions
    $('#resetEmailModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var studentId = button.data('student-id');
        var modal = $(this);
        modal.find('#studentIdEmail').val(studentId);
    });

    $('#resetPasswordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var studentId = button.data('student-id');
        var modal = $(this);
        modal.find('#studentIdPassword').val(studentId);
    });
</script>

@endsection
