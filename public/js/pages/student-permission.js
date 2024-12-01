document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('permissionRequestForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        // Client-side validation
        const lateDate = document.getElementById('late_date').value;
        const lateTime = document.getElementById('late_time').value;
        const reason = document.getElementById('reason').value;

        if (!lateDate || !lateTime || !reason) {
            swal({
                title: 'Error!',
                text: 'Please fill in all required fields.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            });
            return;
        }

        // Check for CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            swal({
                title: 'Error!',
                text: 'CSRF token is missing. Please refresh the page.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            });
            return;
        }

        // Check route is defined
        if (!Window.routes || !Window.routes.permissionStore) {
            console.error('Submission route is not defined');
            swal({
                title: 'Error!',
                text: 'Submission route is not configured.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            });
            return;
        }

        // Send AJAX request
        fetch(Window.routes.permissionStore, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json', // Explicitly request JSON response
            },
            body: formData,
        })
        .then(response => {
            // Check if response is OK and is JSON
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                swal({
                    title: 'Success!',
                    text: data.message || 'Permission request submitted successfully.',
                    type: 'success',
                    confirmButtonClass: 'btn btn-success',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                // Handle server-side validation errors
                swal({
                    title: 'Error!',
                    text: data.message || 'An error occurred while submitting your request.',
                    type: 'error',
                    confirmButtonClass: 'btn btn-danger',
                });
            }
        })
        .catch((error) => {
            console.error('Submission Error:', error);
            swal({
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again later.',
                type: 'error',
                confirmButtonClass: 'btn btn-danger',
            });
        });
    });
});