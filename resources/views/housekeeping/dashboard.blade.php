@extends('layouts.app')

@section('title', 'Housekeeping Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $scheduledCount }}</h3>
                    <p>Scheduled Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <a href="{{ route('housekeeping.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inProgressCount }}</h3>
                    <p>In Progress</p>
                </div>
                <div class="icon">
                    <i class="fas fa-broom"></i>
                </div>
                <a href="{{ route('housekeeping.index') }}?status=In%20Progress" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $completedToday }}</h3>
                    <p>Completed Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('housekeeping.index') }}?status=Completed" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $overdueCount }}</h3>
                    <p>Overdue Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <a href="{{ route('housekeeping.index') }}" class="small-box-footer">
                    More info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Today's Tasks -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Today's Cleaning Schedule - {{ date('M d, Y') }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('housekeeping.create') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-plus"></i> Add Task
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($todayTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Time</th>
                                        <th>Room</th>
                                        <th>Housekeeper</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayTasks as $task)
                                    <tr class="@if($task->status == 'Scheduled' && $task->cleaning_time < date('H:i:s')) table-warning @endif">
                                        <td>
                                            <strong>{{ $task->formatted_cleaning_time }}</strong>
                                            @if($task->status == 'Scheduled' && $task->cleaning_time < date('H:i:s'))
                                                <br><small class="text-danger">Overdue</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $task->room->RoomNo }}</strong>
                                            <br><small class="text-muted">{{ $task->room->Type }}</small>
                                        </td>
                                        <td>{{ $task->employee->first_name }} {{ $task->employee->last_name }}</td>
                                        <td>
                                            <span class="badge badge-{{ $task->cleaning_type_badge }}">
                                                {{ $task->cleaning_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $task->status_badge }}">
                                                {{ $task->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($task->status == 'Scheduled')
                                                    <button class="btn btn-success progress-btn" data-id="{{ $task->id }}" title="Start">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @elseif($task->status == 'In Progress')
                                                    <button class="btn btn-primary complete-btn" data-id="{{ $task->id }}" title="Complete">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-info view-btn" data-id="{{ $task->id }}" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('housekeeping.edit', $task->id) }}" class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-broom fa-3x text-muted mb-3"></i>
                            <h5>No tasks scheduled for today</h5>
                            <p class="text-muted">All rooms are clean and ready for guests!</p>
                            <a href="{{ route('housekeeping.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Schedule a Cleaning Task
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Housekeeping Staff -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Housekeeping Staff
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($employees as $employee)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                                    <small class="text-muted">{{ $employee->employee_id }}</small>
                                </div>
                                <span class="badge badge-primary badge-pill">{{ $employee->today_tasks_count }}</span>
                            </div>
                            <small class="text-muted">Today's tasks</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('housekeeping.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Schedule Cleaning
                        </a>
                        <a href="{{ route('housekeeping.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> View All Tasks
                        </a>
                        <a href="{{ route('rooms.index') }}" class="btn btn-info">
                            <i class="fas fa-hotel"></i> Room Status
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Summary -->
            <div class="card mt-4">
                <div class="card-header bg-warning text-white">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Today's Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Tasks Today
                            <span class="badge badge-primary badge-pill">{{ $todayTasks->count() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Completed
                            <span class="badge badge-success badge-pill">{{ $completedToday }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            In Progress
                            <span class="badge badge-warning badge-pill">{{ $inProgressCount }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Pending
                            <span class="badge badge-info badge-pill">{{ $scheduledCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the modals from index view -->
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

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Start Cleaning</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this task as In Progress?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmProgress">Start Cleaning</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Cleaning Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeForm">
                    <div class="form-group">
                        <label for="quality_rating">Quality Rating *</label>
                        <select class="form-control" id="quality_rating" name="quality_rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2 - Fair</option>
                            <option value="3">3 - Good</option>
                            <option value="4">4 - Very Good</option>
                            <option value="5">5 - Excellent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cleaning_supplies_cost">Cleaning Supplies Cost ($)</label>
                        <input type="number" step="0.01" class="form-control" id="cleaning_supplies_cost" name="cleaning_supplies_cost" value="0">
                    </div>
                    <div class="form-group">
                        <label for="issues_found">Issues Found</label>
                        <textarea class="form-control" id="issues_found" name="issues_found" rows="2" placeholder="Any issues found during cleaning..."></textarea>
                    </div>
                    <div class="form-group">
                        <label for="supervisor_notes">Supervisor Notes</label>
                        <textarea class="form-control" id="supervisor_notes" name="supervisor_notes" rows="2" placeholder="Supervisor comments..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmComplete">Mark Complete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View Task
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            viewTask(taskId);
        });
    });

    // Mark In Progress
    document.querySelectorAll('.progress-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            confirmProgress(taskId);
        });
    });

    // Mark Complete
    document.querySelectorAll('.complete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            confirmComplete(taskId);
        });
    });

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

    async function viewTask(id) {
        try {
            const response = await fetch(`/housekeeping/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                displayTaskDetails(data.housekeeping);
            } else {
                alert('Error loading task details: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error in viewTask:', error);
            alert('Error loading task details. Please try again.');
        }
    }

    function displayTaskDetails(task) {
        // Use the same displayTaskDetails function from your housekeeping.js
        // This should be the same as in your main housekeeping JavaScript file
        // For brevity, I'm including a simplified version
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Room: ${task.room.RoomNo} (${task.room.Type})</h6>
                    <h6>Housekeeper: ${task.employee.first_name} ${task.employee.last_name}</h6>
                </div>
                <div class="col-md-6">
                    <h6>Time: ${task.formatted_cleaning_date} at ${task.formatted_cleaning_time}</h6>
                    <h6>Type: <span class="badge badge-${task.cleaning_type_badge}">${task.cleaning_type}</span></h6>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Tasks:</h6>
                    <ul>
                        ${task.tasks && task.tasks.length > 0 ? task.tasks.map(t => `<li>${t}</li>`).join('') : '<li class="text-muted">No tasks specified</li>'}
                    </ul>
                </div>
            </div>
            ${task.special_instructions ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>Special Instructions:</h6>
                    <p>${task.special_instructions}</p>
                </div>
            </div>
            ` : ''}
        `;

        document.getElementById('viewModalBody').innerHTML = content;
        $('#viewModal').modal('show');
    }

    function confirmProgress(id) {
        currentTaskId = id;
        $('#progressModal').modal('show');
    }

    function confirmComplete(id) {
        currentTaskId = id;
        $('#completeModal').modal('show');
    }

    async function markInProgress() {
        if (!currentTaskId) return;

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
                $('#progressModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Progress error:', error);
            alert('Error updating task status.');
        }
    }

    async function markCompleted() {
        if (!currentTaskId) return;

        const form = document.getElementById('completeForm');
        const formData = new FormData(form);
        const qualityRating = formData.get('quality_rating');

        if (!qualityRating) {
            alert('Please select a quality rating.');
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
                $('#completeModal').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Complete error:', error);
            alert('Error completing task.');
        }
    }
});
</script>
@endpush
