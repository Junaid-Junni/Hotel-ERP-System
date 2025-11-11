{{-- resources/views/housekeeping/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Housekeeping Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Housekeeping Management</h3>
                        <div>
                            <a href="{{ route('housekeeping.create') }}" class="btn btn-success">
                                <i class="fa fa-plus"></i> New Task
                            </a>
                            <a href="{{ route('housekeeping.dashboard') }}" class="btn btn-info">
                                <i class="fa fa-dashboard"></i> Dashboard
                            </a>
                            <a href="{{ route('housekeeping.calendar') }}" class="btn btn-primary">
                                <i class="fa fa-calendar"></i> Calendar
                            </a>
                            <button class="btn btn-danger" id="deleteAllBtn">
                                <i class="fa fa-trash"></i> Delete All
                            </button>
                            <a href="{{ route('housekeeping.trash.index') }}" class="btn btn-secondary">
                                <i class="fa fa-trash-alt"></i> View Trash
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($tasks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Room</th>
                                        <th>Task Type</th>
                                        <th>Assigned To</th>
                                        <th>Scheduled Date</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Estimated Time</th>
                                        <th>Actual Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tasks as $index => $task)
                                    <tr class="{{ $task->is_overdue ? 'table-danger' : '' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $task->room->RoomNo }} ({{ $task->room->Type }})</td>
                                        <td>{{ $task->task_type }}</td>
                                        <td>{{ $task->assignedEmployee->first_name }} {{ $task->assignedEmployee->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($task->scheduled_date)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <span class="badge
                                                @if($task->priority == 'Low') bg-success
                                                @elseif($task->priority == 'Medium') bg-info
                                                @elseif($task->priority == 'High') bg-warning
                                                @elseif($task->priority == 'Urgent') bg-danger
                                                @else bg-secondary
                                                @endif">
                                                {{ $task->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($task->status == 'Pending') {{ $task->is_overdue ? 'bg-danger' : 'bg-warning' }}
                                                @elseif($task->status == 'In Progress') bg-primary
                                                @elseif($task->status == 'Completed') bg-success
                                                @elseif($task->status == 'Cancelled') bg-secondary
                                                @else bg-secondary
                                                @endif">
                                                {{ $task->status }}
                                            </span>
                                            @if($task->is_overdue)
                                                <span class="badge bg-danger">Overdue</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->estimated_minutes ? $task->estimated_minutes . ' mins' : 'N/A' }}</td>
                                        <td>{{ $task->actual_minutes ? $task->actual_minutes . ' mins' : 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info view-btn" data-id="{{ $task->id }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <a href="{{ route('housekeeping.edit', $task->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>

                                                @if($task->status == 'Pending')
                                                    <button class="btn btn-sm btn-success start-btn" data-id="{{ $task->id }}">
                                                        <i class="fa fa-play"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger cancel-btn" data-id="{{ $task->id }}">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                @elseif($task->status == 'In Progress')
                                                    <button class="btn btn-sm btn-primary complete-btn" data-id="{{ $task->id }}">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $task->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5><i class="icon fa fa-info"></i> No Tasks Found!</h5>
                            <p>There are no housekeeping tasks yet. <a href="{{ route('housekeeping.create') }}">Create the first task</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this task?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete All</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete ALL tasks? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAll">Delete All</button>
            </div>
        </div>
    </div>
</div>

<!-- Start Task Modal -->
<div class="modal fade" id="startModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to start this task?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmStart">Start Task</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Task Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="actualMinutes">Actual Time Taken (minutes)</label>
                    <input type="number" class="form-control" id="actualMinutes" min="1" required>
                </div>
                <div class="form-group">
                    <label for="completionNotes">Completion Notes</label>
                    <textarea class="form-control" id="completionNotes" rows="3" placeholder="Any additional notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmComplete">Complete Task</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Task Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Task</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="cancellationReason">Cancellation Reason</label>
                    <textarea class="form-control" id="cancellationReason" rows="3" placeholder="Enter reason for cancellation..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Cancel Task</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/housekeeping.js') }}"></script>
@endpush
