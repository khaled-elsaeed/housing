<div class="modal fade" id="updateImagesModal" tabindex="-1" aria-labelledby="updateImagesModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateImagesModalLabel">{{ __('Update Payment Images') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <form id="updateImagesForm">
                    <!-- Existing Attachments -->
                    <div id="existingAttachments" class="mb-3" style="display: none;">
                        <label class="form-label">{{ __('Existing Attachments') }}</label>
                        <div id="existingAttachmentsContainer" class="d-flex flex-wrap gap-2"></div>
                    </div>

                    <!-- New Image Upload -->
                    <div class="mb-3">
                        <label for="updateInvoiceReceipt" class="form-label">{{ __('Add New Images') }}</label>
                        <input type="file" class="form-control" id="updateInvoiceReceipt" name="photos[]" multiple />
                    </div>
                    <div class="form-text text-muted mt-1">
                        <i class="fa fa-camera me-1"></i> {{ __('Up to 3 photos, max 3MB each (JPG, PNG)') }}
                    </div>

                    <!-- Preview Container for New Images -->
                    <div class="d-flex flex-wrap mt-2 gap-2 photoPreviewContainer"></div>

                    <!-- Hidden Input for Deleted Media -->
                    <input type="hidden" name="deleted_media" id="deletedMedia" />

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-3">{{ __('Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>