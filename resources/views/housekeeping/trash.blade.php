@extends('layouts.app')

@section('title', 'Housekeeping Trash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-trash-alt"></i> Housekeeping Trash
                        </h3>
                        <div class="btn-group">
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Back to Tasks
                            </a>
                            <button class="btn btn-danger" id="emptyTrashBtn">
                                <i class="fas fa-trash"></i> Empty Trash
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($trashedHousekeepings->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Deleted housekeeping tasks are kept in trash for 30 days before being permanently deleted.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Room</th>
                                        <th>Housekeeper</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Deleted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trashedHousekeepings as $index => $hk)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $hk->room->RoomNo }}</strong>
                                            <br><small class="text-muted">{{ $hk->room->Type }}</small>
                                        </td>
                                        <td>
                                            {{ $hk->employee->first_name }} {{ $hk->employee->last_name }}
                                            <br><small class="text-muted">{{ $hk->employee->employee_id }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $hk->formatted_cleaning_date }}</strong>
                                            <br><small>{{ $hk->formatted_cleaning_time }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $hk->cleaning_type_badge }}">
                                                {{ $hk->cleaning_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $hk->status_badge }}">
                                                {{ $hk->status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $hk->deleted_at->format('M d, Y h:i A') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ $hk->deleted_at->diffForHumans() }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $hk->id }}" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success restore-btn" data-id="{{ $hk->id }}" title="Restore">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger permanent-delete-btn" data-id="{{ $hk->id }}" title="Delete Permanently">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Trash Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-trash"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total in Trash</span>
                                        <span class="info-box-number">{{ $trashedHousekeepings->count() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Oldest Item</span>
                                        <span class="info-box-number">
                                            @if($trashedHousekeepings->count() > 0)
                                                {{ $trashedHousekeepings->sortBy('deleted_at')->first()->deleted_at->diffForHumans() }}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-calendar"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Auto Cleanup</span>
                                        <span class="info-box-number">30 days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5><i class="fas fa-trash-alt fa-3x mb-3"></i></h5>
                            <h5>Trash is Empty!</h5>
                            <p class="text-muted">There are no deleted housekeeping tasks in the trash.</p>
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Housekeeping Tasks
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Housekeeping Task Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Empty Trash Modal -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Empty Trash</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to empty the trash?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>This action will permanently delete ALL {{ $trashedHousekeepings->count() }} items in trash!</strong>
                </p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmEmptyTrash">Empty Trash</button>
            </div>
        </div>
    </div>
</div>

<!-- Permanent Delete Modal -->
<div class="modal fade" id="permanentDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Permanent Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete this housekeeping task?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>This action cannot be undone!</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmPermanentDelete">Delete Permanently</button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Restore</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this housekeeping task?</p>
                <p class="text-info">
                    <i class="fas fa-info-circle"></i>
                    The task will be moved back to the active housekeeping tasks list.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestore">Restore Task</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Housekeeping Trash Manager initialized');

    let currentTaskId = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Task
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            viewTask(taskId);
        });
    });

    // Restore Task
    document.querySelectorAll('.restore-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            confirmRestore(taskId);
        });
    });

    // Permanent Delete Task
    document.querySelectorAll('.permanent-delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            confirmPermanentDelete(taskId);
        });
    });

    // Empty Trash
    const emptyTrashBtn = document.getElementById('emptyTrashBtn');
    if (emptyTrashBtn) {
        emptyTrashBtn.addEventListener('click', confirmEmptyTrash);
    }

    // Confirm Restore
    const confirmRestoreBtn = document.getElementById('confirmRestore');
    if (confirmRestoreBtn) {
        confirmRestoreBtn.addEventListener('click', restoreTask);
    }

    // Confirm Permanent Delete
    const confirmPermanentDeleteBtn = document.getElementById('confirmPermanentDelete');
    if (confirmPermanentDeleteBtn) {
        confirmPermanentDeleteBtn.addEventListener('click', permanentDeleteTask);
    }

    // Confirm Empty Trash
    const confirmEmptyTrashBtn = document.getElementById('confirmEmptyTrash');
    if (confirmEmptyTrashBtn) {
        confirmEmptyTrashBtn.addEventListener('click', emptyTrash);
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
            ? `${task.quality_rating}/5 ${'★'.repeat(task.quality_rating)}${'☆'.repeat(5-task.quality_rating)}`
            : 'Not rated';

        const deletedAt = task.deleted_at
            ? new Date(task.deleted_at).toLocaleString()
            : 'N/A';

        const content = `
            <div class="alert alert-warning">
                <i class="fas fa-trash"></i>
                <strong>This task is in trash.</strong> Deleted on: ${deletedAt}
            </div>

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

    function confirmRestore(id) {
        currentTaskId = id;
        $('#restoreModal').modal('show');
    }

    function confirmPermanentDelete(id) {
        currentTaskId = id;
        $('#permanentDeleteModal').modal('show');
    }

    function confirmEmptyTrash() {
        $('#emptyTrashModal').modal('show');
    }

    async function restoreTask() {
        if (!currentTaskId) {
            showAlert('Error: No task selected for restoration.', 'error');
            return;
        }

        try {
            const response = await fetch(`/housekeeping/trash/${currentTaskId}/restore`, {
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
                $('#restoreModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Restore error:', error);
            showAlert('Error restoring task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
        }
    }

    async function permanentDeleteTask() {
        if (!currentTaskId) {
            showAlert('Error: No task selected for deletion.', 'error');
            return;
        }

        try {
            const response = await fetch(`/housekeeping/trash/${currentTaskId}/destroy`, {
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
                $('#permanentDeleteModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Permanent delete error:', error);
            showAlert('Error deleting task. Please try again.', 'error');
        } finally {
            currentTaskId = null;
        }
    }

    async function emptyTrash() {
        try {
            const response = await fetch('/housekeeping/trash/empty', {
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
                $('#emptyTrashModal').modal('hide');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(data.message, 'error');
            }
        } catch (error) {
            console.error('Empty trash error:', error);
            showAlert('Error emptying trash. Please try again.', 'error');
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
</script>
@endpush
