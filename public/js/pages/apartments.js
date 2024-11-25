$(document).ready(function () {

    const table = $.fn.DataTable.isDataTable('#default-datatable') ?
       $('#default-datatable').DataTable() :
       $('#default-datatable').DataTable();
 
 
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
 
    $('#exportExcel').off('click').on('click', function (e) {
       e.preventDefault();
 
       const downloadBtn = $('#downloadBtn');
 
       exportFile(downloadBtn, window.routes.exportExcel, 'Apartments.xlsx');
 
       $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });
 
 
    // Filter functionality for DataTable based on status
    $('#statusFilter').on('change', function () {
       const selectedStatus = $(this).val();
 
       // Clear any existing search
       table.search('').draw();
 
       // Apply the status filter
       if (selectedStatus) {
          // Use the selected status to filter the DataTable
          table.column(4).search('^' + selectedStatus + '$', true, false).draw(); // Exact match filter
       } else {
          // If no status is selected, show all entries
          table.column(4).search('').draw();
       }
    });
 
    // Filter functionality for DataTable based on status
    $('#buildingFilter').on('change', function () {
       const selectedBuilding = $(this).val();
 
       // Clear any existing search
       table.search('').draw();
 
       // Apply the status filter
       if (selectedBuilding) {
          // Use the selected status to filter the DataTable
          table.column(1).search('^' + selectedBuilding + '$', true, false).draw(); // Exact match filter
       } else {
          // If no status is selected, show all entries
          table.column(1).search('').draw();
       }
    });
 
 
    $('#editStatusForm').on('submit', function (e) {
       e.preventDefault();
       const formData = {
          apartment_id: $('#editApartmentStatusModal').attr('data-apartment-id'),
          status: $('#editApartmentStatus').val(),
          _token: $('meta[name="csrf-token"]').attr('content')
       };
 
       $.ajax({
          url: window.routes.updateApartmentStatus,
          type: 'POST',
          data: formData,
          success: function (response) {
             if (response.success) {
                swal('Success!', response.message || 'Status updated successfully.', 'success');
                $('#editApartmentStatusModal').modal('hide');
                location.reload();
             }
          },
          error: function (xhr) {
             swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
          }
       });
    });
 
    $('#editNoteForm').on('submit', function (e) {
       e.preventDefault();
       const formData = {
          apartment_id: $('#editApartmentNoteModal').attr('data-apartment-id'),
          note: $('#editApartmentNote').val(),
          _token: $('meta[name="csrf-token"]').attr('content')
       };
 
       $.ajax({
          url: window.routes.updateApartmentNote,
          type: 'POST',
          data: formData,
          success: function (response) {
             if (response.success) {
                swal('Success!', response.message || 'Note updated successfully.', 'success');
                $('#editApartmentNoteModal').modal('hide');
                location.reload();
             }
          },
          error: function (xhr) {
             swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
          }
       });
    });
 
    function deleteApartment(apartmentId) {
       swal({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          type: 'warning',
          showCancelButton: true,
          confirmButtonClass: 'btn btn-success',
          cancelButtonClass: 'btn btn-danger m-l-10',
          confirmButtonText: 'Yes, delete it!'
       }).then(function () {
          $.ajax({
             url: window.routes.deleteApartment.replace(':id', apartmentId),
             type: 'DELETE',
             data: {
                _token: $('meta[name="csrf-token"]').attr('content')
             },
             success: function (response) {
                if (response.success) {
                   swal('Deleted!', response.message || 'Apartment deleted successfully.', 'success').
                   then(() => {
                      location.reload();
                   });
                }
             },
             error: function () {
                swal('Error!', 'Failed to delete the apartment. Please try again.', 'error');
             }
          });
       })
 
    }
 
    $(document).on('click', '[id^="delete-btn-"]', function () {
       const apartmentId = $(this).attr('id').split('-').pop();
       deleteApartment(apartmentId);
    });
 
    $(document).on('click', '[id^="edit-note-btn-"]', function () {
       const button = $(this);
       const row = button.closest('tr');
       const apartmentId = button.attr('id').split('-').pop();
       const note = row.find('td:nth-last-child(2)').text().trim();
       console.log(note);
 
       $('#editApartmentNote').val(note === 'No description available' ? '' : note);
       $('#editApartmentNoteModal').attr('data-apartment-id', apartmentId).modal('show');
    });
 
    $(document).on('click', '[id^="edit-status-btn-"]', function () {
       const button = $(this);
       const row = button.closest('tr');
       const apartmentId = button.attr('id').split('-').pop();
       const status = row.find('td:nth-last-child(3)').text().trim();
 
       $('#editApartmentStatus').val(status.toLowerCase().replace(' ', '_'));
       $('#editApartmentStatusModal').attr('data-apartment-id', apartmentId).modal('show');
    });
 
 });