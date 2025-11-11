// public/js/bookings.js

class BookingManager {
    constructor() {
        this.currentBookingId = null;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // View booking
        $(document).on('click', '.view-btn', (e) => {
            this.viewBooking($(e.currentTarget).data('id'));
        });

        // Delete booking
        $(document).on('click', '.delete-btn', (e) => {
            this.confirmDelete($(e.currentTarget).data('id'));
        });

        // Delete all bookings
        $('#deleteAllBtn').on('click', () => {
            this.confirmDeleteAll();
        });

        // Check in booking
        $(document).on('click', '.checkin-btn', (e) => {
            this.confirmCheckIn($(e.currentTarget).data('id'));
        });

        // Check out booking
        $(document).on('click', '.checkout-btn', (e) => {
            this.confirmCheckOut($(e.currentTarget).data('id'));
        });

        // Cancel booking
        $(document).on('click', '.cancel-btn', (e) => {
            this.confirmCancel($(e.currentTarget).data('id'));
        });

        // Add payment
        $(document).on('click', '.payment-btn', (e) => {
            this.showPaymentModal($(e.currentTarget).data('id'));
        });

        // Confirm actions
        $('#confirmDelete').on('click', () => {
            this.deleteBooking();
        });

        $('#confirmDeleteAll').on('click', () => {
            this.deleteAllBookings();
        });

        $('#confirmCheckin').on('click', () => {
            this.checkInBooking();
        });

        $('#confirmCheckout').on('click', () => {
            this.checkOutBooking();
        });

        $('#confirmCancel').on('click', () => {
            this.cancelBooking();
        });

        $('#confirmPayment').on('click', () => {
            this.addPayment();
        });
    }

    async viewBooking(id) {
        try {
            const response = await $.ajax({
                url: `/bookings/${id}`,
                type: 'GET'
            });

            if (response.success) {
                this.displayBookingDetails(response.booking);
            } else {
                this.showAlert('Error loading booking details.', 'error');
            }
        } catch (error) {
            this.showAlert('Error loading booking details.', 'error');
        }
    }

    displayBookingDetails(booking) {
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Guest Name:</th>
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
                        <tr>
                            <th>Guests:</th>
                            <td>${booking.adults} Adult${booking.adults > 1 ? 's' : ''}${booking.children > 0 ? ', ' + booking.children + ' Child' + (booking.children > 1 ? 'ren' : '') : ''}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Room:</th>
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
                        <tr>
                            <th>Total Amount:</th>
                            <td>$${parseFloat(booking.total_amount).toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge ${this.getStatusBadgeClass(booking.status)}">${booking.status}</span></td>
                        </tr>
                        <tr>
                            <th>Payment Status:</th>
                            <td><span class="badge ${this.getPaymentStatusBadgeClass(booking.payment_status)}">${booking.payment_status}</span></td>
                        </tr>
                        <tr>
                            <th>Paid Amount:</th>
                            <td>$${parseFloat(booking.paid_amount).toFixed(2)}</td>
                        </tr>
                        <tr>
                            <th>Balance:</th>
                            <td>$${(parseFloat(booking.total_amount) - parseFloat(booking.paid_amount)).toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Created:</th>
                            <td>${new Date(booking.created_at).toLocaleDateString()}</td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td>${new Date(booking.updated_at).toLocaleDateString()}</td>
                        </tr>
                        ${booking.cancellation_reason ? `
                        <tr>
                            <th>Cancellation Reason:</th>
                            <td>${booking.cancellation_reason}</td>
                        </tr>
                        ` : ''}
                    </table>
                </div>
            </div>
            ${booking.special_requests ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Special Requests:</h6>
                    <p>${booking.special_requests}</p>
                </div>
            </div>
            ` : ''}
        `;

        $('#viewModalBody').html(content);
        $('#viewModal').modal('show');
    }

    getStatusBadgeClass(status) {
        const classes = {
            'Confirmed': 'bg-success',
            'Checked In': 'bg-primary',
            'Checked Out': 'bg-info',
            'Cancelled': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    getPaymentStatusBadgeClass(paymentStatus) {
        const classes = {
            'Paid': 'bg-success',
            'Pending': 'bg-warning',
            'Partial': 'bg-info',
            'Refunded': 'bg-secondary'
        };
        return classes[paymentStatus] || 'bg-secondary';
    }

    confirmDelete(id) {
        this.currentBookingId = id;
        $('#deleteModal').modal('show');
    }

    confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    confirmCheckIn(id) {
        this.currentBookingId = id;
        $('#checkinModal').modal('show');
    }

    confirmCheckOut(id) {
        this.currentBookingId = id;
        $('#checkoutModal').modal('show');
    }

    confirmCancel(id) {
        this.currentBookingId = id;
        $('#cancelModal').modal('show');
    }

    showPaymentModal(id) {
        this.currentBookingId = id;
        $('#paymentModal').modal('show');
    }

    async deleteBooking() {
        if (!this.currentBookingId) return;

        try {
            const response = await $.ajax({
                url: `/bookings/${this.currentBookingId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error deleting booking.', 'error');
        } finally {
            $('#deleteModal').modal('hide');
            this.currentBookingId = null;
        }
    }

    async deleteAllBookings() {
        try {
            const response = await $.ajax({
                url: '/bookings',
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error deleting all bookings.', 'error');
        } finally {
            $('#deleteAllModal').modal('hide');
        }
    }

    async checkInBooking() {
        if (!this.currentBookingId) return;

        try {
            const response = await $.ajax({
                url: `/bookings/${this.currentBookingId}/checkin`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error checking in guest.', 'error');
        } finally {
            $('#checkinModal').modal('hide');
            this.currentBookingId = null;
        }
    }

    async checkOutBooking() {
        if (!this.currentBookingId) return;

        try {
            const response = await $.ajax({
                url: `/bookings/${this.currentBookingId}/checkout`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error checking out guest.', 'error');
        } finally {
            $('#checkoutModal').modal('hide');
            this.currentBookingId = null;
        }
    }

    async cancelBooking() {
        if (!this.currentBookingId) return;

        const reason = $('#cancellationReason').val();

        try {
            const response = await $.ajax({
                url: `/bookings/${this.currentBookingId}/cancel`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: reason
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error cancelling booking.', 'error');
        } finally {
            $('#cancelModal').modal('hide');
            this.currentBookingId = null;
            $('#cancellationReason').val('');
        }
    }

    async addPayment() {
        if (!this.currentBookingId) return;

        const amount = $('#paymentAmount').val();
        const paymentType = $('#paymentType').val();

        if (!amount || amount <= 0) {
            this.showAlert('Please enter a valid amount.', 'warning');
            return;
        }

        try {
            const response = await $.ajax({
                url: `/bookings/${this.currentBookingId}/payment`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    amount: amount,
                    payment_type: paymentType
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error adding payment.', 'error');
        } finally {
            $('#paymentModal').modal('hide');
            this.currentBookingId = null;
            $('#paymentAmount').val('');
            $('#paymentType').val('cash');
        }
    }

    showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        // Remove existing alerts
        $('.alert-dismissible').remove();

        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);

        $('.card-body').prepend(alert);

        setTimeout(() => {
            alert.alert('close');
        }, 5000);
    }
}

// Initialize booking manager when document is ready
$(document).ready(function () {
    window.bookingManager = new BookingManager();
});
