$(document).ready(function () {

   const toggleButton = document.getElementById("toggleButton");
   const icon = toggleButton.querySelector("i");

   document.getElementById("collapseExample").addEventListener("shown.bs.collapse", function() {
       icon.classList.remove("fa-search-plus");
       icon.classList.add("fa-search-minus");
   });

   document.getElementById("collapseExample").addEventListener("hidden.bs.collapse", function() {
       icon.classList.remove("fa-search-minus");
       icon.classList.add("fa-search-plus");
   });

   const table = $('#default-datatable').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      ajax: {
          url: window.routes.fetchBuildings,
          data: function (d) {
              d.customSearch = $('#searchBox').val();
              // Use 'gender' and 'status' for the backend
              d.gender = $('#genderFilter').val();   // For gender
              d.status = $('#statusFilter').val();   // For status
          }
      },
      columns: [
          { data: "number" },
          { data: "gender" },
          { data: "max_apartments" },
          { data: "status" },
          { data: "description" },
          {
              data: null,
              render: function (data, type, row) {
                  return `
                      <button type="button" class="btn btn-round btn-warning-rgba" id="edit-note-btn-${row.id}" title="Edit Note">
                          <i class="feather icon-edit"></i>
                      </button>
                      <button type="button" class="btn btn-round btn-primary-rgba" id="edit-status-btn-${row.id}" title="Edit Status">
                          <i class="feather icon-settings"></i>
                      </button>
                      <button type="button" class="btn btn-round btn-danger-rgba" id="delete-btn-${row.id}" title="Delete Building">
                          <i class="feather icon-trash-2"></i>
                      </button>
                  `;
              }
          }
      ]
  });
  
  
  $('#genderFilter').on('change', function () {
   table.draw(); // Redraw table when filter changes
});

