@extends('layouts.admin')
@section('title', __('System Settings - NMU Housing System'))
@section('content')
<!-- Start row -->
<div class="row">
   <!-- Start col for settings content -->
   <div class="col-lg-12">
      <div class="card shadow-sm border-light rounded">
         <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">@lang('System Settings')</h5>
         </div>
         <div class="card-body">
            <!-- Horizontal Tabs -->
            <ul class="nav nav-pills mb-4" id="settings-tabs" role="tablist">
               <!-- Reservation Settings Tab -->
               <li class="nav-item" role="presentation">
                  <a class="nav-link active" id="reservation-settings-tab" data-bs-toggle="pill" href="#reservation-settings" role="tab" aria-controls="reservation-settings" aria-selected="true">
                  <i class="feather icon-calendar me-2"></i> @lang('Reservation Settings')
                  </a>
               </li>
               <!-- Academic Terms Settings Tab -->
               <li class="nav-item" role="presentation">
                  <a class="nav-link" id="academic-terms-tab" data-bs-toggle="pill" href="#academic-terms" role="tab" aria-controls="academic-terms" aria-selected="false">
                  <i class="feather icon-book me-2"></i> @lang('Academic Terms')
                  </a>
               </li>
            </ul>
            <div class="tab-content" id="settings-tab-content">
               <!-- Reservation Settings Tab Content -->
               <div class="tab-pane fade show active" id="reservation-settings" role="tabpanel" aria-labelledby="reservation-settings-tab">
                  <div class="card shadow-sm border-secondary rounded">
                     <div class="card-header bg-secondary">
                        <h5 class="card-title mb-0 text-white">@lang('Reservation Settings')</h5>
                     </div>
                     <div class="card-body">
                        <!-- Form to update reservation settings -->
                        <form action="#" method="POST">
                           @csrf
                           <div class="row g-3">
                              <div class="col-md-6">
                                 <label for="reservation_start_datetime" class="form-label">@lang('Reservation Start Date & Time')</label>
                                 <input type="datetime-local" class="form-control border-primary" id="reservation_start_datetime" name="reservation_start_datetime" 
                                    value="{{ old('reservation_start_datetime', $settings['reservation_start_datetime'] ?? '') }}" required>
                              </div>
                              <div class="col-md-6">
                                 <label for="reservation_end_datetime" class="form-label">@lang('Reservation End Date & Time')</label>
                                 <input type="datetime-local" class="form-control border-primary" id="reservation_end_datetime" name="reservation_end_datetime" 
                                    value="{{ old('reservation_end_datetime', $settings['reservation_end_datetime'] ?? '') }}" required>
                              </div>
                              <div class="col-md-12">
                                 <label for="reservation_status" class="form-label">@lang('Reservation Status')</label>
                                 <select class="form-select border-primary" id="reservation_status" name="reservation_status" required onchange="toggleEligibleField()">
                                 <option value="open" {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'open' ? 'selected' : '' }}>@lang('Open')</option>
                                 <option value="closed" {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'closed' ? 'selected' : '' }}>@lang('Closed')</option>
                                 </select>
                              </div>
                              <div class="col-md-12" id="eligible_students_field" style="display: {{ old('reservation_status', $settings['reservation_status'] ?? '') == 'open' ? 'block' : 'none' }};">
                                 <label for="eligible_students" class="form-label">@lang('Eligible Students')</label>
                                 <select class="form-select border-primary" id="eligible_students" name="eligible_students" required>
                                 <option value="new" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'new' ? 'selected' : '' }}>@lang('New Students')</option>
                                 <option value="old" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'old' ? 'selected' : '' }}>@lang('Old Students')</option>
                                 <option value="all" {{ old('eligible_students', $settings['eligible_students'] ?? '') == 'all' ? 'selected' : '' }}>@lang('All Students')</option>
                                 </select>
                              </div>
                              <div class="col-md-12">
                                 <button type="submit" class="btn btn-primary mt-3">@lang('Save Reservation Settings')</button>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
               <!-- End Reservation Settings Tab -->
               <!-- Academic Terms Tab Content -->
               <div class="tab-pane fade" id="academic-terms" role="tabpanel" aria-labelledby="academic-terms-tab">
                  <div class="card shadow-sm border-secondary rounded">
                     <div class="card-header bg-secondary">
                        <h5 class="card-title mb-0 text-white">@lang('Academic Terms Management')</h5>
                     </div>
                     <div class="card-body">
                        <!-- Add New Term Button -->
                        <div class="mb-4">
                           <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTermModal">
                           <i class="feather icon-plus me-2"></i> {{ __('Add New Term') }}
                           </button>
                        </div>
                        <div class="row g-3">
                           @foreach($academicTerms as $term)
                           <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                              <div class="card border-0 shadow-sm rounded-3 h-100">
                                 <div class="card-header {{ $term->status === 'active' ? 'bg-primary' : ($term->status === 'completed' ? 'bg-secondary' : 'bg-light') }} text-white d-flex justify-content-between align-items-center">
                                    <div>
                                       <h6 class="mb-0 text-white">{{ trans($term->name) }}</h6>
                                       <small class="opacity-75">{{ trans($term->academic_year) }}</small>
                                    </div>
                                    <span class="badge bg-white text-dark rounded-pill">{{ ucfirst($term->status) }}</span>
                                 </div>
                                 <div class="card-body">
                                    <!-- Start Section with Icon -->
                                    <div class="d-flex justify-content-between mb-2">
                                       <medium class="text-primary">
                                          <i class="feather icon-calendar text-secondary me-2"></i>{{ __('Start') }}
                                       </medium>
                                       <medium class="text-secondary">{{ $term->start_date }}</medium>
                                    </div>
                                    <!-- End Section with Icon -->
                                    <div class="d-flex justify-content-between">
                                       <medium class="text-primary">
                                          <i class="feather icon-calendar text-secondary me-2"></i>{{ __('End') }}
                                       </medium>
                                       <medium class="text-secondary">{{ $term->end_date }}</medium>
                                    </div>
                                 </div>
                                 <div class="card-footer p-2">
                                    @if($term->status == 'completed')
                                    <button class="btn btn-sm btn-secondary rounded-pill w-100" disabled>
                                    <i class="feather icon-check me-1"></i>{{ __('Completed') }}
                                    </button>
                                    @else
                                    <div class="btn-group w-100" role="group">
                                       <button class="btn btn-sm btn-primary rounded-start-pill start-term-btn" data-id="{{ $term->id }}" {{ $term->status === 'active' ? 'disabled' : '' }}>
                                       {{ __('Begin') }}
                                       <i class="feather icon-play"></i>
                                       </button>
                                       <button class="btn btn-sm btn-secondary end-term-btn" data-id="{{ $term->id }}" {{ $term->status === 'inactive' ? 'disabled' : '' }}>
                                       {{__('End')}}    
                                       <i class="feather icon-stop-circle"></i>
                                       </button>
                                       <div class="btn-group" role="group">
                                          <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                          <i class="feather icon-more-horizontal"></i>
                                          </button>
                                          <ul class="dropdown-menu">
                                             <li>
                                                <button class="dropdown-item edit-term-btn" data-id="{{ $term->id }}" 
                                                   data-name="{{ $term->name }}" 
                                                   data-start="{{ $term->start_date }}" 
                                                   data-end="{{ $term->end_date }}">
                                                <i class="feather icon-edit me-2"></i>{{ __('Edit') }}
                                                </button>
                                             </li>
                                             <li>
                                                <button class="dropdown-item text-danger delete-term-btn" data-id="{{ $term->id }}">
                                                <i class="feather icon-trash me-2"></i>{{ __('Delete') }}
                                                </button>
                                             </li>
                                          </ul>
                                       </div>
                                    </div>
                                    @endif
                                 </div>
                              </div>
                           </div>
                           @endforeach
                           @if($academicTerms->isEmpty())
                           <div class="col-12">
                              <div class="alert alert-soft-primary text-center rounded-3 p-3">
                                 <i class="feather icon-info fs-4 mb-2 text-primary"></i>
                                 <p class="mb-0">{{ __('No academic terms found.') }}</p>
                              </div>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <!-- End Academic Terms Tab -->
            </div>
         </div>
      </div>
   </div>
   <!-- End col for settings content -->
