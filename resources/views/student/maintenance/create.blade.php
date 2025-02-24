@extends('layouts.student')
@section('title', __('Maintenance Request'))
@section('links')
<style>


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
    // === Category Selection ===
    $('#category_id').on('change', function () {
        const categoryId = $(this).val();
        const $problemsList = $('.problems-list');

        if (!categoryId) {
            $problemsList.html(`
                <div class="col-12 text-center py-3">
                    <p class="text-muted">{{ __('Please select a category first') }}</p>
                </div>
            `);
            return;
        }

        // Show loading spinner
        $problemsList.html(`
            <div class="col-12 text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2 text-muted">{{ __('Loading issues...') }}</p>
            </div>
        `);

        // Fetch problems via AJAX
        $.ajax({
            url: "{{ route('student.maintenance.problems.by.category', ':id') }}".replace(':id', categoryId),
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
                    response.data.forEach(problem => {
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

                $problemsList.html(problemsHtml);
            },
            error: function () {
                $problemsList.html(`
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

    // === File Configuration ===
    const fileConfig = {
        maxSize: 4 * 1024 * 1024, // 4MB
        allowedTypes: ["image/jpeg", "image/png"]
    };

    // === File Validation ===
    function validateImageFile(file) {
        if (!file) return false;

        if (file.size > fileConfig.maxSize) {
            swal({
                type: "error",
                title: "{{ __('File Too Large') }}",
                text: "{{ __('Image size must be less than 4MB') }}"
            });
            return false;
        }

        if (!fileConfig.allowedTypes.includes(file.type)) {
            swal({
                type: "error",
                title: "{{ __('Invalid File Type') }}",
                text: "{{ __('Please upload only JPG or PNG images') }}"
            });
            return false;
        }

        return true;
    }

    // === Photo Preview ===
    const $photoPreviewContainer = $('#photoPreviewContainer');
    const $fileInput = $('#photos');
    const $fileLabel = $('.custom-file-label');

    $fileInput.on('change', function () {
        $photoPreviewContainer.empty();
        const files = Array.from(this.files);
        const validFiles = files.filter(file => validateImageFile(file)); // Filter out invalid files

        if (validFiles.length > 0) {
            const dataTransfer = new DataTransfer();
            validFiles.forEach(file => dataTransfer.items.add(file));
            this.files = dataTransfer.files;

            validFiles.slice(0, 3).forEach(file => { // Limit to 3 previews
                const reader = new FileReader();
                reader.onload = function (e) {
                    const $preview = $(`
                        <div class="photo-preview-item me-2 mb-2 position-relative">
                            <img src="${e.target.result}" alt="Preview" class="img-thumbnail">
                            <button type="button" class="btn btn-sm btn-danger position-absolute remove-preview-btn">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    `);
                    $photoPreviewContainer.append($preview);
                };
                reader.readAsDataURL(file);
            });

            const fileCount = validFiles.length;
            $fileLabel.text(fileCount > 1 ? `${fileCount} files selected` : validFiles[0].name);
        } else {
            this.files = new DataTransfer().files; // Clear files if all are invalid
            $fileLabel.text('{{ __("Choose files") }}');
        }
    });

    $photoPreviewContainer.on('click', '.remove-preview-btn', function () {
        const $previewItem = $(this).parent();
        const index = $previewItem.index();
        $previewItem.remove();

        const currentFiles = Array.from($fileInput[0].files);
        const dataTransfer = new DataTransfer();
        currentFiles.forEach((file, i) => {
            if (i !== index) dataTransfer.items.add(file);
        });
        $fileInput[0].files = dataTransfer.files;

        const remainingFiles = $fileInput[0].files.length;
        $fileLabel.text(remainingFiles > 0 
            ? (remainingFiles > 1 ? `${remainingFiles} files selected` : currentFiles[0].name) 
            : '{{ __("Choose files") }}');
    });

    // === Form Submission ===
    $('#maintenanceForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const $submitButton = $('#submitBtn');
        const originalButtonText = $submitButton.html();
        const formData = new FormData(this);

        $submitButton.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ __("Sending...") }}
        `);

        $.ajax({
            url: $form.attr('action'),
            type: $form.attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                swal({
                    type: 'success',
                    title: '{{ __("Success") }}',
                    text: response.message
                }).then(() => {
                    window.location.href = "{{ route('student.maintenance.create') }}";
                });
            },
            error: function (xhr) {
                const errors = xhr.responseJSON?.errors || '{{ __("An error occurred. Please try again.") }}';
                swal({
                    type: 'error',
                    title: '{{ __("Error") }}',
                    text: errors
                });
            },
            complete: function () {
                $submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    });
});

// === Utility Functions ===
function removePreview(button) {
    const $previewItem = $(button).parent();
    const index = $previewItem.index();
    $previewItem.remove();

    const $fileInput = $('#photos');
    const currentFiles = Array.from($fileInput[0].files);
    const dataTransfer = new DataTransfer();
    currentFiles.forEach((file, i) => {
        if (i !== index) dataTransfer.items.add(file);
    });
    $fileInput[0].files = dataTransfer.files;

    const remainingFiles = $fileInput[0].files.length;
    $('.custom-file-label').text(remainingFiles > 0 
        ? (remainingFiles > 1 ? `${remainingFiles} files selected` : currentFiles[0].name) 
        : '{{ __("Choose files") }}');
}
</script>
@endsection