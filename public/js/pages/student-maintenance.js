document.addEventListener('DOMContentLoaded', function () {
    // Handle card selection and toggle corresponding issues
    document.querySelectorAll('.issue-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var issueType = this.id.replace('_card', ''); // Extract type (e.g., water_sanitary)
            toggleIssueDetails(issueType);               // Toggle visibility
            toggleCardHighlight(this);                   // Toggle card highlight
        });
    });

    // Function to toggle issue details
    function toggleIssueDetails(issueType) {
        console.log(issueType);
        // Select the corresponding issue section by id
        var issueSection = document.getElementById(issueType + '_issues');

        // Ensure the section exists before toggling
        if (issueSection) {
            issueSection.classList.toggle('d-none');
        } else {
            console.error(`No issue section found for: ${issueType}`);
        }
    }

    // Function to toggle card highlight
    function toggleCardHighlight(card) {
        card.classList.toggle('active-card');                    // Toggle active style
        card.querySelector('.card-overlay').classList.toggle('show'); // Show or hide overlay
    }

    // Form submission handling
    document.getElementById('maintenanceRequestForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission


         // Check CSRF token
         const csrfToken = document.querySelector('meta[name="csrf-token"]');
         if (!csrfToken) {
             return swal({
                 title: 'Configuration Error',
                 text: 'CSRF token is missing. Please refresh the page.',
                 type: 'error',
                 confirmButtonClass: 'btn btn-danger'
             });
         }


        // Validate that at least one checkbox is checked in each section
        const waterIssues = document.querySelectorAll('input[name="water_issues[]"]:checked');
        const electricalIssues = document.querySelectorAll('input[name="electrical_issues[]"]:checked');
        const housingIssues = document.querySelectorAll('input[name="housing_issues[]"]:checked');

        // Check if at least one checkbox is checked in any of the sections
        if (waterIssues.length === 0 && electricalIssues.length === 0 && housingIssues.length === 0) {
            swal({
                title: 'Error!',
                text: 'Please select at least one issue from any of the sections.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            });
            return; // Stop submission if no checkboxes are selected
        }

        const formData = new FormData(this); // Collect form data

        // Send AJAX request
        fetch(window.routes.maintenanceStore, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                swal({
                    title: 'Success!',
                    text: 'Maintenance request submitted successfully.',
                    type: 'success',
                    confirmButtonClass: 'btn btn-success',
                }).then(() => {
                    document.getElementById('maintenanceRequestForm').reset(); // Reset the form
                    window.location.reload(); // Reload the page
                });
            } else {
                swal({
                    title: 'Error!',
                    text: 'Failed to submit the request. Please check your input.',
                    type: 'error',
                    confirmButtonClass: 'btn btn-danger',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            swal({
                title: 'Error!',
                text: 'An error occurred while submitting your request.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            }).then(() => {
                window.location.reload(); // Reload the page on error
            });
        });
    });

    // Image upload handling
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = document.getElementById('previewImage');
    const removeImageBtn = document.getElementById('removeImageBtn');

    // Image Upload Logic
    uploadZone.addEventListener('click', () => fileInput.click());
    uploadZone.addEventListener('dragover', preventDefaults);
    uploadZone.addEventListener('dragleave', preventDefaults);
    uploadZone.addEventListener('drop', handleDrop);
    fileInput.addEventListener('change', handleFileSelect);
    removeImageBtn.addEventListener('click', clearImage);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
        uploadZone.classList.toggle('dragging', e.type === 'dragover');
    }

    function handleDrop(e) {
        preventDefaults(e);
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (validateFile(file)) {
                displayPreview(file);
            }
        }
    }

    function validateFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
            return false;
        }

        if (file.size > maxSize) {
            alert('File is too large. Maximum size is 2MB.');
            return false;
        }

        return true;
    }

    function displayPreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            imagePreview.classList.remove('d-none');
            uploadZone.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }

    function clearImage() {
        fileInput.value = '';
        previewImage.src = '';
        imagePreview.classList.add('d-none');
        uploadZone.classList.remove('d-none');
    }
});
