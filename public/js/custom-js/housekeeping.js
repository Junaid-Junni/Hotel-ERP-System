// public/js/housekeeping.js

class HousekeepingManager {
    constructor() {
        this.currentTaskId = null;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // View task
        $(document).on('click', '.view-btn', (e) => {
            this.viewTask($(e.currentTarget).data('id'));
        });

        // Delete task
        $(document).on('click', '.delete-btn', (e) => {
            this.confirmDelete($(e.currentTarget).data('id'));
        });

        // Delete all tasks
        $('#deleteAllBtn').on('click', () => {
            this.confirmDeleteAll();
        });

        // Start task
        $(document).on('click', '.start-btn', (e) => {
            this.confirmStart($(e.currentTarget).data('id'));
        });

        // Complete task
        $(document).on('click', '.complete-btn', (e) => {
            this.showCompleteModal($(e.currentTarget).data('id'));
        });

        // Cancel task
        $(document).on('click', '.cancel-btn', (e) => {
            this.showCancelModal($(e.currentTarget).data('id'));
        });

        // Confirm actions
        $('#confirmDelete').on('click', () => {
            this.deleteTask();
        });

        $('#confirmDeleteAll').on('click', () => {
            this.deleteAllTasks();
        });

        $('#confirmStart').on('click', () => {
            this.startTask();
        });

        $('#confirmComplete').on('click', () => {
            this.completeTask();
        });

        $('#confirmCancel').on('click', () => {
            this.cancelTask();
        });
    }

    async viewTask(id) {
        try {
            const response = await $.ajax({
                url: `/housekeeping/${id}`,
                type: 'GET'
            });

            if (response.success) {
                this.displayTaskDetails(response.task);
            } else {
                this.showAlert('Error loading task details.', 'error');
            }
        } catch (error) {
            this.showAlert('Error loading task details.', 'error');
        }
    }

    displayTaskDetails(task) {
        const startedAt = task.started_at ? new Date(task.started_at).toLocaleString() : 'Not started';
        const completedAt = task.completed_at ? new Date(task.completed_at).toLocaleString() : 'Not completed';
        const duration = task.actual_minutes ? task.actual_minutes + ' minutes' : 'N/A';

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Room:</th>
                            <td>${task.room.RoomNo} (${task.room.Type}) - Floor ${task.room.Floor}</td>
                        </tr>
                        <tr>
                            <th>Task Type:</th>
                            <td>${task.task_type}</td>
                        </tr>
                        <tr>
                            <th>Assigned To:</th>
                            <td>${task.assigned_employee.first_name} ${task.assigned_employee.last_name} (${task.assigned_employee.employee_id})</td>
                        </tr>
                        <tr>
                            <th>Priority:</th>
                            <td><span class="badge ${this.getPriorityBadgeClass(task.priority)}">${task.priority}</span></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge ${this.getStatusBadgeClass(task.status)}">${task.status}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Scheduled Date:</th>
                            <td>${new Date(task.scheduled_date).toLocaleString()}</td>
                        </tr>
                        <tr>
                            <th>Started At:</th>
                            <td>${startedAt}</td>
                        </tr>
                        <tr>
                            <th>Completed At:</th>
                            <td>${completedAt}</td>
                        </tr>
                        <tr>
                            <th>Estimated Time:</th>
                            <td>${task.estimated_minutes ? task.estimated_minutes + ' minutes' : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Actual Time:</th>
                            <td>${duration}</td>
                        </tr>
                    </table>
                </div>
            </div>

            ${task.description ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Task Description:</h6>
                    <p>${task.description}</p>
                </div>
            </div>
            ` : ''}

            ${task.notes ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Additional Notes:</h6>
                    <p>${task.notes}</p>
                </div>
            </div>
            ` : ''}

            ${task.cancellation_reason ? `
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Cancellation Reason:</h6>
                    <p class="text-danger">${task.cancellation_reason}</p>
                </div>
            </div>
            ` : ''}
        `;

        $('#viewModalBody').html(content);
        $('#viewModal').modal('show');
    }

    getPriorityBadgeClass(priority) {
        const classes = {
            'Low': 'bg-success',
            'Medium': 'bg-info',
            'High': 'bg-warning',
            'Urgent': 'bg-danger'
        };
        return classes[priority] || 'bg-secondary';
    }

    getStatusBadgeClass(status) {
        const classes = {
            'Pending': 'bg-warning',
            'In Progress': 'bg-primary',
            'Completed': 'bg-success',
            'Cancelled': 'bg-secondary'
        };
        return classes[status] || 'bg-secondary';
    }

    confirmDelete(id) {
        this.currentTaskId = id;
        $('#deleteModal').modal('show');
    }

    confirmDeleteAll() {
        $('#deleteAllModal').modal('show');
    }

    confirmStart(id) {
        this.currentTaskId = id;
        $('#startModal').modal('show');
    }

    showCompleteModal(id) {
        this.currentTaskId = id;
        $('#completeModal').modal('show');
    }

    showCancelModal(id) {
        this.currentTaskId = id;
        $('#cancelModal').modal('show');
    }

    async deleteTask() {
        if (!this.currentTaskId) return;

        try {
            const response = await $.ajax({
                url: `/housekeeping/${this.currentTaskId}`,
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
            this.showAlert('Error deleting task.', 'error');
        } finally {
            $('#deleteModal').modal('hide');
            this.currentTaskId = null;
        }
    }

    async deleteAllTasks() {
        try {
            const response = await $.ajax({
                url: '/housekeeping',
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
            this.showAlert('Error deleting all tasks.', 'error');
        } finally {
            $('#deleteAllModal').modal('hide');
        }
    }

    async startTask() {
        if (!this.currentTaskId) return;

        try {
            const response = await $.ajax({
                url: `/housekeeping/${this.currentTaskId}/start`,
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
            this.showAlert('Error starting task.', 'error');
        } finally {
            $('#startModal').modal('hide');
            this.currentTaskId = null;
        }
    }

    async completeTask() {
        if (!this.currentTaskId) return;

        const actualMinutes = $('#actualMinutes').val();
        const completionNotes = $('#completionNotes').val();

        if (!actualMinutes || actualMinutes < 1) {
            this.showAlert('Please enter a valid time taken.', 'warning');
            return;
        }

        try {
            const response = await $.ajax({
                url: `/housekeeping/${this.currentTaskId}/complete`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    actual_minutes: actualMinutes,
                    completion_notes: completionNotes
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
            this.showAlert('Error completing task.', 'error');
        } finally {
            $('#completeModal').modal('hide');
            this.currentTaskId = null;
            $('#actualMinutes').val('');
            $('#completionNotes').val('');
        }
    }

    async cancelTask() {
        if (!this.currentTaskId) return;

        const reason = $('#cancellationReason').val();

        if (!reason) {
            this.showAlert('Please enter a cancellation reason.', 'warning');
            return;
        }

        try {
            const response = await $.ajax({
                url: `/housekeeping/${this.currentTaskId}/cancel`,
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
            this.showAlert('Error cancelling task.', 'error');
        } finally {
            $('#cancelModal').modal('hide');
            this.currentTaskId = null;
            $('#cancellationReason').val('');
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

// Initialize housekeeping manager when document is ready
$(document).ready(function () {
    window.housekeepingManager = new HousekeepingManager();
});
