$(document).ready(function () {
   
    // Filter functionality for DataTable based on status
$('#statusFilter').on('change', function() {
    const selectedStatus = $(this).val();

    // Clear any existing search
    table.search('').draw();

    // Apply the status filter
    if (selectedStatus) {
        // Use the selected status to filter the DataTable
        table.column(5).search('^' + selectedStatus + '$', true, false).draw(); // Exact match filter
    } else {
        // If no status is selected, show all entries
        table.column(5).search('').draw();
    }
});

    // Filter functionality for DataTable based on status
    $('#buildingFilter').on('change', function() {
        const selectedBuilding = $(this).val();
    
        // Clear any existing search
        table.search('').draw();
    
        // Apply the status filter
        if (selectedBuilding) {
            // Use the selected status to filter the DataTable
            table.column(2).search('^' + selectedBuilding + '$', true, false).draw(); // Exact match filter
        } else {
            // If no status is selected, show all entries
            table.column(2).search('').draw();
        }
    });

    // Filter functionality for DataTable based on status
    $('#apartmentFilter').on('change', function() {
        const selectedApartment = $(this).val();
    
        // Clear any existing search
        table.search('').draw();
    
        // Apply the status filter
        if (selectedApartment) {
            // Use the selected status to filter the DataTable
            table.column(1).search('^' + selectedApartment + '$', true, false).draw(); // Exact match filter
        } else {
            // If no status is selected, show all entries
            table.column(1).search('').draw();
        }
    });

    const table = $.fn.DataTable.isDataTable('#default-datatable') 
    ? $('#default-datatable').DataTable() 
    : $('#default-datatable').DataTable();


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

$('#exportExcel').off('click').on('click', function(e) {
    e.preventDefault();

    const downloadBtn = $('#downloadBtn');

    exportFile(downloadBtn, window.routes.exportExcel, 'Rooms.xlsx');

    $('.dropdown-menu.show').removeClass('show');
});

 
    $('#editRoomDetailsModal').on('submit', function (e) {
        e.preventDefault();
        const formData = {
            room_id: $('#editRoomDetailsModal').attr('data-room-id'),
            status: $('#editRoomStatus').val(),
            purpose: $('#editRoomPurpose').val(), // Correct field for purpose
            type: $('#editRoomType').val(), // Added .val()
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        

        $.ajax({
            url: window.routes.updateRoomStatus,
            type: 'POST',
            data: formData,
            success: function (response) {
                if (response.success) {
                    swal('Success!', response.message || 'Status updated successfully.', 'success');
                    $('#editRoomDetailsModal').modal('hide');
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
                room_id: $('#editRoomNoteModal').attr('data-room-id'),
                note: $('#editRoomNote').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
    
            $.ajax({
                url: window.routes.updateRoomNote,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        swal('Success!', response.message || 'Note updated successfully.', 'success');
                        $('#editRoomNoteModal').modal('hide');
                        location.reload();
                    }
                },
                error: function (xhr) {
                    swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
                }
            });
        });
    
        function deleteRoom(roomId) {
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
                    url: window.routes.deleteRoom.replace(':id', roomId),
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.success) {
                            swal('Deleted!', response.message || 'Room deleted successfully.', 'success').
                            then(() => {
                                location.reload();
                            });
                         }
                    },
                    error: function () {
                        swal('Error!', 'Failed to delete the room. Please try again.', 'error');
                    }
                });
            })
            
        }
    
        $(document).on('click', '[id^="delete-btn-"]', function () {
            const roomId = $(this).attr('id').split('-').pop();
            deleteRoom(roomId);
        });
    
        $(document).on('click', '[id^="edit-note-btn-"]', function () {
            const button = $(this);
            const row = button.closest('tr');
            const roomId = button.attr('id').split('-').pop();
            const note = row.find('td:nth-last-child(2)').text().trim();
            console.log(note);
    
            $('#editRoomNote').val(note === 'No description available' ? '' : note);
            $('#editRoomNoteModal').attr('data-room-id', roomId).modal('show');
        });
    
        $(document).on('click', '[id^="edit-status-btn-"]', function () {
            const button = $(this);
            const row = button.closest('tr');
            const roomId = button.attr('id').split('-').pop();
            const status = row.find('td:nth-last-child(5)').text().trim() || '';
            const purpose = row.find('td:nth-last-child(7)').text().trim() || '';
            const type = row.find('td:nth-last-child(6)').text().trim() || '';
            
            $('#editRoomStatus').val(status.toLowerCase().replace(' ','_'));
            $('#editRoomPurpose').val(purpose.toLowerCase());
            $('#editRoomType').val(type.toLowerCase());
            
            $('#editRoomDetailsModal').attr('data-room-id', roomId).modal('show');
        });


});