</div>
<!-- End row -->
<!-- Modal for Adding/Editing Academic Term -->
<div class="modal fade" id="addTermModal" tabindex="-1" aria-labelledby="addTermModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addTermModalLabel">{{ __('Add New Term') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="termForm">
               @csrf
               <input type="hidden" id="termId" name="term_id">
               <div class="mb-3">
                  <label for="termName" class="form-label">{{ __('Term Name') }}</label>
                  <input type="text" class="form-control" id="termName" name="term_name" required>
               </div>
               <div class="mb-3">
                  <label for="startDate" class="form-label">{{ __('Start Date') }}</label>
                  <input type="date" class="form-control" id="startDate" name="start_date" required>
               </div>
               <div class="mb-3">
                  <label for="endDate" class="form-label">{{ __('End Date') }}</label>
                  <input type="date" class="form-control" id="endDate" name="end_date" required>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            <button type="button" class="btn btn-primary" id="saveTermBtn">{{ __('Save Term') }}</button>
         </div>
      </div>
   </div>
</div>
<!-- Include SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- JavaScript for Academic Terms Management -->
<script>
   document.addEventListener('DOMContentLoaded', function () {
       const addTermModal = new bootstrap.Modal(document.getElementById('addTermModal'));
   
       // Open Modal for Adding New Term
       document.querySelector('[data-bs-target="#addTermModal"]').addEventListener('click', function () {
           document.getElementById('addTermModalLabel').innerText = '{{ __("Add New Term") }}';
           document.getElementById('termForm').reset();
           document.getElementById('termId').value = '';
       });
   
       // Open Modal for Editing Term
       document.querySelectorAll('.edit-term-btn').forEach(button => {
           button.addEventListener('click', function () {
               document.getElementById('addTermModalLabel').innerText = '{{ __("Edit Term") }}';
               document.getElementById('termId').value = this.dataset.id;
               document.getElementById('termName').value = this.dataset.name;
               document.getElementById('startDate').value = this.dataset.start;
               document.getElementById('endDate').value = this.dataset.end;
               addTermModal.show();
           });
       });
   
       // Save Term (Add/Edit)
       document.getElementById('saveTermBtn').addEventListener('click', function () {
           const formData = new FormData(document.getElementById('termForm'));
           fetch("{{ route('admin.academic.create') }}", {
               method: 'POST',
               body: formData,
               headers: {
                   'X-CSRF-TOKEN': '{{ csrf_token() }}'
               }
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   swal({
                       type: 'success',
                       title: '{{ __("Success") }}',
                       text: data.message,
                   }).then(() => {
                       window.location.reload();
                   });
               } else {
                   swal({
                       type: 'error',
                       title: '{{ __("Error") }}',
                       text: data.message,
                   });
               }
           })
           .catch(error => {
               swal({
                   type: 'error',
                   title: '{{ __("Error") }}',
                   text: '{{ __("An unexpected error occurred") }}',
               });
           });
       });
   
       // Start Term
       document.querySelectorAll('.start-term-btn').forEach(button => {
           button.addEventListener('click', function () {
               const termId = this.dataset.id;
               fetch(`{{ url('admin/academic-terms') }}/${termId}/start`, {
                   method: 'POST',
                   headers: {
                       'X-CSRF-TOKEN': '{{ csrf_token() }}',
                       'Content-Type': 'application/json'
                   }
               })
               .then(response => response.json())
               .then(data => {
                   swal({
                       type: data.success ? 'success' : 'error',
                       title: data.success ? '{{ __("Success") }}' : '{{ __("Error") }}',
                       text: data.message || '{{ __("An error occurred") }}',
                   }).then(() => {
                       if (data.success) {
                           window.location.reload();
                       }
                   });
               });
           });
       });
   
       // End Term
       document.querySelectorAll('.end-term-btn').forEach(button => {
           button.addEventListener('click', function () {
               const termId = this.dataset.id;
               swal({
                   title: '{{ __("Are you sure?") }}',
                   text: '{{ __("This will end the academic term and mark all related reservations as completed.") }}',
                   type: 'warning',
                   showCancelButton: true,
                   confirmButtonText: '{{ __("Yes, end it") }}',
                   cancelButtonText: '{{ __("Cancel") }}'
               }).then((result) => {
                       fetch(`{{ url('admin/academic-terms') }}/${termId}/end`, {
                           method: 'POST',
                           headers: {
                               'X-CSRF-TOKEN': '{{ csrf_token() }}',
                               'Content-Type': 'application/json'
                           }
                       })
                       .then(response => response.json())
                       .then(data => {
                           swal({
                               type: data.success ? 'success' : 'error',
                               title: data.success ? '{{ __("Success") }}' : '{{ __("Error") }}',
                               text: data.message || '{{ __("An error occurred") }}',
                           }).then(() => {
                               if (data.success) {
                                   window.location.reload();
                               }
                           });
                       });
                   
               });
           });
       });
   
       // Delete Term
       document.querySelectorAll('.delete-term-btn').forEach(button => {
           button.addEventListener('click', function () {
               const termId = this.dataset.id;
               swal({
                   title: '{{ __("Are you sure?") }}',
                   text: '{{ __("You won\'t be able to revert this!") }}',
                   type: 'warning',
                   showCancelButton: true,
                   confirmButtonColor: '#d33',
                   cancelButtonColor: '#3085d6',
                   confirmButtonText: '{{ __("Delete") }}'
               }).then((result) => {
                       fetch(`/admin/settings/academic-terms/${termId}/delete`, {
                           method: 'DELETE',
                           headers: {
                               'X-CSRF-TOKEN': '{{ csrf_token() }}'
                           }
                       })
                       .then(response => response.json())
                       .then(data => {
                           swal({
                               type: data.success ? 'success' : 'error',
                               title: data.success ? '{{ __("Deleted!") }}' : '{{ __("Error") }}',
                               text: data.message,
                           }).then(() => {
                               if (data.success) {
                                   window.location.reload();
                               }
                           });
                       });
                   
               });
           });
       });
   });
</script>
@endsection