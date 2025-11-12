// public/js/bookings.js

document.addEventListener('DOMContentLoaded', function () {
    console.log('Booking Manager initialized');

    let currentBookingId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Booking
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            viewBooking(bookingId);
        });
    });

    // Delete Booking
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            confirmDelete(bookingId);
        });
    });

    // Check-in Booking
    document.querySelectorAll('.checkin-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            confirmCheckin(bookingId);
        });
    });

    // Check-out Booking
    document.querySelectorAll('.checkout-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            confirmCheckout(bookingId);
        });
    });

    // Cancel Booking
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function () {
            const bookingId = this.getAttribute('data-id');
            confirmCancel(bookingId);
        });
    });

    // Delete All Bookings
    const deleteAllBtn = document.getElementById('deleteAllBtn');
    if (deleteAllBtn) {
        deleteAllBtn.addEventListener('click', confirmDeleteAll);
    }

    // Confirm Delete
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteBooking);
    }

    // Confirm Delete All
    const confirmDeleteAllBtn = document.getElementById('confirmDeleteAll');
    if (confirmDeleteAllBtn) {
        confirmDeleteAllBtn.addEventListener('click', deleteAllBookings);
    }

    // Confirm Check-in
    const confirmCheckinBtn = document.getElementById('confirmCheckin');
    if (confirmCheckinBtn) {
        confirmCheckinBtn.addEventListener('click', checkinBooking);
    }

    // Confirm Check-out
    const confirmCheckoutBtn = document.getElementById('confirmCheckout');
    if (confirmCheckoutBtn) {
        confirmCheckoutBtn.addEventListener('click', checkoutBooking);
    }

    // Confirm Cancel
    const confirmCancelBtn = document.getElementById('confirmCancel');
    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', cancelBooking);
    }

    async function viewBooking(id) {
        try {
            console.log('Fetching booking details for ID:', id);

            const response = await fetch(`/bookings/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('View booking response:', data);

            if (data.success) {
                displayBookingDetails(data.booking);
            } else {
                showAlert('Error loading booking details: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error in viewBooking:', error);
            showAlert('Error loading booking details. Please try again.', 'error');
        }
    }

    function displayBookingDetails(booking) {
        console.log('Displaying booking details:', booking);

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Guest Information</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Guest Name:</th>
                            <td>${booking.guest_name}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>${booking.guest_email}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>${booking.guest_phone}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>${booking.guest_address || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Booking Details</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Room:</th>
                            <td>${booking.room.RoomNo} (${booking.room.Type})</td>
                        </tr>
                        <tr>
                            <th>Check In:</th>
                            <td>${new Date(booking.check_in).toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <th>Check Out:</th>
                            <td>${new Date(booking.check_out).toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <th>Total Nights:</th>
                            <td>${booking.total_nights}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Occupancy</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Adults:</th>
                            <td>${booking.adults}</td>
                        </tr>
                        <tr>
                            <th>Children:</th>
                            <td>${booking.children}</td>
                        </tr>
                        <tr>
                            <th>Total Guests:</th>
                            <td>${parseInt(booking.adults) + parseInt(booking.children)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Financial Information</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Total Amount:</th>
                            <td>$${parseFloat(booking.total_amount).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td>$${parseFloat(booking.paid_amount).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <th>Remaining:</th>
                            <td class="font-weight-bold">$${(parseFloat(booking.total_amount) - parseFloat(booking.paid_amount)).toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Status Information</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Status:</th>
                            <td><span class="badge ${getStatusBadgeClass(booking.status)}">${booking.status}</span></td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td><span class="badge ${getPaymentStatusBadgeClass(booking.payment_status)}">${booking.payment_status}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Dates</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Created:</th>
                            <td>${new Date(booking.created_at).toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>${new Date(booking.updated_at).toLocaleDateString()}</td>
                        </tr>
                    </table>
                </div>
            </div>
            ${booking.special_requests ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Special Requests:</h6>
                    <div class="alert alert-light">
                        ${booking.special_requests}
                    </div>
                </div>
            </div>
            ` : ''}
            ${booking.cancellation_reason ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Cancellation Reason:</h6>
                    <div class="alert alert-warning">
                        ${booking.cancellation_reason}
                    </div>
                </div>
            </div>
            ` : ''}
        `;

        document.getElementById('viewModalBody').innerHTML = content;
        $('#viewModal').modal('show');
    }

    function getStatusBadgeClass(status) {
        const classes = {
            'Confirmed': 'bg-success',
            'Checked In': 'bg-primary',
            'Checked Out': 'bg-info',
            'Cancelled': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    function getPaymentStatusBadgeClass(paymentStatus) {
        const classes = {
            'Paid': 'bg-success',
            'Pending': 'bg-warning',
            'Partial': 'bg-info',
            'Refunded': 'bg-secondary'
        };
        return classes[paymentStatus] || 'bg-secondary';
    }

    function confirmDelete(id) {
        currentBookingId = id;
        console.log('Setting currentBookingId for deletion:', currentBookingId);
        $('#deleteModal').modal('show');
    }

    function confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    function confirmCheckin(id) {
        currentBookingId = id;
        $('#checkinModal').modal('show');
    }

    function confirmCheckout(id) {
        currentBookingId = id;
        $('#checkoutModal').modal('show');
    }

    function confirmCancel(id) {
        currentBookingId = id;
        $('#cancelModal').modal('show');
    }

    async function deleteBooking() {
        if (!currentBookingId) {
            console.error('No booking ID set for deletion');
            showAlert('Error: No booking selected for deletion.', 'error');
            return;
        }

        console.log('Deleting booking with ID:', currentBookingId);

        try {
            const response = await fetch(`/bookings/${currentBookingId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Delete response:', data);

            if (data.success) {
                showAlert(data.message, 'success');
                $('#deleteModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showAlert('Error deleting booking. Please try again.', 'error');
        } finally {
            currentBookingId = null;
        }
    }

    async function deleteAllBookings() {
        try {
            const response = await fetch('/bookings', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            console.log('Delete all response:', data);

            if (data.success) {
                showAlert(data.message, 'success');
                $('#deleteAllModal').modal('hide');

                // Reload the page after a short delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete all error:', error);
            showAlert('Error deleting all bookings. Please try again.', 'error');
        }
    }

    async function checkinBooking() {
        if (!currentBookingId) {
            showAlert('Error: No booking selected for check-in.', 'error');
            return;
        }

        try {
            const response = await fetch(`/bookings/${currentBookingId}/checkin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#checkinModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Check-in error:', error);
            showAlert('Error during check-in. Please try again.', 'error');
        } finally {
            currentBookingId = null;
        }
    }

    async function checkoutBooking() {
        if (!currentBookingId) {
            showAlert('Error: No booking selected for check-out.', 'error');
            return;
        }

        try {
            const response = await fetch(`/bookings/${currentBookingId}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#checkoutModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Check-out error:', error);
            showAlert('Error during check-out. Please try again.', 'error');
        } finally {
            currentBookingId = null;
        }
    }

    async function cancelBooking() {
        if (!currentBookingId) {
            showAlert('Error: No booking selected for cancellation.', 'error');
            return;
        }

        const reason = document.getElementById('cancellationReason').value;
        if (!reason) {
            showAlert('Please provide a cancellation reason.', 'warning');
            return;
        }

        try {
            const response = await fetch(`/bookings/${currentBookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#cancelModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Cancel error:', error);
            showAlert('Error cancelling booking. Please try again.', 'error');
        } finally {
            currentBookingId = null;
        }
    }

    function showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert-dismissible');
        existingAlerts.forEach(alert => alert.remove());

        const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alert);

        setTimeout(() => {
            const alertElement = document.querySelector('.alert-dismissible');
            if (alertElement) {
                alertElement.remove();
            }
        }, 5000);
    }
});