$('#statusFilter').on('change', function () {
   table.draw(); // Redraw table when filter changes
});

  
      // Optional: Trigger search when the user types in the search box
      $('#searchBox').on('keyup', function () {
          table.draw();
      });
  
  

   function fetchSummaryData() {
       $.ajax({
           url: window.routes.fetchStats,
           method: 'GET',
           dataType: 'json',
           success: function(data) {
               // Update the statistics on the page
               document.getElementById('totalBuildings').innerText = data.totalBuildings || 0;
               document.getElementById('activeBuildingsCount').innerText = data.activeBuildingsCount || 0;
               document.getElementById('inactiveBuildingsCount').innerText = data.inactiveBuildingsCount || 0;

               document.getElementById('maleBuildingCount').innerText = data.maleBuildingCount || 0;
               document.getElementById('maleActiveCount').innerText = data.maleActiveCount || 0;
               document.getElementById('maleInactiveCount').innerText = data.maleInactiveCount || 0;
               document.getElementById('maleUnderMaintenanceCount').innerText = data.maleUnderMaintenanceCount || 0;

               document.getElementById('femaleBuildingCount').innerText = data.femaleBuildingCount || 0;
               document.getElementById('femaleActiveCount').innerText = data.femaleActiveCount || 0;
               document.getElementById('femaleInactiveCount').innerText = data.femaleInactiveCount || 0;
               document.getElementById('femaleUnderMaintenanceCount').innerText = data.femaleUnderMaintenanceCount || 0;

               document.getElementById('maintenanceCount').innerText = data.maintenanceCount || 0;
           }
       });
   }

   fetchSummaryData();
 
    $('#saveBuildingBtn').on('click', function (e) {
       e.preventDefault();
       const buildingNumber = $('#newBuildingNumber').val();
       const gender = $('#newBuildingGender').val();
       const maxApartments = $('#newBuildingMaxApartments').val();
       const maxRooms = $('#newBuildingMaxRooms').val();
 
       if (!buildingNumber || !gender || !maxApartments || !maxRooms) {
          swal('Validation Error!', 'All fields are required.', 'error');
          return;
       }
 
       const formData = {
          building_number: buildingNumber,
          gender: gender,
          max_apartments: maxApartments,
          max_rooms_per_apartment: maxRooms,
          _token: $('meta[name="csrf-token"]').attr('content')
       };
 
       $.ajax({
          url: window.routes.saveBuilding,
          type: 'POST',
          data: formData,
          success: function (response) {
             if (response.success) {
                swal('Success!', response.message || 'Building saved successfully.', 'success');
                location.reload();
             }
          },
          error: function (xhr) {
             const errorMessage = (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.';
             swal('Error!', errorMessage, 'error');
          }
       });
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
 
 
    $('#exportExcel').off('click').on('click', function (e) {
       e.preventDefault();
 
       const downloadBtn = $('#downloadBtn');
 
       exportFile(downloadBtn, window.routes.exportExcel, 'Buildings.xlsx');
 
       $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });
 
 
    $('#statusFilter').on('change', function () {
       const selectedStatus = $(this).val();
       console.log('Selected Status:', selectedStatus);
 
       table.search('').draw();
       if (selectedStatus) {
          table.column(3).search('^' + selectedStatus + '$', true, false).draw();
       } else {
          table.column(3).search('').draw();
       }
    });
 
 
    $('#editStatusForm').on('submit', function (e) {
       e.preventDefault();
       const formData = {
          building_id: $('#editBuildingStatusModal').attr('data-building-id'),
          status: $('#editBuildingStatus').val(),
          _token: $('meta[name="csrf-token"]').attr('content')
       };
 
       $.ajax({
          url: window.routes.updateBuildingStatus,
          type: 'POST',
          data: formData,
          success: function (response) {
             if (response.success) {
                swal('Success!', response.message || 'Status updated successfully.', 'success');
                $('#editBuildingStatusModal').modal('hide');
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
          building_id: $('#editBuildingNoteModal').attr('data-building-id'),
          note: $('#editBuildingNote').val(),
          _token: $('meta[name="csrf-token"]').attr('content')
       };
 
       $.ajax({
          url: window.routes.updateBuildingNote,
          type: 'POST',
          data: formData,
          success: function (response) {
             if (response.success) {
                swal('Success!', response.message || 'Note updated successfully.', 'success');
                $('#editBuildingNoteModal').modal('hide');
                location.reload();
             }
          },
          error: function (xhr) {
             swal('Error!', (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred.', 'error');
          }
       });
    });
 
    function deleteBuilding(buildingId) {
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
             url: window.routes.deleteBuilding.replace(':id', buildingId),
             type: 'DELETE',
             data: {
                _token: $('meta[name="csrf-token"]').attr('content')
             },
             success: function (response) {
                if (response.success) {
                   swal('Deleted!', response.message || 'Building deleted successfully.', 'success').
                   then(() => {
                      location.reload();
                   });
                }
             },
             error: function () {
                swal('Error!', 'Failed to delete the building. Please try again.', 'error');
             }
          });
       })
 
    }
 
    $(document).on('click', '[id^="delete-btn-"]', function () {
       const buildingId = $(this).attr('id').split('-').pop();
       deleteBuilding(buildingId);
    });
 
    $(document).on('click', '[id^="edit-note-btn-"]', function () {
       const button = $(this);
       const row = button.closest('tr');
       const buildingId = button.attr('id').split('-').pop();
       const note = row.find('td:nth-last-child(2)').text().trim();
       console.log(note);
 
       $('#editBuildingNote').val(note === 'No description available' ? '' : note);
       $('#editBuildingNoteModal').attr('data-building-id', buildingId).modal('show');
    });
 
    $(document).on('click', '[id^="edit-status-btn-"]', function () {
       const button = $(this);
       const row = button.closest('tr');
       const buildingId = button.attr('id').split('-').pop();
       const status = row.find('td:nth-last-child(3)').text().trim();
 
       $('#editBuildingStatus').val(status.toLowerCase().replace(' ', '_'));
       $('#editBuildingStatusModal').attr('data-building-id', buildingId).modal('show');
    });
 
 });