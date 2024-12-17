$(document).ready(function () {
    const isArabic = $('html').attr('dir') === 'rtl';

    // Initialize DataTable
    const table = $('#default-datatable').DataTable({
        responsive: true,
        language: isArabic ? {
            url: "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Arabic.json",
        } : {},

    });

    // Reference to the toggle button and icon
    const toggleButton = document.getElementById("toggleButton");
    const icon = toggleButton.querySelector("i");

    // Toggle icon on collapse show
    document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
        icon.classList.remove("fa-search-plus");
        icon.classList.add("fa-search-minus");
    });

    // Toggle icon on collapse hide
    document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
        icon.classList.remove("fa-search-minus");
        icon.classList.add("fa-search-plus");
    });

    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw(); 
    });

$('#statusFilter').on('change', function() {
    const selectedStatus = $(this).val();

    table.search('').draw();

    if (selectedStatus) {
        table.column(5).search('^' + selectedStatus + '$', true, false).draw(); 
    } else {
        table.column(5).search('').draw();
    }
});

    // Filter functionality for DataTable based on status
    $('#buildingFilter').on('change', function() {
        const selectedBuilding = $(this).val();
            table.search('').draw();
    
        if (selectedBuilding) {
            table.column(2).search('^' + selectedBuilding + '$', true, false).draw(); 
        } else {
            table.column(2).search('').draw();
        }
    });

    // Filter functionality for DataTable based on status
    $('#apartmentFilter').on('change', function() {
        const selectedApartment = $(this).val();
    
        table.search('').draw();
    
        // Apply the status filter
        if (selectedApartment) {
            table.column(1).search('^' + selectedApartment + '$', true, false).draw(); 
        } else {
            table.column(1).search('').draw();
        }
    });



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

 
   // Handle Edit Room Details Form Submission
$('#editRoomDetailsModal').on('submit', function (e) {
    e.preventDefault();

    // Get the form data
    const formData = {
        room_id: $('#editRoomDetailsModal').attr('data-room-id'),
        status: $('#editRoomStatus').val(), // Get selected status
        purpose: $('#editRoomPurpose').val(), // Get selected purpose
        type: $('#editRoomType').val(), // Get selected room type
        _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token
    };

    // Send the AJAX request
    $.ajax({
        url: window.routes.updateRoomStatus, // URL for updating room status
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response.success) {
                swal('Success!', response.message || 'Status updated successfully.', 'success');
                $('#editRoomDetailsModal').modal('hide'); // Close the modal
                location.reload(); // Reload the page to reflect changes
            }
        },
        error: function (xhr) {
            swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
        }
    });
});

// Handle Edit/Add Room Note Form Submission
$('#editNoteForm').on('submit', function (e) {
    e.preventDefault();

    // Get the form data
    const formData = {
        room_id: $('#editRoomNoteModal').attr('data-room-id'), // Get the room ID from modal data attribute
        note: $('#editRoomNote').val(), // Get the note value from the textarea
        _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token
    };

    // Send the AJAX request
    $.ajax({
        url: window.routes.updateRoomNote, // URL for updating room note
        type: 'POST',
        data: formData,
        success: function (response) {
            if (response.success) {
                swal('Success!', response.message || 'Note updated successfully.', 'success');
                $('#editRoomNoteModal').modal('hide'); // Close the modal
                location.reload(); // Reload the page to reflect changes
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
          
            $('#editRoomNoteModal').attr('data-room-id', roomId).modal('show');
        });
    
        $(document).on('click', '[id^="edit-status-btn-"]', function () {
            const button = $(this);
            const row = button.closest('tr');
            const roomId = button.attr('id').split('-').pop();
            
    
            
            $('#editRoomDetailsModal').attr('data-room-id', roomId).modal('show');
        });


});



