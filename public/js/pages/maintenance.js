$(document).ready(function () {
   const table = $.fn.DataTable.isDataTable('#default-datatable') ?
       $('#default-datatable').DataTable() :
       $('#default-datatable').DataTable();
   
   // Function to toggle button loading state
   function toggleButtonLoading(button, isLoading) {
       const hasClassBtnRound = button.hasClass('btn-round');

       if (isLoading) {
           if (!button.data('original-text')) {
               button.data('original-text', button.html());
           }

           if (hasClassBtnRound) {
               button.html('<i class="fa fa-spinner fa-spin"></i>')
                   .addClass('loading')
                   .prop('disabled', true);
           } else {
               button.html('<i class="fa fa-spinner fa-spin"></i> Downloading...')
                   .addClass('loading')
                   .prop('disabled', true);
           }
       } else {
           button.html(button.data('original-text'))
               .removeClass('loading')
               .prop('disabled', false);
           button.removeData('original-text');
       }
   }

   // Function to export the file
   function exportFile(button, url, filename) {
       toggleButtonLoading(button, true);

       const csrfToken = $('meta[name="csrf-token"]').attr('content');

       fetch(url, {
           method: 'GET',
           headers: {
               'X-Requested-With': 'XMLHttpRequest',
               'X-CSRF-Token': csrfToken
           }
       })
       .then(response => {
           if (!response.ok) {
               throw new Error('Network response was not ok.');
           }
           return response.blob();
       })
       .then(blob => {
           const downloadUrl = window.URL.createObjectURL(blob);
           const link = document.createElement('a');
           link.style.display = 'none';
           link.href = downloadUrl;
           link.download = filename;
           document.body.appendChild(link);
           link.click();
           link.remove();
           window.URL.revokeObjectURL(downloadUrl);
       })
       .catch(error => {
           console.error('Download error:', error);
           swal('Error!', 'Error downloading the file. Please try again later.', 'error');
       })
       .finally(() => {
           toggleButtonLoading(button, false);
       });
   }

   // Export button click event
   $('#exportExcel').off('click').on('click', function (e) {
       e.preventDefault();

       const downloadBtn = $('#downloadBtn');

       exportFile(downloadBtn, window.routes.exportExcel, 'Maintenance.xlsx');

       $(downloadBtn).next('.dropdown-menu').removeClass('show');
   });

   // Status filter for DataTable
   $('#statusFilter').on('change', function () {
       const selectedStatus = $(this).val();
       table.search('').draw(); // Clear previous search
       if (selectedStatus) {
           table.column(5).search('^' + selectedStatus + '$', true, false).draw(); // Apply the new status filter
       } else {
           table.column(5).search('').draw(); // Clear filter if none is selected
       }
   });

   // Function to update the status of the maintenance request
   function updateStatus(requestId, status) {
       let updateStatusUrl = window.routes.updateStatus.replace(':id', requestId);

       const formData = {
           status: status, // Status ('accepted' or 'rejected')
           _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for security
       };

       $.ajax({
           url: updateStatusUrl,
           type: 'PUT', // We are updating an existing resource, so use PUT
           data: formData,
           beforeSend: function () {
               // Disable the button while processing
               toggleButtonLoading($('#accept-status-btn-' + requestId), true);
               toggleButtonLoading($('#reject-status-btn-' + requestId), true);
           },
           success: function (response) {
               if (response.success) {
                   swal('Success!', response.message || 'Status updated successfully.', 'success');
                   location.reload(); // Reload page to reflect status changes
               }
           },
           error: function (xhr) {
               swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
           },
           complete: function () {
               // Re-enable the buttons after the process is complete
               toggleButtonLoading($('#accept-status-btn-' + requestId), false);
               toggleButtonLoading($('#reject-status-btn-' + requestId), false);
           }
       });
   }

   // Accept button click event
   $(document).on('click', '[id^="accept-status-btn-"]', function () {
       const button = $(this);
       const requestId = button.attr('id').split('-').pop();
       updateStatus(requestId, 'accepted'); // Update status to accepted
   });

   // Reject button click event
   $(document).on('click', '[id^="reject-status-btn-"]', function () {
       const button = $(this);
       const requestId = button.attr('id').split('-').pop();
       updateStatus(requestId, 'rejected'); // Update status to rejected
   });

});
