@extends('layouts.admin')
@section('title', __('Accounts Management'))
@section('links')
<!-- DataTables CSS -->
<link href="{{ asset('plugins/datatables/dataTables.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/buttons.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('plugins/datatables/responsive.bootstrap4.min.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/custom-datatable.css') }}?v={{ config('app.version') }}" rel="stylesheet" type="text/css" />

<style>
    .loading {
        pointer-events: none; 
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
                        <span class="action-icon badge badge-primary-inverse me-0"><i class="feather icon-user"></i></span>
                     </div>
                     <div class="col-7 text-end mt-2 mb-2">
                        <h5 class="card-title font-14">{{ __('Total Users') }}</h5>
                        <h4 class="mb-0">{{ $totalUsersCount }}</h4>
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
           <input type="search" class="form-control search-input" id="searchBox" placeholder="{{ __('Search...') }}" />
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
                        <th>{{ __('No') }}</th>
                        <th>{{ __('User Name') }}</th>
                        <th>{{ __('National ID') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                     </tr>
                  </thead>
                  <tbody>
                  @foreach($users as $user)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $user->getUsername() ?? 'N/A' }}</td>
    <td>{{ $user->student?->national_id ?? 'N/A' }}</td>
    <td>{{ $user->email ?? 'N/A' }}</td>
    <td>
        @if($user->status === 'active') 
            <span class="badge bg-success"> {{ __('Active') }} </span>
        @elseif($user->status === 'inactive') 
            <span class="badge bg-danger">{{ __('Inactive') }} </span>
        @else
            <span class="badge bg-secondary">{{  __('Unknown')  }}</span>
        @endif
    </td>
    <td>
        <!-- Reset Email Button -->
        <button type="button" class="btn btn-round btn-info-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetEmailModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Email') }}">
            <i class="feather icon-mail"></i> 
        </button>

        <!-- Reset Password Button -->
        <button type="button" class="btn btn-round btn-danger-rgba ms-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-user-id="{{ $user->id }}" title="{{ __('Reset Password') }}">
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
        <h5 class="modal-title" id="resetEmailModalLabel">{{ __('Reset Email') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.account.user.editEmail') }}" method="POST">
          @csrf
          <input type="hidden" id="userIdEmail" name="user_id" />
          <div class="mb-3">
            <label for="newEmail" class="form-label">{{ __('New Email Address') }}</label>
            <input type="email" class="form-control" id="newEmail" name="new_email" required />
          </div>
          <button type="submit" class="btn btn-primary">{{ __('Reset Email') }}</button>
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
        <h5 class="modal-title" id="resetPasswordModalLabel">{{ __('Reset Password') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.account.user.resetPassword') }}" method="POST">
          @csrf
          <input type="hidden" id="userIdPassword" name="user_id" />
          <div class="mb-3">
            <label for="newPassword" class="form-label">{{ __('New Password') }}</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" required />
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">{{ __('Confirm Password') }}</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required />
          </div>
          <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
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
        var userId = button.data('user-id');
        var modal = $(this);
        modal.find('#userIdEmail').val(userId);
    });

    // Modal to reset password
    $('#resetPasswordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var modal = $(this);
        modal.find('#userIdPassword').val(userId);
    });
</script>
@endsection
