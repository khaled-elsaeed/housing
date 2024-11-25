$(document).ready(function() {

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


    function showMoreDetails(residentId) {
        const url = window.routes.getResidentMoreDetails.replace(':id', residentId);

        toggleButtonLoading($('#details-btn'), true);
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {

                populateResidentDetails(response.data);
                $('#details-modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Details fetch error:', error);
                alert('Failed to fetch resident details. Please try again.');
            },
            complete: function() {
                toggleButtonLoading($('#details-btn'), false);
            }
        });
    }


    function populateResidentDetails(data) {
        $('#faculty').val(data.faculty);
        $('#program').val(data.program);
        $('#score').val(data.score);
        $('#percent').val(data.percent);
        $('#governorate').val(data.governorate);
        $('#city').val(data.city);
        $('#street').val(data.street);

        $('#residentDetailsModal').modal('show');

    }


    $('#exportExcel').off('click').on('click', function(e) {
        e.preventDefault();

        const downloadBtn = $('#downloadBtn');

        exportFile(downloadBtn, window.routes.exportExcel, 'residents.xlsx');

        $(downloadBtn).next('.dropdown-menu').removeClass('show');
    });

    $('#exportPDF').on('click', function(e) {
        e.preventDefault();
        exportFile($('#downloadBtn'), window.routes.exportPdf, 'residents.pdf');
    });


    $(document).on('click', '#details-btn', function() {
        const residentId = $(this).data('resident-id');
        showMoreDetails(residentId);

    });

});