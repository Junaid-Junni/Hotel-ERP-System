@extends('layouts.app')

@section('title', 'Edit Housekeeping Task')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Edit Cleaning Task
                    </h3>
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-light btn-sm float-right">
                        <i class="fas fa-arrow-left"></i> Back to Tasks
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('housekeeping.update', $housekeeping->id) }}" method="POST" id="housekeepingForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room_id">Room *</label>
                                    <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                        <option value="">Select Room</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}"
                                                {{ old('room_id', $housekeeping->room_id) == $room->id ? 'selected' : '' }}>
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
                                                {{ old('employee_id', $housekeeping->employee_id) == $employee->id ? 'selected' : '' }}>
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
                                           value="{{ old('cleaning_date', $housekeeping->cleaning_date->format('Y-m-d')) }}" required>
                                    @error('cleaning_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="cleaning_time">Cleaning Time *</label>
                                    <input type="time" class="form-control @error('cleaning_time') is-invalid @enderror"
                                           id="cleaning_time" name="cleaning_time"
                                           value="{{ old('cleaning_time', $housekeeping->cleaning_time) }}" required>
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
                                        <option value="Daily" {{ old('cleaning_type', $housekeeping->cleaning_type) == 'Daily' ? 'selected' : '' }}>Daily Cleaning</option>
                                        <option value="Checkout" {{ old('cleaning_type', $housekeeping->cleaning_type) == 'Checkout' ? 'selected' : '' }}>Check-out Cleaning</option>
                                        <option value="Deep" {{ old('cleaning_type', $housekeeping->cleaning_type) == 'Deep' ? 'selected' : '' }}>Deep Cleaning</option>
                                        <option value="Maintenance" {{ old('cleaning_type', $housekeeping->cleaning_type) == 'Maintenance' ? 'selected' : '' }}>Maintenance Cleaning</option>
                                    </select>
                                    @error('cleaning_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="duration_minutes">Duration (minutes) *</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                           id="duration_minutes" name="duration_minutes"
                                           value="{{ old('duration_minutes', $housekeeping->duration_minutes) }}" min="15" max="480" required>
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
                                                   {{ in_array($task, old('tasks', $housekeeping->tasks ?? [])) ? 'checked' : '' }}>
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
                                              placeholder="Any special instructions for the housekeeper...">{{ old('special_instructions', $housekeeping->special_instructions) }}</textarea>
                                    @error('special_instructions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              id="notes" name="notes" rows="2"
                                              placeholder="Additional notes...">{{ old('notes', $housekeeping->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Task Status Information -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Task Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Current Status:</strong>
                                    <span class="badge badge-{{ $housekeeping->status_badge }} ml-2">
                                        {{ $housekeeping->status }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong> {{ $housekeeping->created_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                            @if($housekeeping->started_at)
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Started At:</strong> {{ $housekeeping->started_at->format('M d, Y h:i A') }}
                                </div>
                            </div>
                            @endif
                            @if($housekeeping->completed_at)
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Completed At:</strong> {{ $housekeeping->completed_at->format('M d, Y h:i A') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Quality Rating:</strong>
                                    @if($housekeeping->quality_rating)
                                        {{ $housekeeping->quality_rating }}/5
                                        {!! str_repeat('★', $housekeeping->quality_rating) . str_repeat('☆', 5 - $housekeeping->quality_rating) !!}
                                    @else
                                        Not rated
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Update Task
                            </button>
                            <a href="{{ route('housekeeping.index') }}" class="btn btn-secondary">Cancel</a>

                            @if($housekeeping->status == 'Scheduled')
                                <button type="button" class="btn btn-success ml-2" id="markInProgressBtn">
                                    <i class="fas fa-play"></i> Mark In Progress
                                </button>
                            @elseif($housekeeping->status == 'In Progress')
                                <button type="button" class="btn btn-success ml-2" id="markCompleteBtn">
                                    <i class="fas fa-check"></i> Mark Complete
                                </button>
                            @endif

                            @if(in_array($housekeeping->status, ['Scheduled', 'In Progress']))
                                <button type="button" class="btn btn-danger ml-2" id="cancelTaskBtn">
                                    <i class="fas fa-times"></i> Cancel Task
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Task Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">
                        <i class="fas fa-tasks"></i> Task Summary
                    </h3>
                </div>
                <div class="card-body">
                    <h6>Room Details:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Room:</strong> {{ $housekeeping->room->RoomNo }}</li>
                        <li><strong>Type:</strong> {{ $housekeeping->room->Type }}</li>
                        <li><strong>Floor:</strong> {{ $housekeeping->room->Floor }}</li>
                        <li><strong>Status:</strong> <span class="badge badge-secondary">{{ $housekeeping->room->Status }}</span></li>
                    </ul>

                    <h6 class="mt-3">Housekeeper:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Name:</strong> {{ $housekeeping->employee->first_name }} {{ $housekeeping->employee->last_name }}</li>
                        <li><strong>ID:</strong> {{ $housekeeping->employee->employee_id }}</li>
                        <li><strong>Position:</strong> {{ $housekeeping->employee->position }}</li>
                    </ul>

                    <h6 class="mt-3">Cleaning Guidelines:</h6>
                    <ul class="list-unstyled">
                        <li><span class="badge badge-info">Daily</span> - Regular room cleaning</li>
                        <li><span class="badge badge-primary">Checkout</span> - After guest departure</li>
                        <li><span class="badge badge-warning">Deep</span> - Thorough deep cleaning</li>
                        <li><span class="badge badge-danger">Maintenance</span> - Repair-related cleaning</li>
                    </ul>
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
                            <i class="fas fa-plus"></i> New Task
                        </a>
                        <a href="{{ route('housekeeping.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> All Tasks
                        </a>
                        <a href="{{ route('housekeeping.dashboard') }}" class="btn btn-info">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </div>
                </div>
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

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this housekeeping task?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const taskId = {{ $housekeeping->id }};

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

    // Mark In Progress
    const markInProgressBtn = document.getElementById('markInProgressBtn');
    if (markInProgressBtn) {
        markInProgressBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to mark this task as In Progress?')) {
                fetch(`/housekeeping/${taskId}/in-progress`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating task status.');
                });
            }
        });
    }

    // Mark Complete
    const markCompleteBtn = document.getElementById('markCompleteBtn');
    if (markCompleteBtn) {
        markCompleteBtn.addEventListener('click', function() {
            $('#completeModal').modal('show');
        });
    }

    // Cancel Task
    const cancelTaskBtn = document.getElementById('cancelTaskBtn');
    if (cancelTaskBtn) {
        cancelTaskBtn.addEventListener('click', function() {
            $('#cancelModal').modal('show');
        });
    }

    // Confirm Complete
    const confirmCompleteBtn = document.getElementById('confirmComplete');
    if (confirmCompleteBtn) {
        confirmCompleteBtn.addEventListener('click', function() {
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

            fetch(`/housekeeping/${taskId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#completeModal').modal('hide');
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error completing task.');
            });
        });
    }

    // Confirm Cancel
    const confirmCancelBtn = document.getElementById('confirmCancel');
    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', function() {
            fetch(`/housekeeping/${taskId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#cancelModal').modal('hide');
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling task.');
            });
        });
    }
});
</script>
@endpush
