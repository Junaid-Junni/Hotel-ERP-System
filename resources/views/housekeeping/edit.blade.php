{{-- resources/views/housekeeping/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Housekeeping Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Edit Task: Room {{ $task->room->RoomNo }} - {{ $task->task_type }}</h3>
                        <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('housekeeping.update', $task->id) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room *</label>
                                    <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id', $task->room_id) == $room->id ? 'selected' : '' }}>
                                                Room {{ $room->RoomNo }} - {{ $room->Type }} ({{ $room->Floor }})
                                                @if($room->Status != 'Available')
                                                    - Currently {{ $room->Status }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="assigned_to">Assign To *</label>
                                    <select class="form-control @error('assigned_to') is-invalid @enderror" id="assigned_to" name="assigned_to" required>
                                        <option value="">Select Staff</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('assigned_to', $task->assigned_to) == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="task_type">Task Type *</label>
                                    <select class="form-control @error('task_type') is-invalid @enderror" id="task_type" name="task_type" required>
                                        <option value="">Select Task Type</option>
                                        <option value="Cleaning" {{ old('task_type', $task->task_type) == 'Cleaning' ? 'selected' : '' }}>Room Cleaning</option>
                                        <option value="Deep Cleaning" {{ old('task_type', $task->task_type) == 'Deep Cleaning' ? 'selected' : '' }}>Deep Cleaning</option>
                                        <option value="Maintenance" {{ old('task_type', $task->task_type) == 'Maintenance' ? 'selected' : '' }}>Maintenance Check</option>
                                        <option value="Linen Change" {{ old('task_type', $task->task_type) == 'Linen Change' ? 'selected' : '' }}>Linen Change</option>
                                        <option value="Restocking" {{ old('task_type', $task->task_type) == 'Restocking' ? 'selected' : '' }}>Restocking Supplies</option>
                                        <option value="Inspection" {{ old('task_type', $task->task_type) == 'Inspection' ? 'selected' : '' }}>Room Inspection</option>
                                        <option value="Other" {{ old('task_type', $task->task_type) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('task_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="Medium" {{ old('priority', $task->priority) == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="Low" {{ old('priority', $task->priority) == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="High" {{ old('priority', $task->priority) == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Urgent" {{ old('priority', $task->priority) == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                @php
                                    $scheduledDateTime = \Carbon\Carbon::parse($task->scheduled_date);
                                    $scheduledDate = $scheduledDateTime->format('Y-m-d');
                                    $scheduledTime = $scheduledDateTime->format('H:i');
                                @endphp

                                <div class="form-group">
                                    <label for="scheduled_date">Scheduled Date *</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror"
                                           id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', $scheduledDate) }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="scheduled_time">Scheduled Time *</label>
                                    <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror"
                                           id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time', $scheduledTime) }}" required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="estimated_minutes">Estimated Time (minutes)</label>
                                    <input type="number" class="form-control @error('estimated_minutes') is-invalid @enderror"
                                           id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" min="1">
                                    @error('estimated_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror>
                                </div>

                                <div class="form-group">
                                    <label>Current Status</label>
                                    <div>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Task Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Additional Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="3">{{ old('notes', $task->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Task
                                </button>
                                <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
