{{-- resources/views/housekeeping/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Housekeeping Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Create New Housekeeping Task</h3>
                        <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Tasks
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('housekeeping.store') }}" method="POST" id="createForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room *</label>
                                    <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
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
                                            <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
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
                                        <option value="Cleaning" {{ old('task_type') == 'Cleaning' ? 'selected' : '' }}>Room Cleaning</option>
                                        <option value="Deep Cleaning" {{ old('task_type') == 'Deep Cleaning' ? 'selected' : '' }}>Deep Cleaning</option>
                                        <option value="Maintenance" {{ old('task_type') == 'Maintenance' ? 'selected' : '' }}>Maintenance Check</option>
                                        <option value="Linen Change" {{ old('task_type') == 'Linen Change' ? 'selected' : '' }}>Linen Change</option>
                                        <option value="Restocking" {{ old('task_type') == 'Restocking' ? 'selected' : '' }}>Restocking Supplies</option>
                                        <option value="Inspection" {{ old('task_type') == 'Inspection' ? 'selected' : '' }}>Room Inspection</option>
                                        <option value="Other" {{ old('task_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('task_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                        <option value="Urgent" {{ old('priority') == 'Urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scheduled_date">Scheduled Date *</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror"
                                           id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="scheduled_time">Scheduled Time *</label>
                                    <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror"
                                           id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time', '09:00') }}" required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="estimated_minutes">Estimated Time (minutes)</label>
                                    <input type="number" class="form-control @error('estimated_minutes') is-invalid @enderror"
                                           id="estimated_minutes" name="estimated_minutes" value="{{ old('estimated_minutes', 60) }}" min="1">
                                    @error('estimated_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Estimated time to complete the task in minutes</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Task Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3" placeholder="Describe the task details...">{{ old('description') }}</textarea>
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
                                              id="notes" name="notes" rows="3" placeholder="Any additional instructions or notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Create Task
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

@push('scripts')
<script>
$(document).ready(function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    $('#scheduled_date').attr('min', today);
});
</script>
@endpush
@endsection
