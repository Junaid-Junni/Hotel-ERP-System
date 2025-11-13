@extends('layouts.app')

@section('title', 'Schedule Housekeeping Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">
                        <i class="fas fa-broom"></i> Schedule New Cleaning Task
                    </h3>
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-light btn-sm float-right">
                        <i class="fas fa-arrow-left"></i> Back to Tasks
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('housekeeping.store') }}" method="POST" id="housekeepingForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room *</label>
                                    <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                                {{ $room->RoomNo }} - {{ $room->Type }} ({{ $room->Status }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('room_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="employee_id">Housekeeper *</label>
                                    <select class="form-control @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                                        <option value="">Select Housekeeper</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="cleaning_date">Cleaning Date *</label>
                                    <input type="date" class="form-control @error('cleaning_date') is-invalid @enderror"
                                           id="cleaning_date" name="cleaning_date"
                                           value="{{ old('cleaning_date', date('Y-m-d')) }}" required>
                                    @error('cleaning_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="cleaning_time">Cleaning Time *</label>
                                    <input type="time" class="form-control @error('cleaning_time') is-invalid @enderror"
                                           id="cleaning_time" name="cleaning_time"
                                           value="{{ old('cleaning_time', '09:00') }}" required>
                                    @error('cleaning_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cleaning_type">Cleaning Type *</label>
                                    <select class="form-control @error('cleaning_type') is-invalid @enderror" id="cleaning_type" name="cleaning_type" required>
                                        <option value="">Select Type</option>
                                        <option value="Daily" {{ old('cleaning_type') == 'Daily' ? 'selected' : '' }}>Daily Cleaning</option>
                                        <option value="Checkout" {{ old('cleaning_type') == 'Checkout' ? 'selected' : '' }}>Check-out Cleaning</option>
                                        <option value="Deep" {{ old('cleaning_type') == 'Deep' ? 'selected' : '' }}>Deep Cleaning</option>
                                        <option value="Maintenance" {{ old('cleaning_type') == 'Maintenance' ? 'selected' : '' }}>Maintenance Cleaning</option>
                                    </select>
                                    @error('cleaning_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="duration_minutes">Duration (minutes) *</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                           id="duration_minutes" name="duration_minutes"
                                           value="{{ old('duration_minutes', 30) }}" min="15" max="480" required>
                                    <small class="form-text text-muted">Estimated cleaning duration in minutes</small>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Cleaning Tasks *</label>
                                    <div class="cleaning-tasks-container">
                                        @foreach($cleaningTasks as $task)
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input task-checkbox"
                                                   id="task_{{ $loop->index }}" name="tasks[]" value="{{ $task }}"
                                                   {{ in_array($task, old('tasks', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="task_{{ $loop->index }}">
                                                {{ $task }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                    @error('tasks')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="special_instructions">Special Instructions</label>
                                    <textarea class="form-control @error('special_instructions') is-invalid @enderror"
                                              id="special_instructions" name="special_instructions" rows="3"
                                              placeholder="Any special instructions for the housekeeper...">{{ old('special_instructions') }}</textarea>
                                    @error('special_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="2"
                                              placeholder="Additional notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-calendar-plus"></i> Schedule Cleaning
                            </button>
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Cleaning Guidelines
                    </h3>
                </div>
                <div class="card-body">
                    <h6>Cleaning Types:</h6>
                    <ul class="list-unstyled">
                        <li><span class="badge badge-info">Daily</span> - Regular room cleaning</li>
                        <li><span class="badge badge-primary">Checkout</span> - After guest departure</li>
                        <li><span class="badge badge-warning">Deep</span> - Thorough deep cleaning</li>
                        <li><span class="badge badge-danger">Maintenance</span> - Repair-related cleaning</li>
                    </ul>

                    <h6 class="mt-3">Duration Guidelines:</h6>
                    <ul class="list-unstyled">
                        <li>Daily: 30-45 minutes</li>
                        <li>Checkout: 45-60 minutes</li>
                        <li>Deep: 60-120 minutes</li>
                        <li>Maintenance: 30-90 minutes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('housekeepingForm');
    form.addEventListener('submit', function(e) {
        const checkedTasks = document.querySelectorAll('.task-checkbox:checked');
        if (checkedTasks.length === 0) {
            e.preventDefault();
            alert('Please select at least one cleaning task.');
            return false;
        }
    });

    // Set minimum date to today
    const cleaningDate = document.getElementById('cleaning_date');
    cleaningDate.min = new Date().toISOString().split('T')[0];
});
</script>
@endpush
