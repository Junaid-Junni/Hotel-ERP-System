// public/js/housekeeping.js

document.addEventListener('DOMContentLoaded', function () {
    console.log('Housekeeping Manager initialized');

    let currentTaskId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Task
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            viewTask(taskId);
        });
    });

    // Delete Task
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            confirmDelete(taskId);
        });
    });

    // Mark In Progress
    document.querySelectorAll('.progress-btn').forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            confirmProgress(taskId);
        });
    });

    // Mark Complete
    document.querySelectorAll('.complete-btn').forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            confirmComplete(taskId);
        });
    });

    // Cancel Task
    document.querySelectorAll('.cancel-btn').forEach(button => {
        button.addEventListener('click', function () {
            const taskId = this.getAttribute('data-id');
            confirmCancel(taskId);
        });
    });

    // Confirm Delete
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', deleteTask);
    }

    // Confirm Progress
    const confirmProgressBtn = document.getElementById('confirmProgress');
    if (confirmProgressBtn) {
        confirmProgressBtn.addEventListener('click', markInProgress);
    }

    // Confirm Complete
    const confirmCompleteBtn = document.getElementById('confirmComplete');
    if (confirmCompleteBtn) {
        confirmCompleteBtn.addEventListener('click', markCompleted);
    }

    // Confirm Cancel
    const confirmCancelBtn = document.getElementById('confirmCancel');
    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', cancelTask);
    }

    async function viewTask(id) {
        try {
            console.log('Fetching task details for ID:', id);

            const response = await fetch(`/housekeeping/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            console.log('View task response:', data);

            if (data.success) {
                displayTaskDetails(data.housekeeping);
            } else {
                showAlert('Error loading task details: ' + (data.message || 'Unknown error'), 'error');
            }
        } catch (error) {
            console.error('Error in viewTask:', error);
            showAlert('Error loading task details. Please try again.', 'error');
        }
    }

    function displayTaskDetails(task) {
        console.log('Displaying task details:', task);

        const tasksList = task.tasks && task.tasks.length > 0
            ? task.tasks.map(t => `<li>${t}</li>`).join('')
            : '<li class="text-muted">No tasks specified</li>';

        const startedAt = task.started_at
            ? new Date(task.started_at).toLocaleString()
            : 'Not started';

        const completedAt = task.completed_at
            ? new Date(task.completed_at).toLocaleString()
            : 'Not completed';

        const qualityRating = task.quality_rating
            ? `${task.quality_rating}/5 ${'★'.repeat(task.quality_rating)}${'☆'.repeat(5 - task.quality_rating)}`
            : 'Not rated';

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Room Information</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Room Number:</th>
                            <td>${task.room.RoomNo}</td>
                        </tr>
                        <tr>
                            <th>Room Type:</th>
                            <td>${task.room.Type}</td>
                        </tr>
                        <tr>
                            <th>Floor:</th>
                            <td>${task.room.Floor}</td>
                        </tr>
                        <tr>
                            <th>Room Status:</th>
                            <td><span class="badge badge-secondary">${task.room.Status}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Housekeeper Information</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Name:</th>
                            <td>${task.employee.first_name} ${task.employee.last_name}</td>
                        </tr>
                        <tr>
                            <th>Employee ID:</th>
                            <td>${task.employee.employee_id}</td>
                        </tr>
                        <tr>
                            <th>Position:</th>
                            <td>${task.employee.position}</td>
                        </tr>
                        <tr>
                            <th>Department:</th>
                            <td>${task.employee.department}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h6>Cleaning Details</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Date & Time:</th>
                            <td>${task.formatted_cleaning_date} at ${task.formatted_cleaning_time}</td>
                        </tr>
                        <tr>
                            <th>Cleaning Type:</th>
                            <td><span class="badge badge-${task.cleaning_type_badge}">${task.cleaning_type}</span></td>
                        </tr>
                        <tr>
                            <th>Duration:</th>
                            <td>${task.duration_formatted}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge badge-${task.status_badge}">${task.status}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Progress Tracking</h6>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <th width="40%">Started At:</th>
                            <td>${startedAt}</td>
                        </tr>
                        <tr>
                            <th>Completed At:</th>
                            <td>${completedAt}</td>
                        </tr>
                        <tr>
                            <th>Quality Rating:</th>
                            <td>${qualityRating}</td>
                        </tr>
                        <tr>
                            <th>Supplies Cost:</th>
                            <td>$${parseFloat(task.cleaning_supplies_cost || 0).toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Cleaning Tasks</h6>
                    <ul>
                        ${tasksList}
                    </ul>
                </div>
            </div>
            ${task.special_instructions ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Special Instructions:</h6>
                    <div class="alert alert-light">
                        ${task.special_instructions}
                    </div>
                </div>
            </div>
            ` : ''}
            ${task.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Notes:</h6>
                    <div class="alert alert-info">
                        ${task.notes}
                    </div>
                </div>
            </div>
            ` : ''}
            ${task.issues_found ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Issues Found:</h6>
                    <div class="alert alert-warning">
                        ${task.issues_found}
                    </div>
                </div>
            </div>
            ` : ''}
            ${task.supervisor_notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Supervisor Notes:</h6>
                    <div class="alert alert-secondary">
                        ${task.supervisor_notes}
                    </div>
                </div>
            </div>
            ` : ''}
        `;

        document.getElementById('viewModalBody').innerHTML = content;
        $('#viewModal').modal('show');
    }

    function confirmDelete(id) {
        currentTaskId = id;
        $('#deleteModal').modal('show');
    }

    function confirmProgress(id) {
        currentTaskId = id;
        $('#progressModal').modal('show');
    }

    function confirmComplete(id) {
        currentTaskId = id;
        $('#completeModal').modal('show');
    }

    function confirmCancel(id) {
        currentTaskId = id;
        $('#cancelModal').modal('show');
    }

    async function deleteTask() {
        if (!currentTaskId) {
            showAlert('Error: No task selected for deletion.', 'error');
            return;
        }

        try {
            const response = await fetch(`/housekeeping/${currentTaskId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#deleteModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showAlert('Error deleting task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
        }
    }

    async function markInProgress() {
        if (!currentTaskId) {
            showAlert('Error: No task selected.', 'error');
            return;
        }

        try {
            const response = await fetch(`/housekeeping/${currentTaskId}/in-progress`, {
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
                $('#progressModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Progress error:', error);
            showAlert('Error updating task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
        }
    }

    async function markCompleted() {
        if (!currentTaskId) {
            showAlert('Error: No task selected.', 'error');
            return;
        }

        const form = document.getElementById('completeForm');
        const formData = new FormData(form);

        // Validate quality rating
        const qualityRating = formData.get('quality_rating');
        if (!qualityRating) {
            showAlert('Please select a quality rating.', 'warning');
            return;
        }

        const requestData = {
            quality_rating: parseInt(qualityRating),
            cleaning_supplies_cost: parseFloat(formData.get('cleaning_supplies_cost') || 0),
            issues_found: formData.get('issues_found'),
            supervisor_notes: formData.get('supervisor_notes')
        };

        try {
            const response = await fetch(`/housekeeping/${currentTaskId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            const data = await response.json();

            if (data.success) {
                showAlert(data.message, 'success');
                $('#completeModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Complete error:', error);
            showAlert('Error completing task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
        }
    }

    async function cancelTask() {
        if (!currentTaskId) {
            showAlert('Error: No task selected.', 'error');
            return;
        }

        try {
            const response = await fetch(`/housekeeping/${currentTaskId}/cancel`, {
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
                $('#cancelModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Cancel error:', error);
            showAlert('Error cancelling task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
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
        if (cardBody) {
            cardBody.insertAdjacentHTML('afterbegin', alert);
        }

        setTimeout(() => {
            const alertElement = document.querySelector('.alert-dismissible');
            if (alertElement) {
                alertElement.remove();
            }
        }, 5000);
    }
});
