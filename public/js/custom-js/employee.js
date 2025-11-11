// public/js/employees.js

class EmployeeManager {
    constructor() {
        this.currentEmployeeId = null;
        this.currentStatus = null;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // View employee
        $(document).on('click', '.view-btn', (e) => {
            this.viewEmployee($(e.currentTarget).data('id'));
        });

        // Delete employee
        $(document).on('click', '.delete-btn', (e) => {
            this.confirmDelete($(e.currentTarget).data('id'));
        });

        // Delete all employees
        $('#deleteAllBtn').on('click', () => {
            this.confirmDeleteAll();
        });

        // Status change
        $(document).on('click', '.status-btn', (e) => {
            const target = $(e.currentTarget);
            this.confirmStatusChange(target.data('id'), target.data('status'));
        });

        // Confirm actions
        $('#confirmDelete').on('click', () => {
            this.deleteEmployee();
        });

        $('#confirmDeleteAll').on('click', () => {
            this.deleteAllEmployees();
        });

        $('#confirmStatus').on('click', () => {
            this.updateEmployeeStatus();
        });
    }

    async viewEmployee(id) {
        try {
            const response = await $.ajax({
                url: `/employees/${id}`,
                type: 'GET'
            });

            if (response.success) {
                this.displayEmployeeDetails(response.employee);
            } else {
                this.showAlert('Error loading employee details.', 'error');
            }
        } catch (error) {
            this.showAlert('Error loading employee details.', 'error');
        }
    }

    displayEmployeeDetails(employee) {
        const profileImage = employee.profile_image
            ? `<img src="/storage/${employee.profile_image}" class="img-fluid rounded mb-3" style="max-height: 200px;">`
            : '<div class="text-muted mb-3">No profile image</div>';

        const content = `
            <div class="row">
                <div class="col-md-4 text-center">
                    ${profileImage}
                    <h4>${employee.first_name} ${employee.last_name}</h4>
                    <p class="text-muted">${employee.employee_id}</p>
                    <span class="badge ${this.getStatusBadgeClass(employee.status)}">${employee.status}</span>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Email:</th>
                                    <td>${employee.email}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>${employee.phone}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth:</th>
                                    <td>${new Date(employee.date_of_birth).toLocaleDateString()}</td>
                                </tr>
                                <tr>
                                    <th>Gender:</th>
                                    <td>${employee.gender}</td>
                                </tr>
                                <tr>
                                    <th>Position:</th>
                                    <td>${employee.position}</td>
                                </tr>
                                <tr>
                                    <th>Department:</th>
                                    <td>${employee.department}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Salary:</th>
                                    <td>$${parseFloat(employee.salary).toFixed(2)}</td>
                                </tr>
                                <tr>
                                    <th>Hire Date:</th>
                                    <td>${new Date(employee.hire_date).toLocaleDateString()}</td>
                                </tr>
                                <tr>
                                    <th>Employment Type:</th>
                                    <td>${employee.employment_type}</td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>${employee.address}, ${employee.city}, ${employee.state} ${employee.zip_code}</td>
                                </tr>
                                <tr>
                                    <th>Country:</th>
                                    <td>${employee.country}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Emergency Contact</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Name:</th>
                            <td>${employee.emergency_contact_name}</td>
                            <th>Phone:</th>
                            <td>${employee.emergency_contact_phone}</td>
                            <th>Relationship:</th>
                            <td>${employee.emergency_contact_relation}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Bank Information -->
            ${employee.bank_name ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Bank Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Bank Name:</th>
                            <td>${employee.bank_name}</td>
                            <th>Account Number:</th>
                            <td>${employee.account_number ? '••••' + employee.account_number.slice(-4) : 'N/A'}</td>
                            <th>Routing Number:</th>
                            <td>${employee.routing_number || 'N/A'}</td>
                        </tr>
                    </table>
                </div>
            </div>
            ` : ''}

            <!-- Additional Information -->
            ${employee.notes ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Additional Information</h5>
                    <div class="card">
                        <div class="card-body">
                            <p>${employee.notes}</p>
                        </div>
                    </div>
                </div>
            </div>
            ` : ''}
        `;

        $('#viewModalBody').html(content);
        $('#viewModal').modal('show');
    }

    getStatusBadgeClass(status) {
        const classes = {
            'Active': 'bg-success',
            'Inactive': 'bg-secondary',
            'Suspended': 'bg-warning',
            'Terminated': 'bg-danger'
        };
        return classes[status] || 'bg-secondary';
    }

    confirmDelete(id) {
        this.currentEmployeeId = id;
        $('#deleteModal').modal('show');
    }

    confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    confirmStatusChange(id, status) {
        this.currentEmployeeId = id;
        this.currentStatus = status;
        $('#statusText').text(status);
        $('#statusModal').modal('show');
    }

    async deleteEmployee() {
        if (!this.currentEmployeeId) return;

        try {
            const response = await $.ajax({
                url: `/employees/${this.currentEmployeeId}`,
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
            this.showAlert('Error deleting employee.', 'error');
        } finally {
            $('#deleteModal').modal('hide');
            this.currentEmployeeId = null;
        }
    }

    async deleteAllEmployees() {
        try {
            const response = await $.ajax({
                url: '/employees',
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
            this.showAlert('Error deleting all employees.', 'error');
        } finally {
            $('#deleteAllModal').modal('hide');
        }
    }

    async updateEmployeeStatus() {
        if (!this.currentEmployeeId || !this.currentStatus) return;

        try {
            const response = await $.ajax({
                url: `/employees/${this.currentEmployeeId}/status`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    status: this.currentStatus
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
            this.showAlert('Error updating employee status.', 'error');
        } finally {
            $('#statusModal').modal('hide');
            this.currentEmployeeId = null;
            this.currentStatus = null;
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

// Initialize employee manager when document is ready
$(document).ready(function () {
    window.employeeManager = new EmployeeManager();
});
