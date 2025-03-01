<div class="modal fade" id="invoiceDetailsModal" tabindex="-1" role="dialog" aria-labelledby="invoiceDetailsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceDetailsModalLabel">{{ __('Reservation and Payment Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <h6><i class="fa fa-calendar-check"></i> {{ __('Reservation Details') }}</h6>
                <div class="mb-3"><strong>{{ __('Location:') }}</strong> <span id="location">{{ __('Empty') }}</span></div>
                <h6><i class="fa fa-credit-card"></i> {{ __('Payment Details') }}</h6>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center"><i class="fa fa-tag"></i> {{ __('Category') }}</th>
                            <th scope="col" class="text-center"><i class="fa fa-dollar-sign"></i> {{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody id="paymentDetails">
                        <!-- Payment rows will be dynamically added here -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-between">
                    <strong>{{ __('Total:') }}</strong>
                    <span id="totalAmount" class="text-success">{{ __('Empty') }}</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>