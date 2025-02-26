<div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileUploadModalLabel">{{ __('Upload Payment Files') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <form id="fileUploadForm">
                    <!-- Payment Method Selection -->
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">{{ __('Select Payment Method') }}</label>
                        <select class="form-select" id="uploadPaymentMethod" name="payment_method" required>
                            <option value="" disabled selected>{{ __('Select Payment Method') }}</option>
                            <option value="instapay">{{ __('Instapay') }}</option>
                            <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                        </select>
                    </div>

                    <!-- New File Upload -->
                    <div class="mb-3">
                        <label for="uploadInvoiceReceipt" class="form-label">{{ __('Add New Files') }}</label>
                        <input type="file" class="form-control" id="uploadInvoiceReceipt" name="photos[]" multiple />
                    </div>
                    <div class="form-text text-muted mt-1">
                        <i class="fa fa-camera me-1"></i> {{ __('Up to 3 photos, max 3MB each (JPG, PNG)') }}
                    </div>

                    <!-- Preview Container for New Images -->
                    <div class="d-flex flex-wrap mt-2 gap-2 photoPreviewContainer"></div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary mt-3">{{ __('Save Changes') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>