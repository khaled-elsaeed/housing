@extends('layouts.student')
@section('title', __('Maintenance Request'))
@section('links')
<style>
   .problem-checkbox {
   border: 1px solid #dee2e6;
   padding: 15px;
   border-radius: 8px;
   margin-bottom: 10px;
   transition: background-color 0.3s ease;
   }
   .problem-checkbox:hover {
   background-color: #f8f9fa;
   }
   .photo-preview-item {
   position: relative;
   margin-right: 10px;
   margin-bottom: 10px;
   }
   .photo-preview-item img {
   height: 100px;
   width: 100px;
   object-fit: cover;
   border-radius: 8px;
   }
   .photo-preview-item button {
   position: absolute;
   top: 0;
   right: 0;
   padding: 0 5px;
   }
   .custom-file-label::after {
   content: "{{ __('Browse') }}";
   }
</style>
@endsection
@section('content')
<div class="row justify-content-center">
   <div class="col-12">
      <!-- Maintenance Request Form -->
      <div class="card shadow-sm">
         <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
               <i class="fa fa-tools me-2"></i> {{ __('Maintenance Request') }}
            </h5>
         </div>
         <div class="card-body">
            <form id="maintenanceForm" action="{{ route('student.maintenance.store') }}" method="POST" enctype="multipart/form-data">
               @csrf
               <!-- Category Selection -->
               <div class="mb-4">
                  <label for="category_id" class="form-label fw-bold">{{ __('Category') }} <span class="text-danger">*</span></label>
                  <select id="category_id" name="category_id" class="form-select border border-primary" required>
                     <option value="">{{ __('Select maintenance category') }}</option>
                     @foreach($categories as $category)
                     <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                     {{ trans($category->name) }}
                     </option>
                     @endforeach
                  </select>
                  @error('category_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
               <!-- Problem Selection -->
               <div class="mb-4">
                  <label class="form-label fw-bold">{{ __('What needs maintenance?') }} <span class="text-danger">*</span></label>
                  <p class="text-muted small">{{ __('Select all that apply') }}</p>
                  <div class="row problems-list">
                     <div class="col-12 text-center py-3">
                        <div class="spinner-border text-primary d-none" role="status">
                           <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Please select a category first') }}</p>
                     </div>
                  </div>
                  @error('problems')
                  <div class="text-danger small mt-2">{{ $message }}</div>
                  @enderror
               </div>
               <!-- Description -->
               <div class="mb-4">
                  <label for="description" class="form-label fw-bold">{{ __('Additional Details') }} <span class="text-danger">*</span></label>
                  <textarea id="description" name="description" rows="4" class="form-control border border-primary @error('description') is-invalid @enderror" placeholder="{{ __('Please provide specific details about the issue...') }}" required>{{ old('description') }}</textarea>
                  <div class="form-text text-muted mt-1">
                     <i class="fa fa-info-circle me-1"></i> {{ __('Include important details like: what happened, when it started, and any troubleshooting you\'ve tried') }}
                  </div>
                  @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
               </div>
               <!-- Photo Upload -->
               <div class="mb-4">
                  <label for="photos" class="form-label fw-bold">{{ __('Upload Photos') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                  <div class="custom-file">
                     <input type="file" class="form-control border border-primary @error('photos.*') is-invalid @enderror" id="photos" name="photos[]" multiple accept="image/*">
                     <label class="form-label" for="photos">{{ __('Choose files') }}</label>
                  </div>
                  <div class="form-text text-muted mt-1">
                     <i class="fa fa-camera me-1"></i> {{ __('Up to 3 photos, max 5MB each (JPG, PNG)') }}
                  </div>
                  <div id="photoPreviewContainer" class="d-flex flex-wrap mt-2"></div>
                  @error('photos.*')
                  <div class="text-danger small mt-2">{{ $message }}</div>
                  @enderror
               </div>
               <!-- Submit Buttons -->
               <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                  <i class="fa fa-times me-1"></i> {{ __('Cancel') }}
                  </button>
                  <button type="submit" id="submitBtn" class="btn btn-primary px-4">
                  <i class="fa fa-paper-plane me-1"></i> {{ __('Submit Request') }}
                  </button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection
@section('scripts')
<!-- SweetAlert2 JS -->
<script>
   $(document).ready(function () {
       // Handle category change
       $('#category_id').on('change', function () {
           const categoryId = $(this).val();
           if (!categoryId) {
               $('.problems-list').html(`
                   <div class="col-12 text-center py-3">
                       <p class="text-muted">{{ __('Please select a category first') }}</p>
                   </div>
               `);
               return;
           }
   
           // Show loading
           $('.problems-list').html(`
               <div class="col-12 text-center py-3">
                   <div class="spinner-border text-primary" role="status">
                       <span class="sr-only">Loading...</span>
                   </div>
                   <p class="mt-2 text-muted">{{ __('Loading issues...') }}</p>
               </div>
           `);
   
           // Fetch problems for selected category
           $.ajax({
               url: "{{ route('student.maintenance.problems.by.category',':id') }}".replace(':id', categoryId),
               type: "GET",
               success: function (response) {
                   let problemsHtml = '';
   
                   if (response.data.length === 0) {
                       problemsHtml = `
                           <div class="col-12 text-center py-3">
                               <p class="text-muted">{{ __('No maintenance issues found for this category') }}</p>
                           </div>
                       `;
                   } else {
                       response.data.forEach(function (problem) {
                           problemsHtml += `
                               <div class="col-md-6 mb-3">
                                   <div class="problem-checkbox">
                                       <div class="form-check">
                                           <input type="checkbox" class="form-check-input" 
                                               id="problem_${problem.id}" 
                                               name="problems[]" 
                                               value="${problem.id}">
                                           <label class="form-check-label fw-bold" for="problem_${problem.id}">
                                               ${problem.name}
                                           </label>
                                       </div>
                                   </div>
                               </div>
                           `;
                       });
                   }
   
                   $('.problems-list').html(problemsHtml);
               },
               error: function () {
                   $('.problems-list').html(`
                       <div class="col-12 text-center py-3">
                           <div class="alert alert-danger">
                               <i class="fa fa-exclamation-triangle me-2"></i>
                               {{ __('Failed to load issues. Please try again.') }}
                           </div>
                       </div>
                   `);
               }
           });
       });
   
       // Handle file upload preview
       $('#photos').on('change', function () {
           const fileInput = this;
           const photoPreviewContainer = $('#photoPreviewContainer');
           photoPreviewContainer.empty();
   
           if (fileInput.files && fileInput.files.length > 0) {
               for (let i = 0; i < Math.min(fileInput.files.length, 3); i++) {
                   const reader = new FileReader();
                   reader.onload = function (e) {
                       const preview = `
                           <div class="photo-preview-item me-2 mb-2 position-relative">
                               <img src="${e.target.result}" alt="Preview" class="img-thumbnail">
                               <button type="button" class="btn btn-sm btn-danger position-absolute" onclick="removePreview(this)">
                                   <i class="fa fa-times"></i>
                               </button>
                           </div>
                       `;
                       photoPreviewContainer.append(preview);
                   };
                   reader.readAsDataURL(fileInput.files[i]);
               }
   
               // Update label with file count
               const fileCount = fileInput.files.length;
               $('.custom-file-label').text(fileCount > 1 ? `${fileCount} files selected` : fileInput.files[0].name);
           } else {
               $('.custom-file-label').text('{{ __("Choose files") }}');
           }
       });
   
       // Form submission with SweetAlert
       $('#maintenanceForm').on('submit', function (e) {
   e.preventDefault();
   
   const formData = new FormData(this);
   const submitButton = $('#submitBtn');
   
   // Disable the button and add a loading spinner
   submitButton.prop('disabled', true).html(`
       <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
       {{ __("Sending...") }}
   `);
   
   $.ajax({
       url: $(this).attr('action'),
       type: $(this).attr('method'),
       data: formData,
       processData: false,
       contentType: false,
       success: function (response) {
           swal({
               type: 'success',
               title: '{{ __("Success") }}',
               text: response.message,
           }).then(() => {
               window.location.href = "{{ route('student.maintenance.create') }}";
           });
       },
       error: function (xhr) {
           const errors = xhr.responseJSON.errors;
           let errorMessage = '';
   
           for (const field in errors) {
               errorMessage += errors[field].join('\n') + '\n';
           }
   
           swal({
               type: 'error',
               title: '{{ __("Error") }}',
               text: errorMessage || '{{ __("An error occurred. Please try again.") }}',
           });
   
           // Re-enable the button and restore original text
           submitButton.prop('disabled', false).html('{{ __("Submit") }}');
       }
   });
   });
   
   });
   
   // Function to remove photo preview
   function removePreview(button) {
       $(button).parent().remove();
   
       if ($('#photoPreviewContainer').children().length === 0) {
           $('#photos').val('');
           $('.custom-file-label').text('{{ __("Choose files") }}');
       }
   }
</script>
@endsection