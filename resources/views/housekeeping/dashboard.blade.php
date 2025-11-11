{{-- resources/views/housekeeping/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Housekeeping Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fa fa-dashboard mr-2"></i>Housekeeping Dashboard
                        </h3>
                        <div>
                            <a href="{{ route('housekeeping.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> New Task
                            </a>
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> All Tasks
                            </a>
                            <a href="{{ route('housekeeping.calendar') }}" class="btn btn-info btn-sm">
                                <i class="fa fa-calendar"></i> Calendar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fa fa-tasks"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Tasks</span>
                    <span class="info-box-number">{{ $totalTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        All housekeeping tasks
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-warning">
                <span class="info-box-icon"><i class="fa fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Tasks</span>
                    <span class="info-box-number">{{ $pendingTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $totalTasks > 0 ? ($pendingTasks/$totalTasks)*100 : 0 }}%"></div>
                    </div>
                    <span class="progress-description">
                        Waiting to be started
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-primary">
                <span class="info-box-icon"><i class="fa fa-spinner"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">In Progress</span>
                    <span class="info-box-number">{{ $inProgressTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $totalTasks > 0 ? ($inProgressTasks/$totalTasks)*100 : 0 }}%"></div>
                    </div>
                    <span class="progress-description">
                        Currently being worked on
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completed</span>
                    <span class="info-box-number">{{ $completedTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ $totalTasks > 0 ? ($completedTasks/$totalTasks)*100 : 0 }}%"></div>
                    </div>
                    <span class="progress-description">
                        Successfully completed
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-secondary">
                <span class="info-box-icon"><i class="fa fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today's Tasks</span>
                    <span class="info-box-number">{{ $todayTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Scheduled for today
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-danger">
                <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Overdue Tasks</span>
                    <span class="info-box-number">{{ $overdueTasks }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Past scheduled date
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-info">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Active Staff</span>
                    <span class="info-box-number">{{ $activeStaff }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Housekeeping team
                    </span>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="info-box bg-gradient-success">
                <span class="info-box-icon"><i class="fa fa-home"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Available Rooms</span>
                    <span class="info-box-number">{{ $availableRooms }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: 100%"></div>
                    </div>
                    <span class="progress-description">
                        Ready for guests
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Tasks -->
    <div class="row">
        <!-- Task Status Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Task Status Distribution</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="taskStatusChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Priority Distribution Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Priority Distribution</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="priorityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tasks Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Tasks</h3>
                    <div class="card-tools">
                        <a href="{{ route('housekeeping.create') }}" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> New Task
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentTasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Room</th>
                                        <th>Task Type</th>
                                        <th>Assigned To</th>
                                        <th>Scheduled</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTasks as $task)
                                    <tr class="{{ $task->is_overdue ? 'table-danger' : '' }}">
                                        <td>
                                            <strong>{{ $task->room->RoomNo }}</strong>
                                            <br><small class="text-muted">{{ $task->room->Type }}</small>
                                        </td>
                                        <td>{{ $task->task_type }}</td>
                                        <td>
                                            {{ $task->assignedEmployee->first_name }}
                                            <br><small class="text-muted">{{ $task->assignedEmployee->employee_id }}</small>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y') }}
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($task->scheduled_date)->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($task->status == 'Pending') {{ $task->is_overdue ? 'bg-danger' : 'bg-warning' }}
                                                @elseif($task->status == 'In Progress') bg-primary
                                                @elseif($task->status == 'Completed') bg-success
                                                @else bg-secondary
                                                @endif">
                                                {{ $task->status }}
                                            </span>
                                            @if($task->is_overdue)
                                                <br><small class="text-danger">Overdue</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($task->priority == 'Low') bg-success
                                                @elseif($task->priority == 'Medium') bg-info
                                                @elseif($task->priority == 'High') bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('housekeeping.show', $task->id) }}" class="btn btn-info" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('housekeeping.edit', $task->id) }}" class="btn btn-warning" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if($task->status == 'Pending')
                                                    <button class="btn btn-success start-btn" data-id="{{ $task->id }}" title="Start">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                @elseif($task->status == 'In Progress')
                                                    <button class="btn btn-primary complete-btn" data-id="{{ $task->id }}" title="Complete">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5><i class="icon fa fa-info"></i> No Recent Tasks</h5>
                            <p>There are no recent housekeeping tasks. <a href="{{ route('housekeeping.create') }}">Create the first task</a> to get started.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-primary float-right">
                        <i class="fa fa-list"></i> View All Tasks
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-gradient-primary">
                <div class="card-body text-center">
                    <h4><i class="fa fa-clock fa-2x"></i></h4>
                    <h3>Efficiency</h3>
                    <p class="mb-0">
                        {{ $efficiency }}%
                    </p>
                    <small>Tasks completed on or before estimated time</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-gradient-success">
                <div class="card-body text-center">
                    <h4><i class="fa fa-user-check fa-2x"></i></h4>
                    <h3>Top Performer</h3>
                    <p class="mb-0">
                        {{ $topPerformer ? $topPerformer->first_name . ' ' . $topPerformer->last_name : 'N/A' }}
                    </p>
                    <small>Most tasks completed</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-gradient-info">
                <div class="card-body text-center">
                    <h4><i class="fa fa-chart-line fa-2x"></i></h4>
                    <h3>Productivity</h3>
                    <p class="mb-0">
                        {{ $todayCompleted }} tasks
                    </p>
                    <small>Completed today</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.info-box {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    border-radius: 0.25rem;
    background: #fff;
    display: flex;
    margin-bottom: 1rem;
    min-height: 80px;
    padding: 0.5rem;
    position: relative;
}
.info-box .info-box-icon {
    border-radius: 0.25rem;
    align-items: center;
    display: flex;
    font-size: 1.875rem;
    justify-content: center;
    text-align: center;
    width: 70px;
}
.info-box .info-box-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    line-height: 1.8;
    flex: 1;
    padding: 0 10px;
}
.info-box .info-box-number {
    font-size: 1.5rem;
    font-weight: 700;
}
.bg-gradient-primary { background: linear-gradient(45deg, #007bff, #6610f2); color: white; }
.bg-gradient-success { background: linear-gradient(45deg, #28a745, #20c997); color: white; }
.bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #6f42c1); color: white; }
.bg-gradient-warning { background: linear-gradient(45deg, #ffc107, #fd7e14); color: white; }
.bg-gradient-danger { background: linear-gradient(45deg, #dc3545, #e83e8c); color: white; }
.bg-gradient-secondary { background: linear-gradient(45deg, #6c757d, #5a6268); color: white; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Task Status Chart
    const statusCtx = document.getElementById('taskStatusChart').getContext('2d');
    const taskStatusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'In Progress', 'Completed'],
            datasets: [{
                data: [{{ $pendingTasks }}, {{ $inProgressTasks }}, {{ $completedTasks }}],
                backgroundColor: [
                    '#ffc107',
                    '#007bff',
                    '#28a745'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = {{ $totalTasks }};
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });

    // Priority Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    const priorityChart = new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['Low', 'Medium', 'High', 'Urgent'],
            datasets: [{
                label: 'Tasks by Priority',
                data: [
                    {{ $priorityStats['Low'] ?? 0 }},
                    {{ $priorityStats['Medium'] ?? 0 }},
                    {{ $priorityStats['High'] ?? 0 }},
                    {{ $priorityStats['Urgent'] ?? 0 }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Task action buttons
    $('.start-btn').click(function() {
        const taskId = $(this).data('id');
        if (confirm('Are you sure you want to start this task?')) {
            $.ajax({
                url: "{{ url('housekeeping') }}/" + taskId + "/start",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });

    $('.complete-btn').click(function() {
        const taskId = $(this).data('id');
        const minutes = prompt('Enter actual time taken (minutes):');
        if (minutes && minutes > 0) {
            $.ajax({
                url: "{{ url('housekeeping') }}/" + taskId + "/complete",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    actual_minutes: minutes
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });
});
</script>
@endpush
