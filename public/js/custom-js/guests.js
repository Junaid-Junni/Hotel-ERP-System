// public/js/guests.js
class GuestManager {
    constructor() {
        this.currentGuestId = null;
        this.init();
    }

    init() {
        this.initializeDataTable();
        this.bindEvents();
    }

    initializeDataTable() {
        if ($.fn.DataTable.isDataTable('#guestsTable')) {
            $('#guestsTable').DataTable().destroy();
        }

        this.dataTable = $('#guestsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '/guests/data',
                type: 'GET'
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'full_name', name: 'full_name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'id_type', name: 'id_type' },
                { data: 'id_number', name: 'id_number' },
                { data: 'nationality', name: 'nationality' },
                { data: 'date_of_birth', name: 'date_of_birth' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            language: {
                emptyTable: 'No guests found',
                zeroRecords: 'No matching guests found'
            }
        });
    }

    bindEvents() {
        // View guest
        $(document).on('click', '.ViewBtn', (e) => {
            this.viewGuest($(e.currentTarget).data('id'));
        });

        // Edit guest
        $(document).on('click', '.EditBtn', (e) => {
            this.editGuest($(e.currentTarget).data('id'));
        });

        // Delete guest
        $(document).on('click', '.DeleteBtn', (e) => {
            this.confirmDelete($(e.currentTarget).data('id'));
        });

        // Confirm delete
        $('#confirmDeleteBtn').on('click', () => {
            this.deleteGuest();
        });

        // Form submissions
        $('#createGuestForm').on('submit', (e) => this.handleFormSubmit(e, 'create'));
        $('#editGuestForm').on('submit', (e) => this.handleFormSubmit(e, 'edit'));
    }

    async viewGuest(id) {
        try {
            const response = await $.ajax({
                url: `/guests/${id}`,
                type: 'GET'
            });

            const guest = response;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Full Name:</th>
                                <td>${guest.first_name} ${guest.last_name}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>${guest.email}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>${guest.phone}</td>
                            </tr>
                            <tr>
                                <th>Date of Birth:</th>
                                <td>${guest.date_of_birth ? new Date(guest.date_of_birth).toLocaleDateString() : 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Nationality:</th>
                                <td>${guest.nationality}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>ID Type:</th>
                                <td>${guest.id_type}</td>
                            </tr>
                            <tr>
                                <th>ID Number:</th>
                                <td>${guest.id_number}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>${guest.address}</td>
                            </tr>
                            <tr>
                                <th>City:</th>
                                <td>${guest.city}</td>
                            </tr>
                            <tr>
                                <th>Country:</th>
                                <td>${guest.country}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                ${guest.notes ? `
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Notes:</h6>
                        <p>${guest.notes}</p>
                    </div>
                </div>
                ` : ''}
            `;

            $('#viewGuestContent').html(content);
            $('#viewGuestModal').modal('show');
        } catch (error) {
            this.showAlert('Error loading guest details.', 'error');
        }
    }

    editGuest(id) {
        window.location.href = `/guests/${id}/edit`;
    }

    confirmDelete(id) {
        this.currentGuestId = id;
        $('#deleteGuestModal').modal('show');
    }

    async deleteGuest() {
        if (!this.currentGuestId) return;

        try {
            const response = await $.ajax({
                url: `/guests/${this.currentGuestId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showAlert(response.message, 'success');
                this.refreshDataTable();
            } else {
                this.showAlert(response.message, 'error');
            }
        } catch (error) {
            this.showAlert('Error deleting guest.', 'error');
        } finally {
            $('#deleteGuestModal').modal('hide');
            this.currentGuestId = null;
        }
    }

    async handleFormSubmit(e, type) {
        e.preventDefault();

        const form = $(e.target);
        const url = form.attr('action');
        const method = form.find('input[name="_method"]').val() || 'POST';
        const formData = form.serialize();

        // Show loading state
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

        try {
            const response = await $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (type === 'create') {
                this.showAlert('Guest created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/guests';
                }, 1500);
            } else {
                this.showAlert('Guest updated successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '/guests';
                }, 1500);
            }
        } catch (error) {
            if (error.status === 422) {
                this.handleValidationErrors(error.responseJSON.errors, form);
            } else {
                this.showAlert(error.responseJSON?.message || 'An error occurred.', 'error');
            }
        } finally {
            submitBtn.prop('disabled', false).html(originalText);
        }
    }

    handleValidationErrors(errors, form) {
        // Clear previous errors
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').html('');

        // Show new errors
        Object.keys(errors).forEach(field => {
            const input = form.find(`[name="${field}"]`);
            const feedback = input.next('.invalid-feedback');

            input.addClass('is-invalid');
            feedback.html(errors[field][0]);
        });
    }

    showAlert(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

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

    refreshDataTable() {
        if (this.dataTable) {
            this.dataTable.ajax.reload();
        }
    }
}

// Initialize guest manager when document is ready
$(document).ready(function () {
    window.guestManager = new GuestManager();
});
